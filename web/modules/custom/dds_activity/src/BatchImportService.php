<?php

namespace Drupal\dds_activity;

/**
 * Class BatchService.
 */
class BatchImportService {

  /**
   * Batch process callback.
   *
   * @param object $activity
   *   Id of the batch.
   * @param object $context
   *   Context for operations.
   */
  public function importActivity($activity, &$context) {
    $context['results']++;
    $context['message'] = t('Processing Activity: "@title"', ['@title' => $activity->title]);
  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array $results
   *   Array of results for post processing.
   * @param array $operations
   *   Array of operations.
   */
  public function importActivityFinished($success, $results, array $operations) {
    $messenger = \Drupal::messenger();
    if ($success) {
      $messenger->addMessage(t('@count results processed.', ['@count' => $results]));
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $messenger->addMessage(
        t('An error occurred while processing @operation with arguments : @args',
          [
            '@operation' => $error_operation[0],
            '@args' => print_r($error_operation[0], TRUE),
          ]
        )
      );
    }
  }

}
