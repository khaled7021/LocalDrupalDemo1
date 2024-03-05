<?php

namespace Drupal\external_entities\Plugin\ExternalEntities\StorageClient;

/**
 * External entities storage client based on a JSON:API.
 *
 * @ExternalEntityStorageClient(
 *   id = "jsonapi",
 *   label = @Translation("JSON:API"),
 *   description = @Translation("Retrieves external entities from a (Drupal) JSON:API source.")
 * )
 */
class JsonApi extends Rest {

  /**
   * {@inheritdoc}
   */
  public function query(array $parameters = [], array $sorts = [], $start = NULL, $length = NULL) {
    $results = parent::query($parameters, $sorts, $start, $length);

    return $results['data'] ?? $results;
  }

  /**
   * {@inheritdoc}
   */
  public function load($id): mixed {
    $result = parent::load($id);

    // Included contains exposed relationship date for fields in JSON:API.
    if (isset($result['data'], $result['included'])) {
      $result['data']['included'] = $result['included'];
    }

    return $result['data'] ?? $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getHttpHeaders(): array {
    $headers = parent::getHttpHeaders();
    // JSON:API required the following Accept header.
    $headers['Accept'] = 'application/vnd.api+json';

    return $headers;
  }

}

