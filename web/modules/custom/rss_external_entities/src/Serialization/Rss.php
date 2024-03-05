<?php

namespace Drupal\rss_external_entities\Serialization;

use SimpleXMLElement;
use JsonSerializable;

/**
 * Class Feed.
 *
 * @package Drupal\rss_external_entities
 *
 * @see https://stackoverflow.com/a/64937070
 */
class Rss extends SimpleXMLElement implements JsonSerializable {

  /**
   * The namespace prefix to be added to all keys in
   * the final array that belong to a namespace.
   *
   * @var bool
   */
  public static $withNamespace = FALSE;

  /**
   * Json serialize.
   *
   * @return array|mixed|string
   */
  public function jsonSerialize() {
    $array = [];
    $attributes = [];

    // Get all namespaces.
    $namespaces = [NULL] + $this->getDocNamespaces(TRUE);

    // Json encode child elements if any. group on duplicate names as an array.
    foreach ($namespaces as $prefix => $namespace) {
      foreach ($this->attributes($namespace) as $name => $attribute) {
        if (static::$withNamespace && !empty($namespace)) {
          $name = $prefix . ":" . $name;
        }
        $attributes[$name] = $attribute;
      }

      foreach ($this->children($namespace) as $name => $element) {
        if (static::$withNamespace && !empty($namespace)) {
          $name = $prefix . ":" . $name;
        }
        if (isset($array[$name])) {
          if (!is_array($array[$name])) {
            $array[$name] = [$array[$name]];
          }
          $array[$name][] = $element;
        } else {
          $array[$name] = $element;
        }
      }
    }

    if (!empty($attributes)) {
      $array['@attributes'] = $attributes;
    }

    // json encode non-whitespace element simplexml text values.
    $text = trim($this);
    if (strlen($text)) {
      if ($array) {
        $array['@text'] = $text;
      } else {
        $array = $text;
      }
    }

    // return empty elements as NULL (self-closing or empty tags)
    if (!$array) {
      $array = NULL;
    }

    return $array;
  }

  /**
   * To array.
   *
   * @param bool $associative
   *
   * @return array
   */
  public function toArray($associative = TRUE) {
    return (array) json_decode(json_encode($this), $associative);
  }

}
