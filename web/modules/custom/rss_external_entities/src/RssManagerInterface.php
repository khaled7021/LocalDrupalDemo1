<?php

namespace Drupal\rss_external_entities;

/**
 * Interface RssManagerInterface.
 *
 * @package Drupal\rss_external_entities
 */
interface RssManagerInterface {

  /**
   * Get the logger.
   *
   * @return \Psr\Log\LoggerInterface
   */
  public function getLogger();

  /**
   * Get the rss storage clients.
   *
   * @return \Drupal\rss_external_entities\Plugin\ExternalEntities\StorageClient\ExternalRssInterface[]
   */
  public function getAllRssStorageClient();

  /**
   * Get the rss storage client.
   *
   * @param string $external_entity_type
   *
   * @return \Drupal\rss_external_entities\Plugin\ExternalEntities\StorageClient\ExternalRssInterface
   *
   * @throws \Exception
   */
  public function getRssStorageClient(string $external_entity_type);

}
