<?php

namespace Drupal\dds_search\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Search form' block.
 *
 * @Block(
 *   id = "dds_search_form_block",
 *   admin_label = @Translation("DDS search form"),
 *   category = @Translation("DDS Search")
 * )
 */
class SearchFormBlock extends BlockBase {

  /**
   * @inheritdoc
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\dds_search\Form\SearchBlockForm');
  }
}
