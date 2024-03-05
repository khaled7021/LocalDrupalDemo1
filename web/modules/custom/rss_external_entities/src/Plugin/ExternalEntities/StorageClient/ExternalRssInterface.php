<?php

namespace Drupal\rss_external_entities\Plugin\ExternalEntities\StorageClient;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\external_entities\StorageClient\ExternalEntityStorageClientInterface;
use Throwable;

/**
 * Interface ExternalRssInterface.
 *
 * @package Drupal\rss_external_entities\Plugin\ExternalEntities\StorageClient
 */
interface ExternalRssInterface extends PluginFormInterface, CacheableDependencyInterface, ExternalEntityStorageClientInterface {

  /**
   * Storage client id.
   */
  const STORAGE_CLIENT_ID = 'external_rss';

  /**
   * Token type.
   */
  const TOKEN_TYPE = 'external_rss';

  /**
   * Mail key.
   */
  const MAIL_KEY = 'external_rss_notify';

  /**
   *  The state key prefix.
   */
  const STATE_KEY_PREFIX = 'external_rss';

  /**
   * The cache key prefix.
   */
  const CACHE_KEY_PREFIX = 'external_rss';

  /**
   * The index key.
   */
  const RSS_INDEX_KEY = 'rss_index';

  /**
   * Cron intervals.
   */
  const CRON_INTERVALS = [
    1,
    3,
    6,
    12,
    24,
    48,
    72,
    96,
    120,
    144,
    168,
  ];

  /**
   * Get state key.
   *
   * @return string
   */
  public function getStateKey();

  /**
   * Get cache key.
   *
   * @return string
   */
  public function getCacheKey();

  /**
   * Get the field mapping.
   *
   * @param $field_name
   *   The field name.
   *
   * @return string
   */
  public function getFieldMapping(string $field_name);

  /**
   * Get the endpoint.
   *
   * @return string
   */
  public function getEndpoint();

  /**
   * Get the item map key.
   *
   * @return string
   */
  public function getItemMapKey();

  /**
   * Is cron restore.
   *
   * @return bool
   */
  public function isCronRestore();

  /**
   * Get the cron restore interval.
   *
   * @return int
   */
  public function getCronRestoreInterval();

  /**
   * Get the external entity type.
   *
   * @return \Drupal\external_entities\ExternalEntityTypeInterface
   */
  public function getExternalEntityType();

  /**
   * Get last cron restore time.
   *
   * @return int
   */
  public function getLastCronRestoreTime();

  /**
   * Set last cron restore time.
   *
   * @param int $time
   *
   * @return $this
   */
  public function setLastCronRestoreTime(int $time);

  /**
   * Get xml.
   *
   * @return string
   *   The xml data.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getXml();

  /**
   * Set the stored xml.
   *
   * @param string $xml
   *   The xml.
   *
   * @return $this
   */
  public function setStoredXml(string $xml);

  /**
   * Get stored xml.
   *
   * @return string
   *  The stored xml data.
   */
  public function getStoredXml();

  /**
   * Send email.
   *
   * @param $subject
   * @param \Throwable|NULL $e
   *
   * @return mixed
   */
  public function sendEmail($subject, Throwable $e = NULL);

  /**
   * Get cache tags to invalidate.
   *
   * @return string[]
   */
  public function getCacheTagsToInvalidate();

}
