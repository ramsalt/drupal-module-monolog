<?php

/**
 * @file
 * Contains \Drupal\monolog\Form\MonologProfileEditForm.
 */

namespace Drupal\monolog\Form;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\monolog\MonologHandlerManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Add form for monolog profile edit forms.
 */
class MonologProfileEditForm extends MonologProfileFormBase {

  /**
   * The monolog handler manager service.
   *
   * @var \Drupal\monolog\MonologHandlerManager
   */
  protected $handlerManager;

  /**
   * Constructs an ImageStyleEditForm object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $monolog_profile_storage
   *   The storage.
   * @param \Drupal\monolog\MonologHandlerManager $monolog_handler_manager
   *   The monolog handler manager service.
   */
  public function __construct(EntityStorageInterface $monolog_profile_storage, MonologHandlerManager $monolog_handler_manager) {
    parent::__construct($monolog_profile_storage);
    $this->handlerManager = $monolog_handler_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('monolog_profile'),
      $container->get('plugin.manager.monolog.handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $user_input = $form_state->getUserInput();

    $form['channel_table'] = array(
      '#theme' => 'monolog_handler_table',
      '#tree' => TRUE,
      '#monolog' => array(
        'profile' => $this->entity,
        'handler_info' => [],//monolog_handler_info_load_all(),
      ),
    );

    foreach ($this->entity->getHandlers() as $name => $handler) {

      $form['channel_table']['level'][$name] = array(
        '#type' => 'select',
        '#title' => t('Logging level for @handler', array('@handler' => $handler->label())),
        '#title_display' => 'invisible',
        '#default_value' => $handler->getConfiguration('level'),
        '#options' => monolog_level_options(),
      );

      $form['channel_table']['bubble'][$name] = array(
        '#type' => 'select',
        '#title' => t('Bubble setting for @handler', array('@handler' => $handler->label())),
        '#title_display' => 'invisible',
        '#default_value' => $handler->getConfiguration('bubble'),
        '#options' => array(
          1 => t('Yes'),
          0 => t('No'),
        ),
      );

      $weight_options = range(-10, 10);
      $form['channel_table']['weight'][$name] = array(
        '#type' => 'select',
        '#title' => t('Weight for @handler', array('@handler' => $handler->label())),
        '#title_display' => 'invisible',
        '#options' => array_combine($weight_options, $weight_options),
        '#default_value' => $handler->getConfiguration('weight'),
        '#attributes' => array('class' => array('monolog-handler-weight')),
      );
    }

    // Build the new image effect addition form and add it to the effect list.
    $new_handler_options = array();
    $handlers = $this->handlerManager->getDefinitions();
    uasort($handlers, function ($a, $b) {
      return strcasecmp($a['id'], $b['id']);
    });
    foreach ($handlers as $handler => $definition) {
      $new_handler_options[$handler] = $definition['label'];
    }
    $form['handlers']['new'] = array(
      '#tree' => FALSE,
      '#weight' => isset($user_input['weight']) ? $user_input['weight'] : NULL,
      '#attributes' => array('class' => array('draggable')),
    );
    $form['handlers']['new']['handler'] = array(
      'data' => array(
        'new' => array(
          '#type' => 'select',
          '#title' => $this->t('Effect'),
          '#title_display' => 'invisible',
          '#options' => $new_handler_options,
          '#empty_option' => $this->t('Select a new handler'),
        ),
        array(
          'add' => array(
            '#type' => 'submit',
            '#value' => $this->t('Add'),
            '#validate' => array('::handlerValidate'),
            '#submit' => array('::submitForm', '::handlerSave'),
          ),
        ),
      ),
      '#prefix' => '<div class="image-style-new">',
      '#suffix' => '</div>',
    );

    $form['handlers']['new']['weight'] = array(
      '#type' => 'weight',
      '#title' => $this->t('Weight for new effect'),
      '#title_display' => 'invisible',
      '#default_value' => count($this->entity->getHandlers()) + 1,
      '#attributes' => array('class' => array('image-effect-order-weight')),
    );
    $form['handlers']['new']['operations'] = array(
      'data' => array(),
    );

    return parent::form($form, $form_state);
  }

  /**
   * Validate handler for monolog handler.
   */
  public function handlerValidate($form, FormStateInterface $form_state) {
    if (!$form_state->getValue('new')) {
      $form_state->setErrorByName('new', $this->t('Select a handler to add.'));
    }
  }

  /**
   * Submit handler for monolog handler.
   */
  public function handlerSave($form, FormStateInterface $form_state) {
    $this->save($form, $form_state);

    // Check if this field has any configuration options.
    $handler = $this->handlerManager->getDefinition($form_state->getValue('new'));

    // Load the configuration form for this option.
    if (is_subclass_of($handler['class'], '\Drupal\monolog\ConfigurableMonologHandlerInterface')) {
      $form_state->setRedirect(
        'monolog.profile_handler_add_form',
        array(
          'monolog_profile' => $this->entity->id(),
          'monolog_handler' => $form_state->getValue('new'),
        ),
        array('query' => array('weight' => $form_state->getValue('weight')))
      );
    }
    // If there's no form, immediately add the handler.
    else {
      $handler = array(
        'id' => $handler['id'],
        'data' => array(),
        'weight' => $form_state->getValue('weight'),
      );
      $handler_id = $this->entity->addHandler($handler);
      $this->entity->save();
      if (!empty($handler_id)) {
        drupal_set_message($this->t('The handler was successfully applied.'));
      }
    }
  }

}
