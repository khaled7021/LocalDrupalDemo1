<?php

namespace Drupal\rss_external_entities;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\external_entities\ExternalEntityTypeInterface;
use Drupal\rss_external_entities\Plugin\ExternalEntities\StorageClient\ExternalRssInterface;

/**
 * Class RssManager.
 *
 * @package Drupal\rss_external_entities
 */
class RssManager implements RssManagerInterface {

  /**
   * The logger
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The rss storage client.
   *
   * @var array
   */
  protected $rssStorageClient = [];

  /**
   * RssManager constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_channel_factory
   *   The logger channel factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(LoggerChannelFactoryInterface $logger_channel_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->logger = $logger_channel_factory->get('External Entities');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getLogger() {
    return $this->logger;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllRssStorageClient() {
    if (!$this->rssStorageClient) {
      $this->rssStorageClient = array_filter(
        array_map(function (ExternalEntityTypeInterface $entity_type) {
          if ($entity_type->getStorageClientId() !== ExternalRssInterface::STORAGE_CLIENT_ID) {
            return NULL;
          }
          return $entity_type->getStorageClient();
        }, $this->entityTypeManager->getStorage('external_entity_type')->loadMultiple())
      );
    }
    return $this->rssStorageClient;
  }

  /**
   * {@inheritdoc}
   */
  public function getRssStorageClient(string $external_entity_type) {
    // initialize.
    if (!$this->rssStorageClient) {
      $this->getAllRssStorageClient();
    }
    // Check the rss storage exists.
    if (!isset($this->rssStorageClient[$external_entity_type])) {
      throw new \Exception("The ${$external_entity_type} rss storage no-exists.");
    }
    return $this->rssStorageClient[$external_entity_type];
  }

}
