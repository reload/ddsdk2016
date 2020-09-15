<?php

namespace Drupal\dds_activity;

use DOMDocument;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystem;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\TypedData\Exception\ReadOnlyException;
use Drupal\file\FileInterface;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Psr\Log\LoggerInterface;

class ActivityHydrater {

  use StringTranslationTrait;

  /**
   * Mapping of age target group.
   * [(previous id) => (Badge target group id)]
   */
  const AGE_MAP = [
    // Mikrobe => Familiespejder.
    1 => 29,
    // Mikro => Mikro.
    2 => 30,
    // Mini => Mini.
    3 => 31,
    // Junior => Junior.
    4 => 32,
    // Trop => Trop.
    5 => 33,
    // Senior => Senior.
    6 => 34,
    // Leder  => Senior.
    7 => 34,
  ];

  /**
   * Mapping of activity type.
   * [(previous id) => (new activity type term name)]
   */
  const TYPE_MAP = [
    1 => 'Pioner og tovværk',
    2 => 'Orientering',
    3 => 'Klar dig selv',
    4 => 'Samarbejdsøvelser',
    5 => 'Sundhed',
    6 => 'Kommunikation',
    7 => 'Vandaktiviteter',
    8 => 'Natur og friluftsliv',
    9 => 'Madlavning',
    10 => 'Leg',
    11 => 'Drama og Musik',
    12 => 'Klima og Miljø',
    13 => 'Kultur og samfund',
    14 => 'Kreativitet og håndelag',
    15 => 'Evaluering og refleksion',
    16 => 'Førstehjælp',
    17 => 'Sø og vand',
    18 => '<10 deltagere',
  ];

  /**
   * Mapping of activity duration.
   * [(previous id) => (new activity duration term name)]
   */
  const DURATION_MAP = [
    1 => '<15 minutter',
    2 => '15-30 minutter',
    3 => '30-60 minutter',
    4 => '60-90 minutter',
    5 => '90-120 minutter',
    6 => '120+ minutter',
  ];

  /**
   * @var ActivityData
   */
  private $activity;
  /**
   * @var LoggerInterface
   */
  private $logger;
  /**
   * @var MessengerInterface
   */
  private $messenger;
  /**
   * @var EntityTypeManagerInterface
   */
  private $entityTypeManager;
  /**
   * @var FileSystem
   */
  private $fileSystem;

  /**
   * ActivityHydrater constructor.
   * @param ActivityData $activityData
   * @param MessengerInterface $messenger
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param FileSystem $file_system
   */
  public function __construct(ActivityData $activityData, MessengerInterface $messenger, EntityTypeManagerInterface $entity_type_manager, FileSystem $file_system) {
    $this->activity = $activityData;
    $this->messenger = $messenger;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
  }

  /**
   * @param Node $node
   * @return Node
   * @throws EntityStorageException
   * @throws InvalidPluginDefinitionException
   * @throws PluginNotFoundException
   * @throws ReadOnlyException
   */
  public function hydrateNode(Node $node): Node {
    $node->set('title', $this->activity->title);
    $node->set('field_activity_id', $this->activity->id);
    $node->set('field_subtitle', $this->activity->description);

    $node->set(
      'field_instructions',
      ['value' => self::formatText($this->activity->instructions), 'format' => 'full_html']
    );
    $node->set(
      'field_materials',
      ['value' => self::formatText($this->activity->materials), 'format' => 'full_html']
    );
    $node->set(
      'field_questions',
      ['value' => self::formatText($this->activity->questions), 'format' => 'full_html']
    );

    $node->set('field_youtube', $this->activity->youTube);

    // We don't use getTermIds for this field because the terms already exist
    // and we just need to map old ids to new.
    $node->set('field_badge_target_group', array_unique(array_map(function (int $ageId) {
      return self::getDestinationAge($ageId) ?? FALSE;
    }, $this->activity->ages)));

    $node->set('field_duration', $this->getTermIds(
      'duration_interval',
      [self::getDestinationDurationTermName($this->activity->duration)]
    ));

    $node->set('field_activity_type', $this->getTermIds(
      'activity_type',
      self::getDestinationTypeTermNames($this->activity->types)
    ));

    // Main image.
    if ($this->activity->mainImageUrl) {
      // Clean slate with image references.
      if ($node->field_main_media->target_id) {
        $this->deleteImageMedia($node->field_main_media->target_id);
        $node->get('field_main_media')->setValue(NULL);
      }
      // Create new media and create reference to it.
      $this->populateImage($node, 'field_main_media', $this->activity->mainImageUrl);
    }

    // Sub images.
    if (!empty($this->activity->secondaryImages)) {
      // Delete old media.
      array_map(function($value) {
        $this->deleteImageMedia($value['target_id']);
      }, $node->get('field_gallery_image')->getValue() ?? []);

      // Clean slate with image references.
      $node->get('field_gallery_image')->setValue(NULL);
      // Create new media and create references to them.
      array_map(function($image_url) use ($node) {
        $this->populateImage($node, 'field_gallery_image', $image_url);
      }, $this->activity->secondaryImages);
    }

    return $node;
  }


