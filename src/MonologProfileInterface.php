<?php

/**
 * @file
 * Contains \Drupal\monolog\MonologProfileInterface.
 */

namespace Drupal\monolog;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginBagsInterface;

/**
 * Provides an interface defining a monolog profile config entity.
 */
interface MonologProfileInterface extends ConfigEntityInterface, EntityWithPluginBagsInterface {

  /**
   * {@inheritdoc}
   */
  public function getHandler($handler);

  /**
   * {@inheritdoc}
   */
  public function getHandlers();

  /**
   * {@inheritdoc}
   */
  public function addHandler(array $configuration);

  /**
   * {@inheritdoc}
   */
  public function getName();

  /**
   * {@inheritdoc}
   */
  public function setName($name);

  /**
   * {@inheritdoc}
   */
  public function isDisabled();

  /**
   * {@inheritdoc}
   */
  public function setDisabled($disabled);

}
