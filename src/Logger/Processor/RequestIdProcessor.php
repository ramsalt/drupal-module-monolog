<?php

namespace Drupal\monolog\Logger\Processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Class RequestIdProcessor
 */
class RequestIdProcessor implements ProcessorInterface {

  /**
   * @param array $record
   *
   * @return array
   */
  public function __invoke(array $record) {
    $record['extra']['request_id'] = monolog_request_id();

    return $record;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'request_id';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return new TranslatableMarkup('A unique identifier for the page request or PHP process to logically group log messages.');
  }
}
