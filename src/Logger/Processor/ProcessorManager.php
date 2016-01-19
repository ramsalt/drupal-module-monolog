<?php

namespace Drupal\monolog\Logger\Processor;

/**
 * Class ProcessorManager
 */
class ProcessorManager implements ProcessorManagerInterface {

  private $processors;

  /**
   * {@inheritdoc}
   */
  public function addProcessor(ProcessorInterface $processor) {
    $this->processors[$processor->getType()] = $processor;
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessors() {
    return $this->processors;
  }

  /**
   * {@inheritdoc}
   */
  public function getProcessor($type) {
    return $this->processors[$type];
  }
}
