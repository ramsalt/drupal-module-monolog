<?php

/**
 * @file
 * Administrative settings for the Monolog module.
 */

use Drupal\Component\Utility\String;
use Drupal\Component\Utility\Xss;

/**
 * Channel settings for the Monolog module.
 *
 * @ingroup forms
 */
function monolog_channel_form($form, &$form_state) {
  $channel_info = monolog_channel_info_load_all();
  $channel_profiles = variable_get('monolog_channel_profiles', array());

  $form['description'] = array(
    '#markup' => t('<p>A <strong>channel</strong> identifies which part of the application a record is related to.</p><p>Each channel is associated with a <a href="@href">profile</a> that defines which handlers are used to process the record, for example a <em>syslog handler</em> or <em>stream wrapper handler</em>.</p>', array('@href' => url('admin/config/development/monolog/profile'))),
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
    $form['channel_table']['channels'][$channel_name]['profile'] = array(
      '#type' => 'select',
      '#options' => monolog_profile_options(),
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

  $form['#submit'] = array('monolog_channel_form_submit');

  return $form;
}

/**
 * Form submission handler for monolog_channel_form().
 */
function monolog_channel_form_submit($form, &$form_state) {
  $channel_profiles = array();
  foreach ($form_state['values']['channel_table']['channels'] as $name => $channel) {
    $channel_profiles[$name] = $channel['profile'];
  }
  variable_set('monolog_channel_profiles', $channel_profiles);
  drupal_set_message(t('The configuration options have been saved.'));
}

/**
 * Returns HTML for the channel table.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: The FAPI element being themed.
 *
 * @ingroup themeable
 */
function theme_monolog_channel_table(array $variables) {
  $output = '';

  $header = array(
    'channel' => t('Channel'),
    'profile' => t('Logging Profile'),
  );

  $rows = array();
  $channels = $variables['element']['channel_info']['#value'];
  foreach ($channels as $channel_name => $channel_info) {

    // @todo Theme function.
    $label = String::checkPlain($channel_info['label']);
    $machine_name = '<small>' . t('(Machine name: @name)', array('@name' => $channel_name)) . '</small>';
    $descripton = '<div class="description">' . Xss::filterAdmin($channel_info['description'])  . '</div>';

    $rows[] = array(
      'channel' => $label . ' ' . $machine_name . $descripton,
      'profile' => drupal_render($variables['element']['channels'][$channel_name]['profile']),
    );
  }

  $output .= drupal_render_children($variables['element']);
  $output .= _theme('table', array(
    'caption' => t('Logging Channels'),
    'header' => $header,
    'rows' => $rows,
    'empty' => t('There are no available logging channels.'),
    'attributes' => array('id' => 'monolog-channel-table'),
  ));

  return $output;
}

/**
 * Returns HTML for the handler table.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: The FAPI element being themed.
 *
 * @ingroup themeable
 */
function theme_monolog_handler_table(array $variables) {
  $output = '';

  $header = array(
    t('Label'),
    t('Handler'),
    t('Log Level'),
    t('Bubble Messages'),
    t('Weight'),
    t('Operations'),
  );

  $level_options = monolog_level_options();

  $profile = $variables['element']['#monolog']['profile'];

  $rows = array();
  foreach ($profile->getHandlers() as $name => $handler) {
    $base_path = 'base://admin/config/development/monolog/profile/' . $profile->id() . '/handler/' . $handler->getPluginId();

    $row = array(
      'label' => String::checkPlain($handler->label()),
      'handler' => String::checkPlain($handler->getPluginId()),
      'level' => drupal_render($variables['element']['level'][$name]),
      'bubble' => drupal_render($variables['element']['bubble'][$name]),
      'weight' => drupal_render($variables['element']['weight'][$name]),
    );

    $operations = array();
    $operations[] = array(
      'title' => t('Edit'),
      'url' => Drupal\Core\Url::fromUri($base_path . '/edit'),
    );
    $operations[] = array(
      'title' => t('Delete'),
      'url' => Drupal\Core\Url::fromUri($base_path . '/delete'),
    );

    $row['operations'] = array(
      'data' => array(
        '#theme' => 'links__node_operations',
        '#links' => $operations,
        '#attributes' => array('class' => array('links', 'inline')),
      ),
    );

    $rows[$name] = array(
      'data' => $row,
      'class' => array('draggable'),
    );
  }

  $output .= drupal_render_children($variables['element']);
  $output .= _theme('table', array(
    'caption' => t('Logging Handlers'),
    'header' => $header,
    'rows' => $rows,
    'empty' => t('This profile has no handlers. Add one by clicking the "Add handler" link above.'),
    'attributes' => array('id' => 'monolog-handler-table'),
    'tabledrag' => array(
      'action' => 'order',
      'relationship' => 'sibling',
      'group' => 'monolog-handler-weight',
    ),
  ));

  return $output;
}
