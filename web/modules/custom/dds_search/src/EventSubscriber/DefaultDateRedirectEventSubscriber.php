<?php

namespace Drupal\dds_search\EventSubscriber;

use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Adds a default date argument to a view and redirects the user.
 */
class DefaultDateRedirectEventSubscriber implements EventSubscriberInterface {

  protected $routeMatch;

  /**
   * Drupals page_cache_kill_switch service.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * DefaultDateRedirectEventSubscriber constructor.
   */
  public function __construct(RouteMatchInterface $route_match, KillSwitch $kill_switch) {
    $this->routeMatch = $route_match;
    $this->killSwitch = $kill_switch;
  }

  /**
   * Redirect the user to add a default date if necessary.
   */
  public function checkForRedirection(RequestEvent $event) {
    // If we are on the correct view page, check for arguments, if we have
    // none inject a default date and redirect the user.
    if ($this->routeMatch->getRouteName() === 'view.events.events_overview' && empty($event->getRequest()->query->all())) {
      $url = Url::fromRoute('view.events.events_overview', ['event_after' => date(DDS_SEARCH_DATE_USER_FORMAT)]);
      $event->setResponse(new RedirectResponse($url->toString()));
      // Make sure Drupals page-cache does not cache the redirect for anonymous
      // users.
      $this->killSwitch->trigger();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::REQUEST][] = ['checkForRedirection'];
    return $events;
  }

}
