<?php

/**
 * @file
 * Contains \Drupal\monolog\Form\ChannelForm.
 */

namespace Drupal\monolog\Form;

use Drupal\Component\Utility\String;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a form that configures monolog channel settings.
 */
class ChannelForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'monolog_channel_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    \Drupal::moduleHandler()->loadInclude('monolog', 'inc', 'monolog.crud');
    $channel_info = monolog_channel_info_load_all();
    $channel_profiles = $this->config('monolog.settings')->get('monolog_channel_profiles');

    $form['description'] = array(
      '#markup' => t('<p>A <strong>channel</strong> identifies which part of the application a record is related to.</p><p>Each channel is associated with a <a href="@href">profile</a> that defines which handlers are used to process the record, for example a <em>syslog handler</em> or <em>stream wrapper handler</em>.</p>', array('@href' => '')),
    );

    $form['channel_table'] = array(
      '#theme' => 'monolog_channel_table',
      '#tree' => TRUE,
      'channel_info' => array(
        '#type' => 'value',
        '#value' => $channel_info,
      ),
      'channels' => array(),
    );

    foreach ($channel_info as $channel_name => $info) {
      if (!isset($channel_profiles[$channel_name])) {
        $channel_profiles[$channel_name] = $info['default profile'];
      }
      $profiles = \Drupal::entityManager()->getStorage('monolog_profile')->loadMultiple();
      foreach ($profiles as $profile) {
        $options[$profile->id()] = String::checkPlain($profile->label());
      }
      $form['channel_table']['channels'][$channel_name]['profile'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => $channel_profiles[$channel_name],
      );
    }

    $form['actions'] = array(
      '#type' => 'actions',
    );

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save channel settings'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $channel_profiles = array();
    $values = $form_state->getValues();
    foreach ($values['channel_table']['channels'] as $name => $channel) {
      $channel_profiles[$name] = $channel['profile'];
    }

    $this->config('monolog.settings')
      ->set('channel_profiles', $channel_profiles)
      ->save();
    drupal_set_message($this->t('The configuration options have been saved.'));
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['monolog.settings'];
  }

}
