<?php

namespace Drupal\pba_ephemeris\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SocialMedia extends FormBase {

  public function getFormId() {
    return 'pba_ephemeris_social_media';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['post'] = [
      '#type' => 'submit',
      '#value' => $this->t('Post to Facebook'),
      '#submit' => ['::postToFacebook'],
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  public function postToFacebook(array &$form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');
    if (\Drupal\pba_ephemeris\SocialMedia::postToFacebook($node))
      \Drupal::messenger()
        ->addMessage($this->t('Post to Facebook successful!'));
    else
      \Drupal::messenger()
        ->addError($this->t('Post to Facebook failed'));
  }

}
