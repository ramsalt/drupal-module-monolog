<?php

/**
 * @file
 * Contains \Drupal\monolog\MonologProfileListBuilder.
 */

namespace Drupal\monolog;

use Drupal\Component\Utility\String;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a listing of monolog profile entities.
 *
 * @see \Drupal\monolog\Entity\MonologProfile
 */
class MonologProfileListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'label' => $this->t('Profile'),
      'handlers' => $this->t('Handlers'),
    ];

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    // @todo Make a theme function.
    $label = String::checkPlain($entity->label());
    $machine_name = '<small>' . $this->t('(Machine name: @name)', array('@name' => $entity->id())) . '</small>';
    $handlers = [];
    foreach ($entity->getHandlers()->sort() as $handler) {
      $handlers[] = $handler->label();
    }
    $row = [
      'label' => $label . ' ' . $machine_name,
      'handlers' => join(', ', $handlers),
    ];

    if (empty($row['handlers'])) {
      $row['handlers'] = '<em>No handlers</em>';
    }

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['#prefix'] = $this->t('<p>A <strong>profile</strong> is a collection of handlers that process the record.</p><p>Common examples of handlers are a <em>syslog handler</em> that routes records to the syslog and a <em>stream wrapper handler</em> that writes records to files and other streams.</p>');

    $build['#empty'] = $this->t('There are no logging channels. Add one by clicking the "Add channel" link above.');
    $build['#caption'] = $this->t('Logging Profiles');

    return $build;
  }

}
