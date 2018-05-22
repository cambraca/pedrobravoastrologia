<?php

namespace Drupal\pba_ephemeris\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    // See https://drupal.stackexchange.com/a/222042/3904
    \Drupal::service('page_cache_kill_switch')->trigger();

    return $this->redirect('entity.node.canonical', ['node' => $this->getLatestPostId()]);
  }

  protected function getLatestPostId() {
    $query = $this->storage->getQuery();
    $query->condition('type', 'post');
    $query->condition('status', NodeInterface::PUBLISHED);
    $query->sort('field_date', 'DESC');
    $query->range(0, 1);
    $nids = $query->execute();
    if (!$nids) {
      return NULL;
    }

    return current($nids);
  }

  function js($y, $m, $d) {
    $query = $this->storage->getQuery();
    $query->condition('type', 'post');
    if (\Drupal::currentUser()->isAnonymous())
      $query->condition('status', NodeInterface::PUBLISHED);
    $query->condition('field_date', "$y-$m-$d");
    $query->range(0, 1);
    $nids = $query->execute();
    if (!$nids) {
      throw new NotFoundHttpException();
    }
    return $this->outputJsPost(current($nids));
  }

  protected function outputJsPost($nid) {
    $post = Node::load($nid);
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $rendered = $view_builder->view($post);
    $array = [
      'url' => $post->toUrl()->toString(),
      'rendered' => render($rendered),
    ];
    return new JsonResponse($array);
  }

  function jsLatest() {
    return $this->outputJsPost($this->getLatestPostId());
  }
}
