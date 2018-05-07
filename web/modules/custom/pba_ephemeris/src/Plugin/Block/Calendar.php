<?php

namespace Drupal\pba_ephemeris\Plugin\Block;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Sidebar calendar.
 *
 * @Block(
 *   id = "calendar",
 *   admin_label = @Translation("Sidebar calendar"),
 *   category = @Translation("PBA"),
 * )
 */
class Calendar extends BlockBase {
  public function build() {
    $start = new DrupalDateTime('2015-10-03 8:34:59');
    $now = new DrupalDateTime();

    $years = [];
    $is_first = TRUE;

    for ($year = intval($start->format('Y')); $year <= intval($now->format('Y')); $year++) {
      $months = [];

      $start_month = $is_first ? intval($start->format('n')) : 1;
      $end_month = $year === intval($now->format('Y')) ? intval($now->format('n')) : 12;
      for ($month = $start_month; $month <= $end_month; $month++) {
        $months[] = [
          '#theme' => 'sidebar_calendar_month',
          '#date' => new DrupalDateTime("$year-$month-1"),
          '#cache' => ['max-age' => 100000], //TODO: doesn't work like this
        ];
      }

      $years[] = [
        '#theme' => 'sidebar_calendar_year',
        '#date' => new DrupalDateTime("$year-1-1"),
        '#months' => $months,
      ];
      $is_first = FALSE;
    }

    return [
      '#theme' => 'sidebar_calendar',
      '#years' => $years,
      '#cache' => ['max-age' => 0],
    ];
  }
}
