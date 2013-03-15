<?php

/**
 * @file
 * Monolog hook implementations.
 */

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * Implements hook_monolog_handler_info().
 */
function monolog_monolog_handler_info() {
  $handlers = array();

  $handlers['stream_handler'] = array(
    'label' => t('Stream Handler'),
    'description' => t('Logs records into any PHP stream, use this for log files.'),
    'loader callback' => 'monolog_stream_handler_loader',
    'settings form' => 'monolog_stream_handler_settings',
    'default settings' => array(
      'filepath' => 'public://monolog/drupal.log',
    ),
  );

  $handlers['rotating_file'] = array(
    'label' => t('Rotating File Handler'),
    'description' => t('Logs records to a file and creates one logfile per day. It will also delete files older than the "Max Files" settings.'),
    'loader callback' => 'monolog_rotating_file_handler_loader',
    'settings form' => 'monolog_rotating_file_handler_settings',
    'default settings' => array(
      'filepath' => 'public://monolog/drupal.log',
      'max_files' => 0,
    ),
  );

  $handlers['syslog'] = array(
    'label' => t('Syslog Handler'),
    'description' => t('Logs records to the syslog.'),
    'loader callback' => 'monolog_syslog_handler_loader',
    'settings form' => 'monolog_syslog_handler_settings',
    'default settings' => array(
      'ident' => 'drupal',
    ),
  );

  $handlers['chromephp'] = array(
    'label' => t('ChromePHP Handler'),
    'description' => t('Handler for ChromePHP, providing inline console messages within Chrome.'),
    'loader callback' => 'monolog_chromephp_handler_loader',
  );

  $handlers['firephp'] = array(
    'label' => t('FirePHP Handler'),
    'description' => t('Handler for FirePHP, providing inline console messages within FireBug.'),
    'loader callback' => 'monolog_firephp_handler_loader',
  );

  return $handlers;
}

/**
 * Monolog loader callback; Loads a StreamHandler handler.
 *
 * @return HandlerInterface
 */
function monolog_stream_handler_loader($options) {
  $directory = dirname($options['filepath']);
  file_prepare_directory($directory);
  file_create_htaccess($directory, TRUE);
  return new StreamHandler($options['filepath'], $options['level'], $options['bubble']);
}

/**
 * Monolog settings form; Settings for the StreamHandler handler.
 */
function monolog_stream_handler_settings(&$form, &$form_state, $profile, array $handler) {
  $form['filepath'] = array(
    '#title' => 'Log file path',
    '#type' => 'textfield',
    '#default_value' => $handler['filepath'],
    '#description' => t('The path or URI that the log file will be written to.'),
  );
}

/**
 * Monolog loader callback; Loads a RotatingFileHandler handler.
 *
 * @return HandlerInterface
 */
function monolog_rotating_file_handler_loader($options) {
  $directory = dirname($options['filepath']);
  file_prepare_directory($directory);
  file_create_htaccess($directory, TRUE);
  return new RotatingFileHandler($options['filepath'], $options['max_files'], $options['level'], $options['bubble']);
}

/**
 * Monolog settings form; Settings for the RotatingFileHandler handler.
 */
function monolog_rotating_file_handler_settings(&$form, &$form_state, $profile, array $handler) {

  $form['filepath'] = array(
    '#title' => t('Log file path'),
    '#type' => 'textfield',
    '#default_value' => $handler['filepath'],
    '#description' => t('The path or URI that the log file will be written to.'),
  );

  $form['max_files'] = array(
    '#title' => t('Maximum number of files'),
    '#type' => 'textfield',
    '#default_value' => $handler['max_files'],
    '#description' => t('The maximal amount of files to keep (0 means unlimited).'),
    '#size' => 4,
  );
}

/**
 * Monolog loader callback; Loads a SyslogHandler handler.
 *
 * @return HandlerInterface
 */
function monolog_syslog_handler_loader($options) {
  return new SyslogHandler($options['ident'], LOG_USER, $options['level'], $options['bubble']);
}

/**
 * Monolog settings form; Settings for the SyslogHandler handler.
 */
function monolog_syslog_handler_settings(&$form, &$form_state, $profile, array $handler) {
  $form['ident'] = array(
    '#title' => t('Identity string'),
    '#type' => 'textfield',
    '#default_value' => $handler['ident'],
    '#description' => t('The string ident is added to each message.'),
  );
}

/**
 * Monolog loader callback; Loads a ChromePHPHandler handler.
 *
 * @return HandlerInterface
 */
function monolog_chromephp_handler_loader($options) {
  return new ChromePHPHandler($options['level'], $options['bubble']);
}

/**
 * Monolog loader callback; Loads a FirePHPHandler handler.
 *
 * @return HandlerInterface
 */
function monolog_firephp_handler_loader($options) {
  return new FirePHPHandler($options['level'], $options['bubble']);
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
      'stream_handler' => array(
        'handler' => 'stream_handler',
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
