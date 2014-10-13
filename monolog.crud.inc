<?php

/**
 * @file
 * CRUD functions for logging profile configurations.
 */

/**
 * @defgroup monolog_invokers Monolog Hook Invoker Functions
 * @{
 * Functions that load information contained in hook implementations.
 */

/**
 * Loads all channel definitions from hook_monolog_channel_info()
 * implementations.
 *
 * @return array
 */
function monolog_channel_info_load_all() {
  $channels = &drupal_static(__FUNCTION__);
  if (!$channels) {
    $module_handler = \Drupal::moduleHandler();
    $channels = $module_handler->invokeAll('monolog_channel_info');
    $module_handler->alter('monolog_channel_info', $channels);
  }
  return $channels;
}

/**
 * Returns a single channel definition by its unique name.
 *
 * @param string $name
 *   The unique identifier of the channel.
 *
 * @see monolog_channel_load_all()
 */
function monolog_channel_info_load($name) {
  $channels = monolog_channel_info_load_all();
  return isset($channels[$name]) ? $channels[$name] : FALSE;
}

/**
 * @} End of "defgroup monolog_invokers".
 */
