<?php

/**
 * @file
 * Custom settings for the mungo theme.
 */

use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\FileInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function mungo_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  // Settings for the badge-overview-page.
  $form['badge_page_settings'] = array(
    '#type' => 'details',
    '#title' => t('Badge page settings'),
    '#open' => TRUE,
  );
  $form['badge_page_settings']['badge_cover_image'] = array(
    '#type' => 'container',
  );
  $form['badge_page_settings']['badge_cover_image']['badge_cover_image_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Path to the cover-image (use upload-field below)'),
    '#default_value' => theme_get_setting('badge_cover_image_path', 'mungo'),
    '#description' => t(
      'The title and description used to be configurable here as well. Now the title of the view and custom header on the view is used instead.'
    ),
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
}

/**
 * Finalizes upload of the image to use on the badge-overview page.
 *
 * @param array $element
 *   The form-element to be validated.
 * @param Drupal\Core\Form\FormState $form_state
 *   Current state of the form.
 */
function mungo_badge_cover_image_validate(array $element, FormState &$form_state) {
  mungo_handle_uploaded_file(
    $form_state,
    'badge_cover_image',
    'dds_badge',
    'badge_cover_image_path'
  );
}

/**
 * General handling of images in theme settings.
 *
 * @param \Drupal\Core\Form\FormState $form_state
 *   Current state of the form.
 * @param string $formFieldName
 *   Field in the settings form to process
 * @param string $file_usage_module
 *   The module that will be registered as user of the file.
 * @param string $themeSettingName
 *   Name of the theme setting that holds the current image.
 *
 * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
 * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
 */
function mungo_handle_uploaded_file(
  FormState $form_state,
  string $formFieldName,
  string $file_usage_module,
  string $themeSettingName ) {
  // Receive the uploaded file and register a file usage.
  /** @var Drupal\file\Entity\File $file */
  $uploaded_files = file_save_upload(
    $formFieldName,
    ['file_validate_is_image' => []],
    "public://",
    NULL,
    FILE_EXISTS_REPLACE
  );


  // Handle th uploaded file if available.
  if (!empty($uploaded_files[0]) && $uploaded_files[0] instanceof FileInterface) {
    $file = $uploaded_files[0];
    $uri = $file->getFileUri();

    /** @var \Drupal\file\FileUsage\FileUsageInterface $file_usage */
    $file_usage = \Drupal::service('file.usage');

    // Register the owner module.
    $file_usage->add($file, $file_usage_module, 'file', $file->id());

    // See if we can find the previous image and delete its file-usage.
    $old_image_uri = theme_get_setting($themeSettingName, 'mungo');
    if (!empty($old_image_uri)) {
      $old_file = \Drupal::entityTypeManager()
        ->getStorage('file')
        ->loadByProperties(['uri' => $old_image_uri]);
      if (!empty($old_file)) {
        $old_file = reset($old_file);
        $file_usage->delete(
          $old_file,
          $file_usage_module,
          'file',
          $old_file->id()
        );
      }
    }
    // Finish off by accepting the path to the image.
    $form_state->setValue($themeSettingName, $uri);
  }
}
