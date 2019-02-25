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
// Have monolog output logs where we can see them from docker.
$settings['container_yamls'][] = __DIR__ . '/monolog-stdout.services.yml';

$config_directories = array(
  CONFIG_SYNC_DIRECTORY => __DIR__ . '/../../../configuration/drupal',
);

$settings['install_profile'] = 'standard';

// Allow *.docker and *.ngrok.io domains
$settings['trusted_host_patterns'] = ['^.*\.docker$', '.*\.ngrok\.io$', 'localhost', '(.*\.)?dds\.dk'];

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

// Display an indicator of breakpoint sizes.
$settings['ddsdk_show_sizebar'] = TRUE;

// Set up stage file proxy.
$config['stage_file_proxy.settings']['origin'] = 'https://dds.dk/';

// Solr config override.
$config['search_api.server.solr']['backend_config']['connector_config']['host'] = 'solr';
$config['search_api.server.solr']['backend_config']['connector_config']['path'] = '/solr';
$config['search_api.server.solr']['backend_config']['connector_config']['port'] = '8983';
$config['search_api.server.solr']['backend_config']['connector_config']['core'] = 'ddsmainindex';

// Google analytics test.
$config['google_analytics.settings']['account'] = 'UA-7162673-26';

// Setup feature flags.
// The following is used in html.html.twig and is not a mistake but an example
// of how to use the flag.
// $config['feature_flags']['hotjar20171127'] = TRUE;
