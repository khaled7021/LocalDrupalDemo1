<?php

namespace Drupal\external_entities\Plugin\ExternalEntities\StorageClient;

use GuzzleHttp\Exception\GuzzleException;

/**
 * External entities storage client based on a REST API.
 *
 * @ExternalEntityStorageClient(
 *   id = "wiki",
 *   label = @Translation("Wiki"),
 *   description = @Translation("Retrieves external entities from a Wikipedia API.")
 * )
 */
class Wiki extends Rest {

  /**
   * {@inheritdoc}
   */
  public function load($id) {
    try {
      $response = $this->httpClient->request(
        'GET',
        $this->configuration['endpoint'],
        [
          'headers' => $this->getHttpHeaders(),
          'query' => $this->getSingleQueryParameters($id),
        ]
      );
    }
    catch (GuzzleException $exception) {
      $logger = $this->loggerChannelFactory->get('external_entities');
      $logger->error('Error in Wiki::load. Message: ' . $exception->getMessage());
      return [];
    }

    $result = $this
      ->getResponseDecoderFactory()
      ->getDecoder($this->configuration['response_format'])
      ->decode($response->getBody());

    if (!empty($result['query']['pages'][$id])) {
      return $result['query']['pages'][$id];
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function query(array $parameters = [], array $sorts = [], $start = NULL, $length = NULL) {
    try {
      $response = $this->httpClient->request(
        'GET',
        $this->configuration['endpoint'],
        [
          'headers' => $this->getHttpHeaders(),
          'query' => $this->getListQueryParameters($parameters, $start, $length),
        ]
      );
    }
    catch (GuzzleException $exception) {
      $logger = $this->loggerChannelFactory->get('external_entities');
      $logger->error('Error in Wiki::query. Message: ' . $exception->getMessage());
      return [];
    }

    $format = $this->configuration['response_format'];
    $body = $response->getBody() . '';

    $results = $this
      ->getResponseDecoderFactory()
      ->getDecoder($format)
      ->decode($body);

    $items = [];
    if (!empty($results['query']['categorymembers'])) {
      $items = array_values($results['query']['categorymembers']);
    }

    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function getSingleQueryParameters($id, array $parameters = []) {
    return parent::getSingleQueryParameters($id, [
      [
        'field' => 'pageids',
        'value' => $id,
      ],
    ]);
  }

}
