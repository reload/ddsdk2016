<?php

namespace Drupal\dds_link\EventSubscriber;

use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\file\FileInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Download redirect subscriber.
 */
class DownloadRedirectSubscriber implements EventSubscriberInterface {

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $fileUrlGenerator
   *   The file URL generator.
   */
  public function __construct(FileUrlGeneratorInterface $fileUrlGenerator) {
    $this->fileUrlGenerator = $fileUrlGenerator;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return([
      KernelEvents::REQUEST => [
        ['redirectDownloadNodeType'],
      ],
    ]);
  }

  /**
   * Redirect requests for download node types to field_file URL.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The event.
   */
  public function redirectDownloadNodeType(RequestEvent $event) {
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

    if ($request->attributes->get('node')->get('field_file')->isEmpty()) {
      return;
    }

    if (!$request->attributes->get('node')->get('field_file')->first()->entity instanceof FileInterface) {
      return;
    }

    $file_uri = $request->attributes->get('node')->get('field_file')->first()->entity->getFileUri();
    $redirect_url = $this->fileUrlGenerator->generate($file_uri);

    // We use a temporary redirect so the file can be changed without getting
    // stuck in browser caches.
    $response = new RedirectResponse($redirect_url->toString(), Response::HTTP_TEMPORARY_REDIRECT);
    $event->setResponse($response);
  }

}
