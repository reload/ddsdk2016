<?php

namespace Drupal\dds_activity;

use GuzzleHttp\Client;

/**
 * Sets up a guzzle-based client.
 */
class ClientFactory {

  /**
   * Return a configured Client object.
   */
  public function get() {
    $config = [
      'base_uri' => 'https://aktiviteterws.dds.dk/',
    ];

    return new Client($config);
  }

}
