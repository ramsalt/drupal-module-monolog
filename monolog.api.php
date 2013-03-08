<?php

/**
 * @file
 * Hooks provided by the Composer Manager module.
 */

use Monolog\Logger;
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
    'settings form' => 'mymodule_stream_handler_settings',
    'default settings' => array(
      'filepath' => 'public://monolog/drupal.log',
      'level' => Logger::INFO,
    ),
    'loader callback' => 'mymodule_stream_handler_loader',
  );

  return $handlers;
}

/**
 * Example loader callback to instantiate a handler.
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
 * @} End of "addtogroup hooks".
 */
