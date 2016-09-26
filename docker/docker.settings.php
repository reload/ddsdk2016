<?php

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
$settings['container_yamls'][] = __DIR__ . '/services.yml';

$config_directories = array(
  CONFIG_SYNC_DIRECTORY => __DIR__ . '/../../../configuration/drupal',
);

$settings['install_profile'] = 'standard';

// Allow *.docker and *.ngrok.io domains
$settings['trusted_host_patterns'] = ['^.*\.docker$', '.*\.ngrok\.io$'];

// Assertions.
assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();

// Enable local development services.
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

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

/**
 * Include environment-specific local overrides
 */
if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}



