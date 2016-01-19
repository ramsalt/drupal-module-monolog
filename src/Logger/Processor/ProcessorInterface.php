<?php

namespace Drupal\monolog\Logger\Processor;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface {

  /**
   * @return string
   */
  public function getType();

  /**
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public function getDescription();
}
