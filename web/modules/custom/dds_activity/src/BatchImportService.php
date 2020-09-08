<?php

namespace Drupal\dds_activity;

use Drupal;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\Core\Session\UserSession;
use Drupal\node\Entity\Node;

/**
 * Class BatchService.
 */
class BatchImportService {

  /**
   * @var MessengerInterface
   */
  private $messenger;

  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }
  /**
   * Batch process callback.
   *
   * @param ActivityData $activity
   *   Id of the batch.
   * @param object $context
   *   Context for operations.
   * @throws Drupal\Core\Entity\EntityStorageException
   */
  public function importActivity(ActivityData $activity, &$context): void {
    $query = Drupal::service('entity.query')
      ->get('node')
      ->condition('field_activity_id', $activity->id);
    $query_result = $query->execute();

    if (!empty($query_result)) {
      $nid = array_values($query_result)[0];
      $node = Node::load($nid);
    }
    else {
      $node = Node::create(['type' => 'activity']);
    }

    (new ActivityHydrater($activity))
      ->hydrateNode($node)
      ->save();

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
  public function importActivityFinished($success, $results, array $operations): void {
    if ($success) {
      $this->messenger->addMessage(t('@count results processed.', ['@count' => $results]));
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $this->messenger->addMessage(
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
