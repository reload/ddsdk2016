<?php

namespace Drupal\dds_activity;

use Drupal;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class BatchService.
 */
class BatchImportService {

  /**
   * @var MessengerInterface
   */
  private $messenger;

  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }
  /**
   * Batch process callback.
   *
   * @param ActivityData $activity
   *   Id of the batch.
   * @param object $context
   *   Context for operations.
   * @throws Drupal\Core\Entity\EntityStorageException
   */
  public function importActivity(ActivityData $activity, &$context): void {

    $query = Drupal::service('entity.query')
      ->get('node')
      ->condition('field_activity_id', $activity->id);
    $query_result = $query->execute();

    // TODO: Remove debugging clause id === 8.
    if (!empty($query_result) && $activity->id === 8) {
      $nid = array_values($query_result)[0];
      $node = Node::load($nid);
      (new ActivityHydrater($activity))
        ->hydrateNode($node)
        ->save();

    }
    $context['results']++;
    $context['message'] = t('Processing Activity: "@title"', ['@title' => $activity->title]);
  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array $results
   *   Array of results for post processing.
   * @param array $operations
   *   Array of operations.
   */
  public function importActivityFinished($success, $results, array $operations): void {
    if ($success) {
      $this->messenger->addMessage(t('@count results processed.', ['@count' => $results]));
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $this->messenger->addMessage(
        t('An error occurred while processing @operation with arguments : @args',
          [
            '@operation' => $error_operation[0],
            '@args' => print_r($error_operation[0], TRUE),
          ]
        )
      );
    }
  }

  /**
   * @param ActivityData $activity
   * @param Node $node
   * @throws EntityStorageException
   */
  protected static function populateImage(ActivityData $activity, Node &$node): void {
    // Then fetch the image and attach it to the node.
    // First off, prepare the directory where we're going to put the image.
    $file_destination_dir = 'public://aktivws/' . $activity->id;
    if (!\Drupal::service('file_system')->prepareDirectory($file_destination_dir, FileSystemInterface::CREATE_DIRECTORY)) {
//      $this->logger->error('Could not prepare destination directory @dir', ['@dir' => $file_destination_dir]);
      return;
    }

    // The prepare the destination path and download the file.
    $file_destination = $file_destination_dir . '/' . $activity->mainImageFilename;

    /** @var \Drupal\file\FileInterface $file */
    $file = system_retrieve_file($activity->mainImageUrl, $file_destination, TRUE, FILE_EXISTS_REPLACE);
    if ($file === FALSE) {
//      $this->logger->error(
//        'Could download activity image from @url to @destination',
//        [
//          '@url' => $activity->mainImageUrl,
//          '@destination' => $file_destination,
//        ]
//      );
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
        'alt' => $activity->title,
      ],
    ]);

    try {
      $image_media->save();
    }
    catch (EntityStorageException $e) {
      watchdog_exception('dds_activity', $e);
      return;
    }

    // If the activity already have an image, overwrite the reference and then
    // delete the media.
    if (!empty($node->field_main_media->target_id)) {
      // Get the current id.
      $media_to_be_deleted = $node->field_main_media->target_id;
      // Overwrite the current reference.
      $node->field_main_media->target_id = $image_media->id();
      // Then delete the old media if we can load it.
      $old_media = Media::load($media_to_be_deleted);
      if (!empty($old_media)) {
        $old_media->delete();
      }
    }
    else {
      // The activity does not have an image - add it.
      $node->field_main_media->appendItem(['target_id' => $image_media->id()]);
    }
  }

}
