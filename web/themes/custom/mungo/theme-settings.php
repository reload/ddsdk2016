<?php

/**
 * @file
 * Custom settings for the mungo theme.
 */

use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function mungo_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  // Add a default-images section.
  $form['badge_page_settings'] = array(
    '#type' => 'details',
    '#title' => t('Badge page settings'),
    '#open' => TRUE,
  );

  // Settings for the badge-overview-page.
  $form['badge_page_settings']['badge_cover_image'] = array(
    '#type' => 'container',
  );
  $form['badge_page_settings']['badge_cover_image']['badge_cover_image_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Path to the cover-image (use upload-field below)'),
    '#default_value' => theme_get_setting('badge_cover_image_path', 'mungo'),
  );
  $form['badge_page_settings']['badge_cover_image']['badge_cover_image'] = array(
    '#type' => 'file',
    '#title' => t('Upload image image'),
    '#maxlength' => 40,
    '#description' => t(
      "If you don't have direct file access to the server, use this field to upload your image."
    ),
    '#element_validate' => array('mungo_badge_cover_image_validate'),
  );

  $form['badge_page_settings']['badge_page_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title of the badge page'),
    '#default_value' => theme_get_setting('badge_page_title', 'mungo'),
  );

  $form['badge_page_settings']['badge_page_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title of the badge page'),
    '#default_value' => theme_get_setting('badge_page_title', 'mungo'),
  );

  $form['badge_page_settings']['badge_page_description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description for the badge page'),
    '#default_value' => theme_get_setting('badge_page_description', 'mungo'),
  );
}

/**
 * Finalizes upload of the image to use on the badge-overview page.
 *
 * @param array $element
 *   The form-element to be validated.
 * @param FormState $form_state
 *   Current state of the form.
 */
function mungo_badge_cover_image_validate(array $element, FormState &$form_state) {
  $validators = array('file_validate_is_image' => array());

  /** @var Drupal\file\Entity\File $file */
  $file = file_save_upload('badge_cover_image', $validators, "public://", NULL, FILE_EXISTS_REPLACE);
  if (!empty($file)) {
    $uri = $file[0]->getFileUri();
    $form_state->setValue('badge_cover_image_path', $uri);
  }
}
