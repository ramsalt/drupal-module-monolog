<?php

/**
 * @file
 * Contains \Drupal\monolog\Form\SettingsForm.
 */

namespace Drupal\monolog\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\monolog\Logger\Processor\ProcessorManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures monolog logging settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * @var \Drupal\monolog\Logger\Processor\ProcessorManagerInterface
   */
  protected $processorManager;

  /**
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\monolog\Logger\Processor\ProcessorManagerInterface $processor_manager
   */
  public function __construct(ConfigFactoryInterface $config_factory, ProcessorManagerInterface $processor_manager) {
    $this->processorManager = $processor_manager;

    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('monolog.processor_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'monolog_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $processors = $this->processorManager->getProcessors();

    $options = [];
    foreach ($processors as $processor) {
      /** @var \Drupal\monolog\Logger\Processor\ProcessorInterface $processor */
      $options[$processor->getType()] = $processor->getDescription();
    }

    $config = $this->config('monolog.settings');

    $form['logging_processors'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Include extra data in record'),
      '#description' => $this->t('Include the selected extra data in all log messages that are routed through Monolog from <code>watchdog()</code>.'),
      '#options' => $options,
      '#default_value' => $config->get('logging_processors'),
    );

    $form['drupal_compatibility'] = array(
      '#type' => 'item',
      '#title' => $this->t('Drupal compatibility'),
    );

    $form['type_as_channel'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Use the watchdog type as the channel name'),
      '#description' => $this->t('Enable this option to use the watchdog type as each record\'s channel name instead of "watchdog". This allows handlers such as the GELF handler to behave as the current Drupal watchdog implementations do.'),
      '#default_value' => $config->get('type_as_channel'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('monolog.settings')
      ->set('logging_processors', $form_state->getValue('logging_processors'))
      ->set('type_as_channel', $form_state->getValue('type_as_channel'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['monolog.settings'];
  }

}
