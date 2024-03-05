<?php

namespace Drupal\rss_external_entities\Query;

use Drupal\Core\Config\Entity\Query\Condition;

/**
 * Class RssCondition.
 *
 * @package Drupal\rss_external_entities\Query
 */
class RssQuery extends Condition implements RssQueryInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct() {  }

  /**
   * {@inheritdoc}
   */
  public function conditionMatch(array $condition, $value) {
    return $this->match($condition, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function conditionMatchArray(array $condition, array $data, array $needs_matching, array $parents = []) {
    return $this->matchArray($condition, $data, $needs_matching, $parents);
  }

  /**
   * {@inheritdoc}
   */
  public function querySort($value1, $value2, string $direction = 'ASC') {
    switch ($direction) {
      case 'ASC':
      case 'asc':
        return $value1 <=> $value2;

      case 'DESC':
      case 'desc':
        return $value2 <=> $value1;
    }

    return 0;
  }

}
