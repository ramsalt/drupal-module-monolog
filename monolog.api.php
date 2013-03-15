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
 * Defines monolog channels.
 *
 * A channel identifies which part of the application a record is related to.
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
 * Contains default profile configurations.
 *
 * A profile is a collection of handlers that process the record.
 */
function hook_default_monolog_profiles() {
  $profiles = array();

  $profile = new stdClass();
  $profile->disabled = FALSE;
  $profile->api_version = 1;
  $profile->name = 'syslog';
  $profile->options = array(
    'label' => 'Syslog',
    'handlers' => array(
      'syslog' => array(
        'handler' => 'syslog',
        'label' => 'Syslog',
        'ident' => 'drupal',
        'level' => 200,
        'bubble' => 1,
        'weight' => -50,
      ),
    ),
  );
  $profiles[$profile->name] = $profile;

  return $profiles;
}

/**
 * Defines monolog handlers.
 *
 * Handlers process the records and write them to various sources such as files
 * remote servers, sockets, etc.
 *
 * @see https://github.com/Seldaek/monolog#handlers
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
