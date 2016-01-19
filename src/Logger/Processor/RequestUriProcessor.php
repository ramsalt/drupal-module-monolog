<?php

namespace Drupal\monolog\Logger\Processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Class RequestUriProcessor.php
 */
class RequestUriProcessor extends AbstractRequestProcessor implements ProcessorInterface {

  /**
   * @param array $record
   *
   * @return array
   */
  public function __invoke(array $record) {
    if ($request = $this->getRequest()) {
      $record['extra']['request_uri'] = $request->getUri();
    }

    return $record;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'request_uri';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return new TranslatableMarkup('The request URI for the page the event happened in.');
  }
}
