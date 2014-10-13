<?php

/**
 * @file
 * Handler include for MailHandler.
 */

namespace Drupal\monolog\Plugin\MonologHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\monolog\ConfigurableMonologHandlerInterface;
use Drupal\monolog\MonologHandlerBase;
use Monolog\Handler\NativeMailerHandler;

/**
 * Sends emails using PHP's <code>mail()</code> function..
 *
 * @MonologHandler(
 *   id = "native_mailer",
 *   label = @Translation("Native Mail Handler"),
 *   description = @Translation("Sends emails using PHP's <code>mail()</code> function."),
 *   group = @Translation("Alerts and emails"),
 * )
 */
class NativeMailerMonologHandler extends MonologHandlerBase implements ConfigurableMonologHandlerInterface {

  /**
   * {@inheritdoc}
   */
  public function getHandlerClass() {
    // @todo Switch to tokens.
    if ('<site-mail>' == $this->configuration['to']) {
      $options['to'] = \Drupal::config('system.site')->get('site_mail');
    }
    if ('<site-mail>' == $this->configuration['from']) {
      $options['from'] = \Drupal::config('system.site')->get('site_mail');
    }
    return new NativeMailerHandler($this->configuration['to'], $this->configuration['subject'], $this->configuration['from'], $this->configuration['level'], $this->configuration['bubble']);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['to'] = array(
      '#title' => $this->t('Receiver'),
      '#type' => 'email',
      '#default_value' => $this->configuration['to'],
      '#description' => $this->t('The email address that messages will be sent to.<br/><code>&lt;site-mail&gt;</code> will use address that this site uses to send automated emails.'),
      '#required' => TRUE,
    );

    $form['subject'] = array(
      '#title' => $this->t('Subject'),
      '#type' => 'textfield',
      '#default_value' => $this->configuration['subject'],
      '#description' => $this->t('The subject of the email address.'),
      '#required' => TRUE,
    );

    $form['from'] = array(
      '#title' => $this->t('Sender'),
      '#type' => 'email',
      '#default_value' => $this->configuration['from'],
      '#description' => $this->t('The email address of the sender.<br/><code>&lt;site-mail&gt;</code> will use address that this site uses to send automated emails.'),
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['to'] = $form_state->getValue('to');
    $this->configuration['subject'] = $form_state->getValue('subject');
    $this->configuration['from'] = $form_state->getValue('from');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'to' => '<site-mail>',
      'from' => '<site-mail>',
      'subject' => $this->t('Log message sent by !site', array('!site' => \Drupal::config('system.site')->get('site_name'))),
    ];
  }

}