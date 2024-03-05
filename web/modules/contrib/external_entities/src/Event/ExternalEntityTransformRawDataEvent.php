<?php

namespace Drupal\external_entities\Event;

use Drupal\external_entities\ExternalEntityStorageInterface;
use Drupal\Component\EventDispatcher\Event;

/**
 * Defines an external entity raw data transformation event.
 */
class ExternalEntityTransformRawDataEvent extends Event {

  /**
   * The external entity storage.
   *
   * @var ExternalEntityStorageInterface
   */
  protected $storage;

  /**
   * The endpoint raw data.
   *
   * @var array
   */
  protected $rawData;

  /**
   * Constructs a transform raw data event object.
   *
   * @param ExternalEntityStorageInterface $storage
   *   The external entity storage object.
   * @param array $raw_data
   *   The raw data.
   */
  public function __construct(ExternalEntityStorageInterface $storage, array $raw_data) {
    $this->storage = $storage;
    $this->rawData = $raw_data;
  }

  /**
   * Gets the external entity storage.
   *
   * @return ExternalEntityStorageInterface
   *   The storage client.
   */
  public function getStorage() {
    return $this->storage;
  }

  /**
   * Gets the raw data.
   *
   * @return array
   *   The raw data.
   */
  public function getRawData() {
    return $this->rawData;
  }

  /**
   * Sets the raw data.
   *
   * @param array $raw_data
   *   The raw data.
   */
  public function setRawData(array $raw_data) {
    $this->rawData = $raw_data;
  }

}
