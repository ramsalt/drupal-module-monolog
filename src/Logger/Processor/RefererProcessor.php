<?php

namespace Drupal\monolog\Logger\Processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Class RefererProcessor
 */
class RefererProcessor extends AbstractRequestProcessor implements ProcessorInterface {

  /**
   * @param array $record
   *
   * @return array
   */
  public function __invoke(array $record) {
    if ($request = $this->getRequest()) {
      $record['extra']['referer'] = $request->headers->get('Referer', '');
    }

    return $record;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'referer';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return new TranslatableMarkup('The page that referred the user to the page where the event occurred.');
  }
}
