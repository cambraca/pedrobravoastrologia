<?php

namespace Drupal\pba_ephemeris\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\search_api\Entity\Server;

/**
 * Search box.
 *
 * @Block(
 *   id = "search",
 *   admin_label = @Translation("Search box"),
 *   category = @Translation("PBA"),
 * )
 */
class Search extends BlockBase {

  public function build() {
    $config = \Drupal::configFactory()->get('pba_ephemeris.settings');

    return [
      '#theme' => 'pba_search',
      '#attached' => [
        'library' => [
          'pba_ephemeris/search',
        ],
        'drupalSettings' => [
          'algoliaAppId' => Server::load('algolia')
            ->getBackendConfig()['application_id'],
          'algoliaSearchApiKey' => $config->get('algolia_search_api_key'),
        ],
      ],
    ];
  }
}
