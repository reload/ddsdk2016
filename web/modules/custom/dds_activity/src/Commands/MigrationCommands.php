<?php

namespace Drupal\dds_activity\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\dds_activity\ActivityFetcher;
use Drush\Commands\DrushCommands;
use GuzzleHttp\Exception\ClientException;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
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
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   * @param LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->loggerChannelFactory = $loggerChannelFactory;
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
    /** @var ActivityFetcher $activity_fetcher */
    $activity_fetcher = \Drupal::service('dds_activity.activity_fetcher');

    $operations = [];
    $numOperations = 0;
    $batchId = 1;

    for ($id = 0; $id <= 10; $id++) {
      $activity_loaded = $activity_fetcher->loadActivity($id);
      if ($activity_loaded) {
        $activity = new \stdClass();
        $activity->title = $activity_fetcher->getTitle();
        $activity->ages = $activity_fetcher->getAges();
        $activity->description = $activity_fetcher->getDescription();
        $activity->duration = $activity_fetcher->getDuration();
        $activity->mainImage = $activity_fetcher->getImageUrl();
        $activity->secondaryImages = $activity_fetcher->getSecondaryImageUrls();
        $activity->instructions = $activity_fetcher->getInstructions();
        $activity->materials = $activity_fetcher->getMaterials();
        $activity->types = $activity_fetcher->getTypes();
        $activity->youTube = $activity_fetcher->getYoutube();
        $activity->questions = $activity_fetcher->getQuestions();

        $this->output()->writeln("Preparing batch: " . $batchId);
        $operations[] = [
          '\Drupal\dds_activity\BatchImportService::importActivity',
          [
            $activity,
            t('Importing activity @id', ['@id' => $id]),
          ],
        ];
        $batchId++;
        $numOperations++;
      }

    }

    $batch = [
      'title' => t('Importing @num activities', ['@num' => $numOperations]),
      'operations' => $operations,
      'finished' => '\Drupal\dds_activity\BatchImportService::importActivityFinished',
    ];

    batch_set($batch);
    drush_backend_batch_process();

    $this->logger()->notice("Batch operations end.");
    $this->loggerChannelFactory->get('dds_activity')->info('Update batch operations end.');
  }

}
