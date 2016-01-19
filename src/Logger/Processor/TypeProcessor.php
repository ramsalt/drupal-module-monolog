<?php

namespace Drupal\monolog\Logger\Processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;

class TypeProcessor implements ProcessorInterface {

  /**
   * @param array $record
   *
   * @return array
   */
  public function __invoke(array $record) {
    $record['extra']['type'] = $record['channel'];

    return $record;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'type';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return new TranslatableMarkup('The type of message for this entry.');
  }
}
