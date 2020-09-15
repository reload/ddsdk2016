<?php

namespace Drupal\dds_activity\Commands;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountSwitcherInterface;
use Drupal\dds_activity\ActivityFetcher;
use Drupal\dds_activity\ActivityData;
use Drush\Commands\DrushCommands;
use Drupal\dds_activity\BatchImportService;
use GuzzleHttp\Exception\GuzzleException;

/**
 * A Drush command file.
 */
class MigrationCommands extends DrushCommands {

  // The import command tries to import from a given range of ids.
  // Max and min values are defined here.
  const MIN_ACTIVITY_ID = 1;
  const MAX_ACTIVITY_ID = 1200;

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
   * Constructs a new Migration Commands Object.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   * @param AccountSwitcherInterface $account_switcher
   * @param LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   * @param ActivityFetcher $activity_fetcher
   * @param BatchImportService $batch_import_service
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    AccountSwitcherInterface $account_switcher,
    LoggerChannelFactoryInterface $loggerChannelFactory,
    ActivityFetcher $activity_fetcher,
    BatchImportService $batch_import_service
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->accountSwitcher = $account_switcher;
    $this->loggerChannelFactory = $loggerChannelFactory;
    $this->activityFetcher = $activity_fetcher;
    $this->batchImporter = $batch_import_service;

    parent::__construct();
  }

  /**
   * Import all activities from deprecated activity database.
   *
   * @command activity:import
   * @aliases activity-import
   *
   * @usage activity:import
   * @throws InvalidPluginDefinitionException
   * @throws PluginNotFoundException
   * @throws GuzzleException
   */
  public function activityImport() {
    $this->loggerChannelFactory->get('dds_activity')->info('Import activities batch operations start');
    $root_user = $this->entityTypeManager->getStorage('user')->load(1);
    $this->accountSwitcher->switchTo($root_user);

    $operations = [];
    $numOperations = 0;
    $batchId = 1;

    for ($id = self::MIN_ACTIVITY_ID; $id <= self::MAX_ACTIVITY_ID; $id++) {
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
