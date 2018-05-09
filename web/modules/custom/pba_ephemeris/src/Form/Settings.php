<?php

namespace Drupal\pba_ephemeris\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class Settings extends ConfigFormBase {

  public function getFormId() {
    return 'pba_ephemeris_settings';
  }

  protected function getEditableConfigNames() {
    return [
      'pba_ephemeris.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('pba_ephemeris.settings');

    $time = $config->get('publish_time');
    $time = $time
      ? new DrupalDateTime($time)
      : DrupalDateTime::createFromFormat('Y-m-d H:i:s', '1970-01-01 08:00:00');

    $form['auto_publish'] = [
      '#type' => 'checkbox',
      '#title' => 'Auto-publish',
      '#default_value' => $config->get('auto_publish'),
    ];

    $form['publish_time'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Publish time'),
      '#description' => $this->t('The exact publish time depends on how often the cron runs'),
      '#default_value' => $time,
      '#size' => 20,
      '#date_date_element' => 'none',
      '#date_time_element' => 'time',
      '#date_time_format' => 'H:i',
    ];
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('pba_ephemeris.settings')
      ->set('publish_time', (string) $values['publish_time'])
      ->set('auto_publish', $values['auto_publish'])
      ->save();
    parent::submitForm($form, $form_state);
  }

}
