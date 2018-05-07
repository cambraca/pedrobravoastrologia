<?php

namespace Drupal\pba_ephemeris\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostController extends ControllerBase {

  /** @var \Drupal\node\NodeStorage */
  protected $storage;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->storage = $entity_type_manager->getStorage('node');
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  function latest() {
    $query = $this->storage->getQuery();
    $query->condition('type', 'post');
    $query->condition('status', NodeInterface::PUBLISHED);
    $query->sort('field_date', 'DESC');
    $query->range(0, 1);
    $nids = $query->execute();
    if (!$nids) {
      return NULL;
    }

    return $this->redirect('entity.node.canonical', ['node' => current($nids)]);
  }
}
