<?php

namespace Drupal\dds_activity;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Utility class for fetching activities from the activity database.
 */
class ActivityFetcher {
  protected $activityData;
  const AKTDB_WS_ENDPOINT = 'https://aktiviteterws.dds.dk';
  const AKTDB_VIEW_PREFIX = 'https://aktiviteter.dds.dk/aktivitet';
  const AKTDB_IMAGE_STORAGE_PREFIX = 'https://aktiviteter.dds.dk/sites/default/files/aktivws';
  protected $client;

  /**
   * ActivityFetcher constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   Initialized http client.
   */
  public function __construct(ClientInterface $client) {
    $this->client = $client;
  }


  /**
   * Loads an activity by its id.
   *
   * @param int $activity_id
   *   The internal id of the activity.
   *
   * @return bool
   *   Id of the activity.
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function loadActivity($activity_id) {
    if ($activity_id > 0) {
      try {
        $response = $this->client->request(
          'GET',
          self::AKTDB_WS_ENDPOINT . '/activities/' . $activity_id
        );
        $status = $response->getStatusCode();


        if ($status === 200) {
          try {
            $payload = $response->getBody()->getContents();
            $this->activityData = json_decode($payload);
            return TRUE;
          }
          catch (\RuntimeException $e) {
//            watchdog_exception('dds_activity', $e);
          }
        }

      }
      catch (RequestException $e) {
//        watchdog_exception('dds_activity', $e);
      }
    }
    return FALSE;
  }

  /**
   * Retrieve a field from the activity data object.
   *
   * @param string $key
   *   Name of the field to retrieve.
   *
   * @return mixed|null
   *   The field-data or NULL if the field does not exist.
   */
  protected function get($key) {
    if (!empty($this->activityData) && property_exists($this->activityData, $key)) {
      return $this->activityData->$key;
    }
    else {
      return NULL;
    }
  }

  /**
   * Gets the title of the activity.
   *
   * @return string|null
   *   The title if present, if not NULL.
   */
  public function getTitle() {
    return $this->get('title');
  }

  /**
   * Gets the full url for an activity's image.
   *
   * @return string|null
   *   The full URL to the image attached to the activity or NULL if
   *   the activity does not have an image.
   */
  public function getImageUrl() {
    $filename = $this->getImageFilename();

    if (!empty($filename)) {
      return self::AKTDB_IMAGE_STORAGE_PREFIX . '/' . $filename;
    }
    else {
      return NULL;
    }
  }

  /**
   * Gets the name of the image-file associated with the activity.
   *
   * @return string|null
   *   The filename or NULL if the image could not be found.
   */
  public function getImageFilename() {
    return $this->getMediaSource('image_1');
  }

  /**
   * Gets a short description of the activity.
   *
   * @return string|null
   *   The description or null if it could not be found.
   */
  public function getDescription() {
    return $this->get('body');
  }

  /**
   * @return string|null
   */
  public function getInstructions() {
    return $this->get('instructions');
  }

  /**
   * @return string|null
   */
  public function getMaterials() {
    return $this->get('materials');
  }

  /**
   * @return array|null
   */
  public function getAges() {
    return $this->getMultipleIds('age');
  }

  /**
   * @return array|null
   */
  public function getTypes() {
    return $this->getMultipleIds('type');
  }

  /**
   * @return array|null
   */
  public function getDuration() {
    $durations = $this->getMultipleIds('duration');

    return $durations && isset($durations[0]) ? $durations[0] : NULL;
  }

  /**
   * @return string|null
   */
  public function getQuestions() {
    $questions = [];
    for ($i = 1; $i <= 5; $i++) {
      if ($question = $this->get('question_' . $i)) {
        $questions[] = $question;
      }
    }

    return !empty($questions) ? implode(PHP_EOL, $questions) : NULL;
  }

  /**
   * @return array|null
   */
  public function getSecondaryImageUrls() {
    $images = [];
    for ($i = 2; $i <= 6; $i++) {
      if ($filename = $this->getMediaSource('image_' . $i)) {
        $images[] = self::AKTDB_IMAGE_STORAGE_PREFIX . '/' . $filename;
      }
    }

    return !empty($images) ? $images : NULL;
  }

  public function getYoutube() {
    return $this->getMediaSource('youtube');
  }
  /**
   * @return array|null
   */
  protected function getMultipleIds($field) {
    $raw_data = $this->get($field);
    if ($raw_data) {
      $data = (array) $raw_data;
      return count($data) ? array_keys($data) : NULL;
    }

    return NULL;
  }

  /**
   * @param string $field
   * @return string|null
   */
  protected function getMediaSource($field) {
    $data = $this->get($field);

    if (!empty($data) && is_object($data) && isset($data->src)) {
      return $data->src;
    }
    else {
      return NULL;
    }
  }

}
