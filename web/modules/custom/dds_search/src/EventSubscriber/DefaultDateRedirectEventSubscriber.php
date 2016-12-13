<?php

namespace Drupal\dds_search\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds a default date argument to a view and redirects the user.
 */
class DefaultDateRedirectEventSubscriber implements EventSubscriberInterface {

  protected $routeMatch;

  /**
   * DefaultDateRedirectEventSubscriber constructor.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * Redirect the user to add a default date if necessary.
   */
  public function checkForRedirection(GetResponseEvent $event) {
    // If we are on the correct view page, check for arguments, if we have
    // none inject a default date and redirect the user.
    if ($this->routeMatch->getRouteName() === 'view.events.events_overview' && empty($event->getRequest()->query->all())) {
      $url = Url::fromRoute('view.events.events_overview', array('event_after' => date(DDS_SEARCH_DATE_USER_FORMAT)));
      $event->setResponse(new RedirectResponse($url->toString()));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::REQUEST][] = array('checkForRedirection');
    return $events;
  }

}
