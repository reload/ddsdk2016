<?php

// Production Google Analytics.
$config['google_analytics.settings']['account'] = 'UA-7162673-24';

$config['ddsdk']['enable_linkedin_script'] = TRUE;

// Production tracking scripts, overriding docker.settings.php.
$config['plausible_tracking.settings']['domain'] = 'dds.dk';
// Blocked IPs. First one is DDS, second is Reload.
$config['plausible_tracking.settings']['blocked_ips'] = [
  // dds
  '152.115.51.222',
  // reload
  '109.202.128.38',
];
