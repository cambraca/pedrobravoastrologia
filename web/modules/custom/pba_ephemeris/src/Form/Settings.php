<?php

namespace Drupal\pba_ephemeris\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\pba_ephemeris\SocialMedia;

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

    $form['facebook_app_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook App ID'),
      '#default_value' => $config->get('facebook_app_id'),
    ];

    $form['facebook_app_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook App Secret'),
      '#description' => $this->t('Leave empty if you don\'t want to change the value'),
    ];

    $fb = SocialMedia::facebook();
    if ($fb) {
      $helper = $fb->getRedirectLoginHelper();

      if (\Drupal::request()->query->get('fb') == 1) {
        try {
          $accessToken = $helper->getAccessToken();
          if ($accessToken) {
            $config->set('facebook_access_token', $accessToken->getValue());
            $config->save();
            \Drupal::messenger()
              ->addMessage($this->t('Access token stored successfully!'));
          }
          else {
            \Drupal::messenger()
              ->addError($this->t('Access token is empty'));
          }
        } catch (\Exception $e) {
          \Drupal::logger('pba_ephemeris')
            ->error('Error on Facebook login callback', ['exception' => $e]);
        }
      }

      $loginUrl = $helper->getLoginUrl(Url::fromRoute('<current>', [], [
        'absolute' => TRUE,
        'query' => ['fb' => 1]
      ])->toString(), ['manage_pages', 'publish_pages']);

      $form['facebook_login'] = [
        '#type' => 'link',
        '#title' => $this->t('Log into Facebook'),
        '#url' => Url::fromUri($loginUrl),
      ];
    }

    $form['facebook_get_page_access_token'] = [
      '#type' => 'submit',
      '#value' => $this->t('Get page access token'),
      '#submit' => ['::getFbPageAccessToken'],
    ];

    return parent::buildForm($form, $form_state);
  }

  public function getFbPageAccessToken(array &$form, FormStateInterface $form_state) {
    $config = $this->config('pba_ephemeris.settings');

    try {
      $response = SocialMedia::facebook()
        ->get('/me/accounts', $config->get('facebook_access_token'));
      $body = $response->getDecodedBody();
      $found = FALSE;
      foreach ($body['data'] as $page) {
        if ($page['id'] == 1502244163428127) {
          $config->set('facebook_page_access_token', $page['access_token']);
          $config->save();
          $found = TRUE;
          break;
        }
      }
      
      if ($found)
        \Drupal::messenger()
          ->addMessage($this->t('Page access token stored successfully!'));
      else
        \Drupal::messenger()
          ->addError($this->t('Could not get access to the Pedro Bravo page (id: 1502244163428127)'));
    } catch (\Exception $e) {
      \Drupal::messenger()
        ->addError($e->getMessage());
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('pba_ephemeris.settings');

    $config->set('publish_time', (string) $values['publish_time'])
      ->set('auto_publish', $values['auto_publish'])
      ->set('facebook_app_id', $values['facebook_app_id']);

    if (trim($values['facebook_app_secret']))
      $config->set('facebook_app_secret', trim($values['facebook_app_secret']));

    $config->save();
    parent::submitForm($form, $form_state);
  }

}
