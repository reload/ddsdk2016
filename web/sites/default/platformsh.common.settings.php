<?php
/**
 * @file
 * Platform.sh settings shared between environments.
 */

// Configure the database.
if (isset($_ENV['PLATFORM_RELATIONSHIPS'])) {
  $relationships = json_decode(base64_decode($_ENV['PLATFORM_RELATIONSHIPS']), TRUE);

  if (empty($databases['default']['default']) && !empty($relationships['database'])) {
    foreach ($relationships['database'] as $endpoint) {
      $database = [
        'driver' => $endpoint['scheme'],
        'database' => $endpoint['path'],
        'username' => $endpoint['username'],
        'password' => $endpoint['password'],
        'host' => $endpoint['host'],
        'port' => $endpoint['port'],
      ];

      if (!empty($endpoint['query']['compression'])) {
        $database['pdo'][PDO::MYSQL_ATTR_COMPRESS] = TRUE;
      }

      if (!empty($endpoint['query']['is_master'])) {
        $databases['default']['default'] = $database;
      }
      else {
        $databases['default']['slave'][] = $database;
      }
    }
  }

  // Import solr-configuration from platform if available.
  if (!empty($relationships['solr'])) {
    $endpoint = $relationships['solr'][0];

    // Override drupal configuration.
    $config['search_api.server.solr']['connector_config']['host'] = $endpoint['host'];
    $config['search_api.server.solr']['connector_config']['path'] = '/' . $endpoint['path'];
    $config['search_api.server.solr']['connector_config']['port'] = $endpoint['port'];
  }
}

// Configure private and temporary file paths.
if (isset($_ENV['PLATFORM_APP_DIR'])) {
  if (!isset($settings['file_private_path'])) {
    $settings['file_private_path'] = $_ENV['PLATFORM_APP_DIR'] . '/private';
  }
  if (!isset($config['system.file']['path']['temporary'])) {
    $config['system.file']['path']['temporary'] = $_ENV['PLATFORM_APP_DIR'] . '/tmp';
  }
}

// Set trusted hosts based on Platform.sh routes.
if (isset($_ENV['PLATFORM_ROUTES']) && !isset($settings['trusted_host_patterns'])) {
  $routes = json_decode(base64_decode($_ENV['PLATFORM_ROUTES']), TRUE);
  $settings['trusted_host_patterns'] = [];
  foreach ($routes as $url => $route) {
    $host = parse_url($url, PHP_URL_HOST);
    if ($host !== FALSE && $route['type'] == 'upstream' && $route['upstream'] == $_ENV['PLATFORM_APPLICATION_NAME']) {
      $settings['trusted_host_patterns'][] = '^' . preg_quote($host) . '$';
    }
  }
  $settings['trusted_host_patterns'] = array_unique($settings['trusted_host_patterns']);
}

// Set config directory.
$config_directories = array(
  CONFIG_SYNC_DIRECTORY => '/app/configuration/drupal',
);

// Set hash salt, used for transient hashes (eg. the reset-user token).
$settings['hash_salt'] = '9e62c5b69893a3d2816dced2cf45d4b4c0467dc1b451db87e717ea7298fa9fbe';
