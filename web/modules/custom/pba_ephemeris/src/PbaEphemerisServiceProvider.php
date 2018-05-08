<?php

namespace Drupal\pba_ephemeris;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class PbaEphemerisServiceProvider extends ServiceProviderBase {
  public function alter(ContainerBuilder $container) {
    // TODO: This is not working, so it's disabled and a hook_preprocess_menu implementation is highlighting the first main menu item on post node pages.
//    $definition = $container->getDefinition('menu.active_trail');
//    $definition->setClass('Drupal\pba_ephemeris\CustomMenuActiveTrail');
  }
}
