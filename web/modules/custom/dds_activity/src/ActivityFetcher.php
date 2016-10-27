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
  const AKTDB_IMAGE_STORAGE_PREFIX = 'https://aktiviteter.dds.dk/sites/default/files/aktivws';

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
   */
  public function loadActivity($activity_id) {
    if ($activity_id > 0) {
      $client = \Drupal::httpClient();
      try {
        $response = $client->request(
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
            watchdog_exception('dds_activity', $e);
          }
        }

      }
      catch (RequestException $e) {
        watchdog_exception('dds_activity', $e);
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
    $image_data = $this->get('image_1');

    if (!empty($image_data) && is_object($image_data) && isset($image_data->src)) {
      return $image_data->src;
    }
    else {
      return NULL;
    }
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

}
