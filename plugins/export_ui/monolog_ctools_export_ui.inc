<?php

/**
 * @file
 * CTools Export UI plugin for logging profile configurations.
 */

$plugin = array(
  'schema' => 'monolog_profile',
  'access' => 'administer site configuration',

  'menu' => array(
    'menu prefix' => 'admin/config/development/monolog',
    'menu item' => 'profile',
    'menu title' => 'Logging Profiles',
    'menu description' => 'Configure logging profiles.',
  ),

  'title singular' => t('profile'),
  'title plural' => t('profiles'),
  'title singular proper' => t('Logging profile'),
  'title plural proper' => t('Logging profiles'),

  'form' => array(
    'settings' => 'monolog_profile_form',
    'submit' => 'monolog_profile_form_submit',
  ),

  'handler' => array(
    'class' => 'monolog_export_ui',
    'parent' => 'ctools_export_ui',
  ),
);
