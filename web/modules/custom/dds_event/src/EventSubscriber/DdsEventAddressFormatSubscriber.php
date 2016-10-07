<?php

namespace Drupal\dds_event\EventSubscriber;

use Drupal\address\Event\AddressFormatEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * De-requires address fields.
 */
class DdsEventAddressFormatSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    // Really should have used Drupal\address\Event\AddressEvents:ADDRESS_FORMAT
    // here but we're running in to some wird problem with cmi.
    // Should be able to revert back after this version hits all
    // environments and all developers.
    $events['address.address_format'][] = array('onGetDefinition', 0);
    return $events;
  }

  /**
   * Clear the required-state of all fields.
   */
  public function onGetDefinition(AddressFormatEvent $event) {
    $definition = $event->getDefinition();
    // This makes all address fields optional for all entity types on site.
    // We can't set empty array because of check in AddressFormat.php, line
    // 128. Any version of the address library after v1.0.0-beta1 has a fix.
    $definition['required_fields'] = ['postalCode'];
    $event->setDefinition($definition);
  }

}