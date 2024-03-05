<?php

namespace Drupal\rss_external_entities\Query;

/**
 * Interface RssQueryInterface.
 *
 * @package Drupal\rss_external_entities\Query
 */
interface RssQueryInterface {

  /**
   * Perform the actual matching.
   *
   * @param array $condition
   *   The condition array as created by the condition() method.
   * @param string $value
   *   The value to match against.
   *
   * @return bool
   *   TRUE when matches else FALSE.
   */
  public function conditionMatch(array $condition, $value);

  /**
   * Matches for an array representing one or more config paths.
   *
   * @param array $condition
   *   The condition array as created by the condition() method.
   * @param array $data
   *   The config array or part of it.
   * @param array $needs_matching
   *   The list of config array keys needing a match. Can contain config keys
   *   and the * wildcard.
   * @param array $parents
   *   The current list of parents.
   *
   * @return bool
   *   TRUE when the condition matched to the data else FALSE.
   */
  public function conditionMatchArray(array $condition, array $data, array $needs_matching, array $parents = []);

  /**
   * Query sort.
   *
   * @param mixed $value1
   *  The data.
   * @param mixed $value2
   *  The data.
   * @param string $direction
   *  The direction.
   *
   * @return int
   */
  public function querySort($value1, $value2, string $direction = 'ASC');

}
