<?php

/**
 * @file
 * Provide tokens related to the forward module.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function dds_article_token_info() {
  $data['types'] = [
    'dds_article' => [
      'name' => t('DDS Article'),
      'description' => t('Tokens related to dds_article module.'),
    ],
  ];
  $data['tokens'] = [
    'dds_article' => [
      'type' => [
        'name' => t('Type'),
        'description' => t('Type of content to be shared: "article" or "file"'),
      ],
    ],
  ];

  return $data;
}

/**
 * Implements hook_tokens().
 */
function dds_article_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  // Bail out if the token type is not dds_article or if the forward module has
  // not added the entity to forward.
  if (($type !== 'dds_article') ||
    empty($data['forward']) ||
    empty($data['forward']['entity'])) {
    return [];
  }

  // Lookup the entity type of the entity to forward.
  $entity_type = $data['forward']['entity']->bundle();
  $replacements = [];

  foreach ($tokens as $name => $original) {
    // If the token is not the `dds_article:type` token proceed to next one.
    if ($name !== 'type') {
      continue;
    }

    switch ($entity_type) {
      case 'download':
        $replacements[$original] = t('file');
        break;

      default:
        $replacements[$original] = t('article');
        break;
    }
  }

  return $replacements;
}