  /**
   * @param string $vid
   * @param array $term_names
   * @return array
   * @throws EntityStorageException
   * @throws InvalidPluginDefinitionException
   * @throws PluginNotFoundException
   */
  protected function getTermIds(string $vid, array $term_names): array {
    return array_map(function ($term_name) use ($vid) {
      $term_query = $this->entityTypeManager->getStorage('taxonomy_term')
        ->getQuery()
        ->condition('vid', $vid)
        ->condition('name', $term_name);
      $term_query_result = $term_query->execute();

      // Create term if it does not exist yet and return id.
      if (empty($term_query_result)) {
        $term = Term::create([
          'vid' => $vid,
          'name' => $term_name,
        ]);
        $term->save();
        return $term->id();
      }
      // Return id of matched term.
      else {
        return current($term_query_result);
      }
    }, $term_names);
  }

  /**
   * @param int $ageId
   * @return int|null
   */
  protected static function getDestinationAge(int $ageId): ?int {
    return self::getDestinationId($ageId, self::AGE_MAP);
  }

  /**
   * @param array $typeIds
   * @return array
   */
  protected static function getDestinationTypeTermNames(array $typeIds): array {
    return array_map(function ($typeId) {
      return self::getDestinationTermName($typeId, self::TYPE_MAP) ?? FALSE;
    }, $typeIds);
  }

  /**
   * @param int $durationId
   * @return string|null
   */
  protected static function getDestinationDurationTermName(int $durationId): ?string {
    return self::getDestinationTermName($durationId, self::DURATION_MAP);
  }

  /**
   * @param int $id
   * @param array $map
   * @return int|null
   */
  protected static function getDestinationId(int $id, array $map): ?int {
    return $map[$id] ?? NULL;
  }

  /**
   * @param int $id
   * @param array $map
   * @return string|null
   */
  protected static function getDestinationTermName(int $id, array $map): ?string {
    return $map[$id] ?? NULL;
  }

  /**
   * @param Node $node
   * @param string $field_name
   * @param string $image_url
   */
  protected function populateImage(Node &$node, string $field_name, string $image_url): void {
    // Then fetch the image and attach it to the node.
    // First off, prepare the directory where we're going to put the image.
    $file_destination_dir = 'public://aktivws/' . $this->activity->id;
    $filename = basename($image_url);
    if (!$this->fileSystem->prepareDirectory($file_destination_dir, FileSystemInterface::CREATE_DIRECTORY)) {
      $this->messenger->addError($this->t('Could not prepare destination directory @dir', ['@dir' => $file_destination_dir]));
      return;
    }

    // The prepare the destination path and download the file.
    $file_destination = $file_destination_dir . '/' . $filename;

    /** @var FileInterface $file */
    $file = system_retrieve_file($image_url, $file_destination, TRUE, FileSystemInterface::EXISTS_REPLACE);
    if ($file === FALSE) {
      $this->messenger->addError($this->t(
        'Could not download activity image from @url to @destination',
        [
          '@url' => $image_url,
          '@destination' => $file_destination,
        ]
      ));
      return;
    }
    // TODO: we don't do anything to handle file_usage - out of the box we
    // currently register two usages - probably core and media.
    $image_media = Media::create([
      'bundle' => 'image',
      'uid' => '1',
      'status' => TRUE,
      'field_image' => [
        'target_id' => $file->id(),
        'alt' => $this->activity->title,
      ],
    ]);

    try {
      $image_media->save();
    }
    catch (EntityStorageException $e) {
      watchdog_exception('dds_activity', $e);
      return;
    }

    $node->{$field_name}->appendItem(['target_id' => $image_media->id()]);
  }

  /**
   * @param int $id
   * @throws EntityStorageException
   */
  protected function deleteImageMedia(int $id): void {
    $media = Media::load($id);
    if (!empty($media)) {
      $media->delete();
    }
  }

  /**
   * @param $text
   * @return string
   */
  protected static function formatText($text): string {
    if (empty($text)) {
      return '';
    }

    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8" ?>' . $text);

    $elements = $dom->getElementsByTagName("div");
    for ($i = $elements->length - 1; $i >= 0; $i--) {
      $node_old = $elements->item($i);
      $node_new = $dom->createElement("p", $node_old->nodeValue);
      $node_old->parentNode->replaceChild($node_new, $node_old);
    }

    $body = $dom->saveHTML($dom->getElementsByTagName('body'  )->item(0));

    return preg_replace(
      ['/^<body>(.*)<\/body>$/s', '/<p>[^<A-Za-z]+<\/p>\\n/', '/^\\n(.*)/'],
      ['$1', '', '$1'],
      $body
    );
  }

}
