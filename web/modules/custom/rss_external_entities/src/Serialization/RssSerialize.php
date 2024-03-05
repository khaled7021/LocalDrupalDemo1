<?php

namespace Drupal\rss_external_entities\Serialization;

use Drupal\Component\Serialization\SerializationInterface;

/**
 * Class RssSerialize.
 *
 * @package Drupal\rss_external_entities\Serialization
 */
class RssSerialize implements SerializationInterface {

  /**
   * {@inheritdoc}
   */
  public static function encode($data) {
    // TODO: Revert to rss xml.
    return json_encode($data);
  }

  /**
   * Decode.
   *
   * @param string $raw
   * @param bool $with_namespace
   *   The namespace prefix to be added to all keys in
   *   the final array that belong to a namespace.
   * @param bool $associative
   *   When TRUE, returned objects will be converted into
   *   associative arrays.
   *
   * @return array|mixed
   */
  public static function decode($raw, $with_namespace = TRUE, $associative = TRUE) {
    $rss = new Rss($raw);
    $rss::$withNamespace = $with_namespace;
    return $rss->toArray($associative);
  }

  /**
   * {@inheritdoc}
   */
  public static function getFileExtension() {
    return 'rss';
  }

}
