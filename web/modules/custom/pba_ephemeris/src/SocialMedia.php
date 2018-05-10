<?php

namespace Drupal\pba_ephemeris;

use Drupal\node\Entity\Node;
use Facebook\Facebook;

class SocialMedia {
  static function facebook() {
    $config = \Drupal::configFactory()->get('pba_ephemeris.settings');
    return new Facebook([
      'app_id' => $config->get('facebook_app_id'),
      'app_secret' => $config->get('facebook_app_secret'),
    ]);
  }

  static function postToFacebook(Node $node) {
    if ($node->bundle() !== 'post' || !$node->isPublished())
      return;

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
      self::facebook()->post('/me/photos', $data);
    } catch(\Exception $e) {
      \Drupal::logger('pba_ephemeris')
        ->error('Error posting photo to Facebook', ['exception' => $e]);
    }
  }
}
