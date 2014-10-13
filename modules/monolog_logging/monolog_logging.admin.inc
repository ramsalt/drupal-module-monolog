<?php

/**
 * @file
 * Administrative settings for the Monolog Logging module.
 */

/**
 * Returns HTML for the type map table.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: The FAPI element being themed.
 *
 * @ingroup themeable
 */
function theme_monolog_logging_map_table(array $variables) {
  $output = '';

  $header = array(
    'type' => t('Watchdog Types'),
    'channel' => t('Monolog Channel'),
    'operations' => t('Operations'),
  );

  $rows = array();
  $types = array();
  foreach ($types as $type) {

    $rows[] = array(
      'type' => '',
      'channel' => drupal_render($variables['element']['type'][$type]['profile']),
      'operations' => '',
    );
  }

  $output .= drupal_render_children($variables['element']);
  $output .= theme('table', array(
    'caption' => t('Watchdog type mappings'),
    'header' => $header,
    'rows' => $rows,
    'empty' => t('There are no watchdog types mapped to Monolog channels.'),
    'attributes' => array('id' => 'monolog-logging-map-table'),
  ));

  return $output;
}
