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
   * Returns the handler's label.
   *
   * @return string
   *   The handler's label.
   */
  public function label();

  /**
   * Returns the unique ID representing the handler.
   *
   * @return string
   *   The monolog handler UUID.
   */
  public function getUuid();

  /**
   * Returns the weight of the handler.
   *
   * @return int
   *   The weight of the handler.
   */
  public function getWeight();

  /**
   * Sets the weight for this image effect.
   *
   * @param int $weight
   *   The weight for this image effect.
   *
   * @return $this
   */
  public function setWeight($weight);
  
  /**
   * Loads the handler class.
   *
   * @todo Move this to container.
   * 
   * @return \Monolog\Handler\HandlerInterface
   */
  public function getHandlerClass();

}
