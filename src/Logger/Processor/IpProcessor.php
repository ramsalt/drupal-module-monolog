<?php

namespace Drupal\monolog\Logger\Processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Class IpProcessor
 */
class IpProcessor extends AbstractRequestProcessor implements ProcessorInterface {

  /**
   * @param array $record
   *
   * @return array
   */
  public function __invoke(array $record) {
    if ($request = $this->getRequest()) {
      $record['extra']['ip'] = $request->getClientIp();
    }

    return $record;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'ip';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return new TranslatableMarkup('The IP address where the request for the page came from.');
  }
}
