<?php

/**
 * @file
 * CRUD functions for logging profile configurations.
 */

/**
 * @defgroup monolog_crud Monolog CRUD Functions
 * @{
 * CRUD functions for logging profile configurations.
 */

/**
 * Gets all profile configurations.
 *
 * @return array
 *   An associative array keyed by the machine name of the profile to the
 *   configuration object.
 */
function monolog_profile_load_all() {
  $profiles = &drupal_static(__FUNCTION__);
  if (NULL === $profiles) {
    ctools_include('export');
    $profiles = ctools_export_crud_load_all('monolog_profile');
    foreach ($profiles as $name => $profile) {
      uksort($profile->options['handlers'], 'drupal_sort_weight');
    }
  }
  return $profiles;
}

/**
 * Returns an empty profile configuration object with defaults.
 *
 * @return stdClass
 */
function monolog_profile_new() {
  ctools_include('export');
  $profile = ctools_export_crud_new('monolog_profile');
  $profile->options = array(
    'label' => '',
    'handlers' => array(),
  );
  return $profile;
}

/**
 * Loads a profile configuration object.
 *
 * @param string $name
 *   The machine name of the profile.
 *
 * @return array|FALSE
 *   The configuration options, FALSE if the profile doesn't exist.
 */
function monolog_profile_load($name) {
  $profiles = &drupal_static(__FUNCTION__, array());
  if (!isset($profiles[$name])) {
    ctools_include('export');
    if ($profiles[$name] = ctools_export_crud_load('monolog_profile', $name)) {
      uksort($profiles[$name]->options['handlers'], 'drupal_sort_weight');
    }
  }
  return $profiles[$name] ?: FALSE;
}

/**
 * Saves a profile's configuration settings.
 *
 * @param stdClass $profile
 *   The profile configuration being saved.
 *
 * @return boolean
 */
function monolog_profile_save($profile) {
  $sucess = ctools_export_crud_save('monolog_profile', $profile);
  $args = array('@name' => $profile->name);
  if ($sucess) {
    drupal_static_reset('monolog_profile_load');
    drupal_static_reset('monolog_profile_load_all');
    watchdog('monolog', 'Logging profile saved: @name', $args);
  }
  else {
    watchdog('monolog', 'Error saving logging profile: @name', $args, WATCHDOG_ERROR);
  }
  return $sucess;
}

/**
 * Deletes a profile's configuration settings.
 *
 * @param stdClass $profile
 *   The profile configuration being deleted.
 *
 * @return boolean
 */
function monolog_profile_delete($profile) {
  // @todo CTools doesn't return the status. Figure out how to get it.
  ctools_export_crud_delete('monolog_profile', $profile);
  drupal_static_reset('monolog_profile_load');
  drupal_static_reset('monolog_profile_load_all');
  $args = array('@name' => $profile->name);
  watchdog('monolog', 'Logging profile deleted: @name', $args);
  return TRUE;
}

/**
 * Loads a logging profile's handler configuration.
 *
 * @param int $handler_name
 *   The machine name of the handler configuration.
 * @param string $profile_name
 *   The machine name of logging profile.
 *
 * @return array|FALSE
 *   The configuration options, FALSE if the profile or handler doesn't exist.
 *
 * @see monolog_profile_load()
 */
function monolog_handler_load($handler_name, $profile_name) {
  $profile = monolog_profile_load($profile_name);
  if ($profile && isset($profile->options['handlers'][$handler_name])) {
    return $profile->options['handlers'][$handler_name];
  }
  return FALSE;
}

/**
 * @} End of "defgroup monolog_crud".
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
    $channels = module_invoke_all('monolog_channel_info');
    drupal_alter('monolog_channel_info', $channels);
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
 * Loads all handler definitions from hook_monolog_handler_info()
 * implementations.
 *
 * @return array
 */
function monolog_handler_info_load_all() {
  $handlers = &drupal_static(__FUNCTION__);
  if (!$handlers) {
    composer_manager_register_autoloader();
    $handlers = module_invoke_all('monolog_handler_info');
    drupal_alter('monolog_handler_info', $handlers);
  }
  return $handlers;
}

/**
 * Returns a single handler definition by its unique name.
 *
 * @param string $name
 *   The unique identifier of the handler.
 *
 * @see monolog_handler_load_all()
 */
function monolog_handler_info_load($name) {
  $handlers = monolog_handler_info_load_all();
  return isset($handlers[$name]) ? $handlers[$name] : FALSE;
}

/**
 * @} End of "defgroup monolog_invokers".
 */
