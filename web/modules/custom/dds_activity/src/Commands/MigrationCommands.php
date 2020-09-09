<?php

namespace Drupal\dds_activity\Commands;

use Drupal;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\dds_activity\ActivityFetcher;
use Drupal\dds_activity\ActivityData;
use Drush\Commands\DrushCommands;
use Drupal\dds_activity\BatchImportService;

/**
 * A Drush command file.
 */
class MigrationCommands extends DrushCommands {

  /**
   * Entity type service.
   *
   * @var EntityTypeManagerInterface
   */
  private $entityTypeManager;
  /**
   * Logger service.
   *
   * @var LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;
  /**
   * @var ActivityFetcher
   */
  private $activityFetcher;
  /**
   * @var BatchImportService
   */
  private $batchImporter;
  /**
   * @var AccountSwitcherInterface
   */
  private $accountSwitcher;

  /**
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   * @param LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   * @param AccountSwitcherInterface $account_switcher
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, AccountSwitcherInterface $account_switcher,  LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->accountSwitcher = $account_switcher;
    $this->loggerChannelFactory = $loggerChannelFactory;
    $this->activityFetcher = Drupal::service('dds_activity.activity_fetcher');
    $this->batchImporter = Drupal::service('dds_activity.batch_importer');

    parent::__construct();
  }

  /**
   * Import all activities from deprecated activity database.
   *
   * @command activity:import
   * @aliases activity-import
   *
   * @usage activity:import
   */
  public function activityImport() {
    $this->loggerChannelFactory->get('dds_activity')->info('Import activities batch operations start');
    $root_user = $this->entityTypeManager->getStorage('user')->load(1);
    $this->accountSwitcher->switchTo($root_user);

    $operations = [];
    $numOperations = 0;
    $batchId = 1;

    // TODO: Set proper max id. eg.: 1100.
    for ($id = 0; $id <= 1200; $id++) {
      $activity_loaded = $this->activityFetcher->loadActivity($id);
      if ($activity_loaded) {
        $activity = ActivityData::constructFromActivityFetcher($this->activityFetcher, $id);
        $this->output()->writeln('Preparing batch: ' . $batchId);
        $operations[] = [
          [$this->batchImporter, 'importActivity'],
          [$activity],
        ];
        $batchId++;
        $numOperations++;
      }

    }

    $batch = [
      'title' => t('Importing @num activities', ['@num' => $numOperations]),
      'operations' => $operations,
      'finished' => [[$this->batchImporter, 'importActivityFinished']],
    ];

    batch_set($batch);
    drush_backend_batch_process();

    $this->logger()->notice("Batch operations end.");
    $this->loggerChannelFactory->get('dds_activity')->info('Update batch operations end.');
    $this->accountSwitcher->switchBack();
  }

}
