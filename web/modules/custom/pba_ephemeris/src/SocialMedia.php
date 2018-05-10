<?php

namespace Drupal\pba_ephemeris;

use Drupal\node\Entity\Node;
use Facebook\Facebook;

class SocialMedia {

  /**
   * @return \Facebook\Facebook|null
   */
  static function facebook() {
    $config = \Drupal::configFactory()->get('pba_ephemeris.settings');

    if (!$config->get('facebook_app_id') || !$config->get('facebook_page_access_token'))
      return NULL;

    return new Facebook([
      'app_id' => $config->get('facebook_app_id'),
      'app_secret' => $config->get('facebook_app_secret'),
    ]);
  }

  static function postToFacebook(Node $node) {
    if ($node->bundle() !== 'post' || !$node->isPublished())
      return;

    $fb = self::facebook();
    if (!$fb)
      return;

    $config = \Drupal::configFactory()->get('pba_ephemeris.settings');
    $access_token = $config->get('facebook_page_access_token');

    /** @var \Drupal\file\Entity\File $image */
    $image = $node->get('field_image')->entity;
    if (!$image) {
      \Drupal::logger('pba_ephemeris')
        ->error('Trying to post node without image to Facebook', ['nid' => $node->id()]);
      return;
    }

    $data = [
//      'source' => $fb->fileToUpload(\Drupal::service('file_system')->realpath($image->getFileUri())),
      'url' => $image->url(),
      'message' => $node->toUrl()->setAbsolute()->toString(),
    ];

    try {
      $fb->post('/me/photos', $data, $access_token);
    } catch(\Exception $e) {
      \Drupal::logger('pba_ephemeris')
        ->error('Error posting photo to Facebook', ['exception' => $e]);
    }
  }
}
