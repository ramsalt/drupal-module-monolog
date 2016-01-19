<?php

namespace Drupal\monolog\Logger\Processor;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Class UidProcessor
 */
class UidProcessor implements ProcessorInterface {

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $accountProxy;

  /**
   * @param \Drupal\Core\Session\AccountProxyInterface $account_proxy
   */
  public function __construct(AccountProxyInterface $account_proxy) {
    $this->accountProxy = $account_proxy;
  }

  /**
   * @param array $record
   *
   * @return array
   */
  public function __invoke(array $record) {
    $record['extra']['uid'] = $this->accountProxy->id();
    $record['extra']['user'] = $this->accountProxy->getAccountName();

    return $record;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return 'uid';
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return new TranslatableMarkup('The user ID for the user who was logged in when the event happened.');
  }
}
