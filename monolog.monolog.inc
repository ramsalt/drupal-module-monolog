<?php

/**
 * @file
 * Monolog hook implementations.
 */

/**
 * Implements hook_monolog_handler_path().
 */
function monolog_monolog_handler_path() {
  return 'handlers';
}

/**
 * Implements hook_monolog_handler_info().
 */
function monolog_monolog_handler_info() {
  $handlers = array();

  $handlers['stream'] = array(
    'label' => t('Stream Handler'),
    'description' => t('Logs records into any PHP stream, use this for log files.'),
    'default settings' => array(
      'filepath' => 'public://monolog/drupal.log',
    ),
  );

  $handlers['rotating_file'] = array(
    'label' => t('Rotating File Handler'),
    'description' => t('Logs records to a file and creates one logfile per day. It will also delete files older than the "Max Files" settings.'),
    'default settings' => array(
      'filepath' => 'public://monolog/drupal.log',
      'max_files' => 0,
    ),
  );

  $handlers['syslog'] = array(
    'label' => t('Syslog Handler'),
    'description' => t('Logs records to the syslog.'),
    'default settings' => array(
      'ident' => 'drupal',
    ),
  );

  $handlers['chromephp'] = array(
    'label' => t('ChromePHP Handler'),
    'description' => t('Handler for ChromePHP, providing inline console messages within Chrome.'),
    'settings callback' => FALSE,
  );

  $handlers['firephp'] = array(
    'label' => t('FirePHP Handler'),
    'description' => t('Handler for FirePHP, providing inline console messages within FireBug.'),
    'settings callback' => FALSE,
  );

  return $handlers;
}

/**
 * Implements hook_default_monolog_profiles().
 */
function monolog_default_monolog_profiles() {
  $profiles = array();

  $profile = new stdClass();
  $profile->disabled = FALSE;
  $profile->api_version = 1;
  $profile->name = 'production';
  $profile->options = array(
    'label' => 'Production',
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

  $profile = new stdClass();
  $profile->disabled = FALSE;
  $profile->api_version = 1;
  $profile->name = 'development';
  $profile->options = array(
    'label' => 'Development',
    'handlers' => array(
      'stream' => array(
        'handler' => 'stream',
        'label' => 'Log file',
        'filepath' => 'public://monolog/drupal.log',
        'level' => 200,
        'bubble' => 1,
        'weight' => -50,
      ),
    ),
  );
  $profiles[$profile->name] = $profile;

  return $profiles;
}
