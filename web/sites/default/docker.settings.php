<?php

/**
 * @file
 * Settings-file for Docker development environments.
 */

$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => 'db',
  'username' => 'db',
  'password' => 'db',
  'host' => 'db',
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['hash_salt'] = 'hardcodedsaltshouldneverbeusedoutsidedocker';
$settings['update_free_access'] = FALSE;
$settings['container_yamls'][] = __DIR__ . '/docker.services.yml';

$config_directories = array(
  CONFIG_SYNC_DIRECTORY => __DIR__ . '/../../../configuration/drupal',
);

$settings['install_profile'] = 'standard';

// Allow *.docker and *.ngrok.io domains
$settings['trusted_host_patterns'] = ['^.*\.docker$', '.*\.ngrok\.io$', 'localhost'];

// Assertions.
assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();

// Show all error messages, with backtrace information.
$config['system.logging']['error_level'] = 'verbose';

// Disable CSS and JS aggregation.
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

// Disable the render cache (this includes the page cache).
$settings['cache']['bins']['render'] = 'cache.backend.null';

// Disable Dynamic Page Cache.
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

// Allow test modules and themes to be installed.
$settings['extension_discovery_scan_tests'] = TRUE;

// Enable access to rebuild.php.
$settings['rebuild_access'] = TRUE;

// Set up stage file proxy.
$config['stage_file_proxy.settings']['origin'] = 'http://develop-sr3snxi-55isd6w54kg6s.eu.platform.sh/';

// Solr config override.
$config['search_api.server.solr']['backend_config']['host'] = 'solr';
$config['search_api.server.solr']['backend_config']['path'] = '/solr';
