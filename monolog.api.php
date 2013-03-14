<?php

/**
 * @file
 * Hooks provided by the Composer Manager module.
 */

use Monolog\Handler\StreamHandler;
use Monolog\Handler\HandlerInterface;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Define monolog channels.
 */
function hook_monolog_channel_info() {
  $channels = array();

  $channels['search'] = array(
    'label' => t('Search'),
    'default profile' => 'stream_handler',
  );

  return $channels;
}

/**
 * Define monolog handlers.
 */
function hook_monolog_handler_info() {
  $handlers = array();

  $handlers['stream_handler'] = array(
    'label' => t('Stream Handler'),
    'description' => t('Logs records into any PHP stream, use this for log files.'),
    'loader callback' => 'mymodule_stream_handler_loader',
    'settings form' => 'mymodule_stream_handler_settings',
    'default settings' => array(
      'filepath' => 'public://monolog/drupal.log',
    ),
  );

  return $handlers;
}

/**
 * Example loader callback.
 *
 * Loader callbacks instantiate the handler class.
 *
 * @param array $options
 *   The configuration options set for this handler.
 *
 * @return HandlerInterface
 *
 * @see hook_monolog_handler_info()
 */
function mymodule_stream_handler_loader($options) {
  return new StreamHandler($options['filepath'], $options['level']);
}

/**
 * Example Monolog settings form.
 *
 * The forms add handler specific options to the handler settings pages.
 *
 * @param array $handler
 *   The handler options set for profile the handler is attached to.
 */
function monolog_stream_handler_settings(&$form, &$form_state, array $handler) {
  $form['filepath'] = array(
    '#title' => 'Log file path',
    '#type' => 'textfield',
    '#default_value' => $handler['filepath'],
    '#description' => t('The path or URI that the log file will be written to.'),
  );
}

/**
 * @} End of "addtogroup hooks".
 */
