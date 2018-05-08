<?php

namespace Drupal\pba_ephemeris;

use Drupal\Core\Menu\MenuActiveTrail;

class CustomMenuActiveTrail extends MenuActiveTrail {
  public function getActiveLink($menu_name = NULL) {
    if ($menu_name !== 'main')
      return parent::getActiveLink($menu_name);

    /** @var \Drupal\node\Entity\Node $node */
    $node = $this->routeMatch->getParameter('node');
    if (!$node || $node->bundle() !== 'post')
      return parent::getActiveLink($menu_name);

    $found = NULL;
    $links = $this->menuLinkManager->loadLinksByRoute('pba_ephemeris.latest', [], $menu_name);
    if ($links)
      $found = reset($links);
    return $found;
  }
}
