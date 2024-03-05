<?php

namespace Drupal\rss_external_entities\State;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\State\State;

/**
 * Class RssExternalEntitiesState.
 *
 * @package Drupal\rss_external_entities\State
 */
class RssExternalEntitiesState extends State {

  /**
   * {@inheritdoc}
   */
  public function __construct(KeyValueFactoryInterface $key_value_factory) {
    parent::__construct($key_value_factory);
    $this->keyValueStore = $key_value_factory->get('rss_external_entities');
  }

}
