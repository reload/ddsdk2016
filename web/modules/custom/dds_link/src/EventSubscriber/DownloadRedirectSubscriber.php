<?php

/**
 * @file
 * Contains \Drupal\dds_link\EventSubscriber\DownloadRedirectSubscriber.
 */

namespace Drupal\dds_link\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DownloadRedirectSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return([
      KernelEvents::REQUEST => [
        ['redirectDownloadNodeType'],
      ]
    ]);
  }

  /**
   * Redirect requests for download node types to field_file URL.
   *
   * @param GetResponseEvent $event
   */
  public function redirectDownloadNodeType(GetResponseEvent $event) {
    $request = $event->getRequest();

    // This is necessary because this also gets called on node sub-tabs such as
    // "edit", "revisions", etc.  This prevents those pages from redirected.
    if ($request->attributes->get('_route') !== 'entity.node.canonical') {
      return;
    }

    // Only redirect a certain content type.
    if ($request->attributes->get('node')->getType() !== 'download') {
      return;
    }

    $file_uri = $request->attributes->get('node')->get('field_file')->first()->entity->getFileUri();
    $file_downloadable_link = file_create_url($file_uri);

    $redirect_url = Url::fromUri($file_downloadable_link);
    // We use a temporary redirect so the file can be changed without getting
    // stuck in browser caches.
    $response = new RedirectResponse($redirect_url->toString(), Response::HTTP_TEMPORARY_REDIRECT);
    $event->setResponse($response);
  }

}
