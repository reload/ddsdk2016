<?php
// Production facebook app id.
$config['ddsdk']['facebook']['appid'] = '117491802579';

// Production Google Analytics.
$config['google_analytics.settings']['account'] = 'UA-7162673-24';

// Raven/Sentry error logging.
$config['raven.settings']['client_key'] = 'https://286d4190d96d4bfd91634ed7ef06d353@o813103.ingest.sentry.io/5908277';
$config['raven.settings']['public_dsn'] = 'https://286d4190d96d4bfd91634ed7ef06d353@o813103.ingest.sentry.io/5908277';

$config['ddsdk']['enable_linkedin_script'] = TRUE;

// Production tracking scripts, overriding docker.settings.php.
$config['ddsdk']['plausible_domain'] = 'dds.dk';