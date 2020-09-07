<?php


namespace Drupal\dds_activity;


class ActivityData
{
  public $id;
  /**
   * @var string|null
   */
  public $title;
  /**
   * @var array|null
   */
  public $ages;
  /**
   * @var string|null
   */
  public $description;
  /**
   * @var array|null
   */
  public $duration;
  /**
   * @var string|null
   */
  public $mainImageUrl;
  /**
   * @var array|null
   */
  public $secondaryImages;
  /**
   * @var string|null
   */
  public $instructions;
  /**
   * @var string|null
   */
  public $materials;
  /**
   * @var array|null
   */
  public $types;
  /**
   * @var string|null
   */
  public $youTube;
  /**
   * @var string|null
   */
  public $questions;
  /**
   * @var string|null
   */
  public $mainImageFilename;

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
    2 => 'Orientering => Something',
    3 => 'Klar dig selv',
    4 => 'Samarbejdsøvelser',
    5 => 'Sundhed',
    6 => 'Kommunikation',
    7 => 'Vandaktiviteter',
    8 => 'Natur og friluftsliv',
    9 => 'Madlavning => Something',
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
   * @param ActivityFetcher $activityFetcher
   * @param int $id
   * @return static
   */
  public static function constructFromActivityFetcher(ActivityFetcher $activityFetcher, int $id): self {
    $activity = new static();
    $activity->id = $id;
    $activity->title = $activityFetcher->getTitle();
    $activity->ages = $activityFetcher->getAges();
    $activity->description = $activityFetcher->getDescription();
    $activity->duration = $activityFetcher->getDuration();
    $activity->mainImageUrl = $activityFetcher->getImageUrl();
    $activity->mainImageFilename = $activityFetcher->getImageFilename();
    $activity->secondaryImages = $activityFetcher->getSecondaryImageUrls();
    $activity->instructions = $activityFetcher->getInstructions();
    $activity->materials = $activityFetcher->getMaterials();
    $activity->types = $activityFetcher->getTypes();
    $activity->youTube = $activityFetcher->getYoutube();
    $activity->questions = $activityFetcher->getQuestions();

    return $activity;
  }

  /**
   * @param int $ageId
   * @return int|null
   */
  public static function getDestinationAge(int $ageId): ?int {
    return self::getDestinationId($ageId, self::AGE_MAP);
  }

  /**
   * @param int $type
   * @return int|null
   */
  public static function getDestinationType(int $type): ?int {
    return self::getDestinationId($type, self::TYPE_MAP);
  }

  public static function getDestinationDurationTermName(int $durationId): ?string {
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

  protected static function getDestinationTermName(int $id, array $map): ?string {
    return $map[$id] ?? NULL;
  }
}
