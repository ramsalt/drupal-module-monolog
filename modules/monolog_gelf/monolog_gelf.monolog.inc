<?php

/**
 * @file
 * Monolog hook implementations.
 */

use Monolog\Handler\GelfHandler;
use Gelf\MessagePublisher;

/**
 * Implements hook_monolog_handler_info().
 */
function monolog_gelf_monolog_handler_info() {
  $handlers = array();

  $handlers['gelf'] = array(
    'label' => t('GELF Handler'),
    'description' => t('Logs records to a Graylog2 server.'),
    'loader callback' => 'monolog_gelf_handler_loader',
    'settings form' => 'monolog_gelf_handler_settings',
    'default settings' => array(
      'hostname' => '',
      'port' => 12201,
      'chunk_size' => 1420,
    ),
  );

  return $handlers;
}

/**
 * Monolog loader callback; Loads a GelfHandler handler.
 *
 * @return HandlerInterface
 */
function monolog_gelf_handler_loader($options) {
  $publisher = new MessagePublisher($options['hostname'], $options['port'], $options['chunk_size']);
  return new GelfHandler($publisher, $options['level'], $options['bubble']);
}

/**
 * Monolog settings form; Settings for the GelfHandler handler.
 */
function monolog_gelf_handler_settings(&$form, &$form_state, $profile, array $handler) {

  $form['hostname'] = array(
    '#title' => t('Hostname'),
    '#type' => 'textfield',
    '#default_value' => $handler['hostname'],
    '#description' => t('The hostname of the Graylog2 server.'),
    '#required' => TRUE,
  );

  $form['port'] = array(
    '#title' => t('Port'),
    '#type' => 'textfield',
    '#default_value' => $handler['port'],
    '#description' => t('The port that the server is listening on.'),
    '#required' => TRUE,
    '#size' => 6,
  );

  $form['chunk_size'] = array(
    '#title' => t('Chunk size'),
    '#type' => 'textfield',
    '#default_value' => $handler['chunk_size'],
    '#description' => t('The size of chunked messages in bytes. This allows larger messages to be broken up into smaller pieces.'),
    '#required' => TRUE,
    '#size' => 6,
  );
}
