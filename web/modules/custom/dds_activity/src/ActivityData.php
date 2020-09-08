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
   * @var int|null
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

}
