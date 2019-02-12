<?php

namespace Drupal\dds_jobposting\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Produce a job-posting page.
 */
class JobPostingController extends ControllerBase {

  /**
   * Returns the content
   *
   * @return array
   *   A simple renderable array.
   */

  /**
   * Returns the contents of the page.
   *
   * @param int $paidJobsTid
   *   Taxonomy id of the paid job term.
   *
   * @param $volunteerJobsTid
   *   Taxonomy id of the volunteer job term.
   *
   * @return array
   */
  public function content($paidJobsTid, $volunteerJobsTid) {
    return   [
      'jobposting' => [
        '#theme' => 'dds_jobposting',
        '#top_image' => [
          '#theme' => 'dds_top_image',
          // Setup strings directly to allow for translation.
          '#title' => new TranslatableMarkup('Jobpostings'),
          '#subtitle' => new TranslatableMarkup('Jobs in the Danish Guide and Scout Association'),
          // Use image specified in the theme settings, see
          // mungo/theme-settings.php.
          '#image_uri' => theme_get_setting(
            'jobposting_cover_image_path',
            'mungo'
          ),
        ],
        // We embed views that lists the jobs and takes the term-id as argument.
        '#paid_jobs' => views_embed_view('jobpostings', 'default', $paidJobsTid),
        '#volunteer_jobs' => views_embed_view('jobpostings', 'default', $volunteerJobsTid),
      ],
    ];

  }
}
