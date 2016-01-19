<?php

namespace Drupal\monolog\Logger\Processor;

/**
 * Interface ProcessorManager
 */
interface ProcessorManagerInterface {

  /**
   * @param $processor
   */
  public function addProcessor(ProcessorInterface $processor);

  /**
   * @return mixed
   */
  public function getProcessors();

  /**
   * @param string $type
   *  The processor type.
   *
   * @return \Drupal\monolog\Logger\Processor\ProcessorInterface
   */
  public function getProcessor($type);
}
