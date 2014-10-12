<?php

/**
 * @file
 * Contains \Drupal\monolog\MonologHandlerInterface.
 */

namespace Drupal\monolog;

use Drupal\Component\Plugin\ConfigurablePluginInterface;

/**
 * Defines a monolog handler interface.
 *
 * @see \Drupal\monolog\Annotation\MonologHandler
 * @see \Drupal\monolog\MonologHandlerManager
 * @see plugin_api
 * @see https://github.com/Seldaek/monolog#handlers
 */
interface MonologHandlerInterface extends ConfigurablePluginInterface {

  /**
   * Loads the handler class.
   *
   * @todo Move this to container.
   */
  public function getHandlerClass();

}
