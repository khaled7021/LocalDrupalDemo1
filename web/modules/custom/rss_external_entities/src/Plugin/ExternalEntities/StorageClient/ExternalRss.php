<?php

namespace Drupal\rss_external_entities\Plugin\ExternalEntities\StorageClient;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Utility\Token;
use Drupal\external_entities\ExternalEntityInterface;
use Drupal\external_entities\StorageClient\ExternalEntityStorageClientBase;
use Drupal\external_entities\Plugin\PluginFormTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\external_entities\ResponseDecoder\ResponseDecoderFactoryInterface;
use Drupal\rss_external_entities\Query\RssQueryInterface;
use Drupal\rss_external_entities\RssManagerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Throwable;

/**
 * External entities storage client based on a REST API.
 *
 * @ExternalEntityStorageClient(
 *   id = "external_rss",
 *   label = @Translation("External RSS"),
 *   description = @Translation("Retrieves external entities from a RSS API.")
 * )
 */
class ExternalRss extends ExternalEntityStorageClientBase implements ExternalRssInterface {

  use PluginFormTrait;

  /**
   * The http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The state.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The rss manager.
   *
   * @var \Drupal\rss_external_entities\RssManagerInterface
   */
  protected $rssManager;

  /**
   * The rss query.
   *
   * @var \Drupal\rss_external_entities\Query\RssQueryInterface
   */
  protected $rssQuery;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The token.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The endpoint
   *
   * @var string
   */
  protected $endpoint;

  /**
   * The item map key.
   *
   * @var string
   */
  protected $itemMapKey;

  /**
   * The cron restore.
   *
   * @var bool
   */
  protected $cronRestore;

  /**
   * The cron restore interval.
   *
   * @var int
   */
  protected $cronRestoreInterval;

  /**
   * Constructs a Rest object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   * @param \Drupal\external_entities\ResponseDecoder\ResponseDecoderFactoryInterface $response_decoder_factory
   *   The response decoder factory service.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The http client.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state.
   * @param \Drupal\rss_external_entities\RssManagerInterface $rss_manager
   *   The rss manager.
   * @param \Drupal\rss_external_entities\Query\RssQueryInterface $rss_query
   *   The rss query.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Utility\Token $token
   *   The token.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TranslationInterface $string_translation, ResponseDecoderFactoryInterface $response_decoder_factory, ClientInterface $http_client, ModuleHandlerInterface $module_handler, CacheBackendInterface $cache, StateInterface $state, RssManagerInterface $rss_manager, RssQueryInterface $rss_query, DateFormatterInterface $date_formatter, MailManagerInterface $mail_manager, Token $token, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $string_translation, $response_decoder_factory);
    $this->logger = $rss_manager->getLogger();
    $this->httpClient = $http_client;
    $this->moduleHandler = $module_handler;
    $this->cache = $cache;
    $this->state = $state;
    $this->rssManager = $rss_manager;
    $this->rssQuery = $rss_query;
    $this->dateFormatter = $date_formatter;
    $this->mailManager = $mail_manager;
    $this->token = $token;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('string_translation'),
      $container->get('external_entities.response_decoder_factory'),
      $container->get('http_client'),
      $container->get('module_handler'),
      $container->get('cache.default'),
      $container->get('rss_external_entities.state'),
      $container->get('rss_external_entities.rss_manager'),
      $container->get('rss_external_entities.rss_query'),
      $container->get('date.formatter'),
      $container->get('plugin.manager.mail'),
      $container->get('token'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getStateKey() {
    return static::STATE_KEY_PREFIX . ':' . $this->getExternalEntityType()->id();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheKey() {
    return static::CACHE_KEY_PREFIX . ':' . $this->getExternalEntityType()->id();
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldMapping(string $field_name) {
    return $this->externalEntityType->getFieldMapperConfig()['field_mappings'][$field_name] ?? $field_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndpoint() {
    if (!$this->endpoint) {
      $this->endpoint = trim($this->getConfiguration()['endpoint'] ?? '');
    }
    return $this->endpoint;
  }

  /**
   * {@inheritdoc}
   */
  public function getItemMapKey() {
    if (!$this->itemMapKey) {
      $this->itemMapKey = trim($this->getConfiguration()['item_map_key'] ?? '');
    }
    return $this->itemMapKey;
  }

  /**
   * {@inheritdoc}
   */
  public function isCronRestore() {
    if (!is_bool($this->cronRestore)) {
      $this->cronRestore = (bool) $this->getConfiguration()['cron']['cron_restore'] ?? FALSE;
    }
    return $this->cronRestore;
  }

  /**
   * {@inheritdoc}
   */
  public function getCronRestoreInterval() {
    if (!is_int($this->cronRestoreInterval)) {
      $this->cronRestoreInterval = (int) $this->getConfiguration()['cron']['cron_restore_interval'] ?? 0;
    }
    return $this->cronRestoreInterval;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCronRestoreTime() {
    return (int) $this->state->get($this->getStateKey() . '.last_cron_restore', 0);
  }

  /**
   * {@inheritdoc}
   */
  public function setLastCronRestoreTime(int $time) {
    $this->state->set($this->getStateKey() . '.last_cron_restore', $time);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getExternalEntityType() {
    return $this->externalEntityType;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'endpoint' => NULL,
      'item_map_key' => 'channel.item',
      'pager' => [
        'default_limit' => 100,
      ],
      'cron' => [
        'cron_restore' => FALSE,
        'cron_restore_interval' => 0,
      ],
      'email' => [
        'send' => '',
        'template' => '',
      ],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['endpoint'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Endpoint'),
      '#required' => TRUE,
      '#default_value' => $config['endpoint'],
    ];

    // A string that maps to a key within the configuration data.
    $form['item_map_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Item Map key'),
      '#default_value' => $config['item_map_key'],
    ];

    // Pager.
    $form['pager'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Pager settings'),
    ];

    $form['pager']['default_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Default number of items per page'),
      '#default_value' => $config['pager']['default_limit'],
    ];

    // Cron.
    $form['cron'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Cron settings'),
    ];

    $form['cron']['cron_restore'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Restore the rss during cron runs'),
      '#description' => $this->t('Uncheck this if you intend to only restore the rss manually or via drush.'),
      '#default_value' => $config['cron']['cron_restore'],
    ];

    $interval_options = array_flip(static::CRON_INTERVALS);
    $interval_options = [0 => $this->t('On every cron run')] + array_map(function ($value, $key) {
      return $this->dateFormatter->formatInterval($key * 60 * 60);
    }, $interval_options, array_keys($interval_options));

    $form['cron']['cron_restore_interval'] = [
      '#type' => 'select',
      '#title' => $this->t('Rss restore interval'),
      '#description' => $this->t('The rss will be restore according to this interval.'),
      '#default_value' => $config['cron']['cron_restore_interval'],
      '#options' => $interval_options,
      '#states' => [
        'visible' => [':input[name="storage[storage_client_config][cron][cron_restore]"]' => ['checked' => TRUE]],
      ],
    ];

    // Email handler.
    $form['email'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Email settings'),
      '#description' => $this->t('Email addresses to notify when restore are unavailable'),
    ];

    $form['email']['send'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#default_value' => $config['email']['send'],
    ];

    $form['email']['template'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Email Template'),
      '#default_value' => $config['email']['template'],
    ];

    $form['email']['token_help'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['external_rss'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $form_state->setValue('endpoint', rtrim($form_state->getValue('endpoint'), '/'));
    $this->setConfiguration($form_state->getValues());
  }

  /**
   * {@inheritdoc}
   */
  public function delete(ExternalEntityInterface $entity) {
    # pass
  }

  /**
   * {@inheritdoc}
   */
  public function save(ExternalEntityInterface $entity) {
    # pass
  }

  /**
   * {@inheritdoc}
   */
  public function query(array $parameters = [], array $sorts = [], $start = NULL, $length = NULL) {
    $configuration = $this->getConfiguration();

    // Return all data if the arguments all empty.
    if (!$parameters && !$sorts && !$start && !$length) {
      return $this->requestXmlDataAll();
    }

    // Handle paging query.
    $start = $start ?: 0;
    $limit = $length ?: $configuration['pager']['default_limit'];

    // Get data.
    if (count($parameters) || count($sorts)) {
      $has_parameters_sorts = TRUE;
      $results = $this->requestXmlDataAll();
    }
    else {
      $has_parameters_sorts = FALSE;
      $results = $this->requestXmlDataMultiple($start, $limit);
    }

    // Handle parameters & sorts.
    if ($has_parameters_sorts) {
      // Handle parameters.
      foreach ($parameters as $parameter) {
        $field = $parameter['field'] ?? NULL;

        if (is_null($field)) {
          continue;
        }

        if (is_array($field)) {
          $origin_field = $this->getFieldMapping($field['value']);
        }
        else {
        $origin_field = $this->getFieldMapping($field)['value'];
        }

        $results = array_filter($results, function ($data) use ($origin_field, $parameter) {
          if (!isset($data[$origin_field])) {
            return FALSE;
          }
          return $this->rssQuery->conditionMatch($parameter, $data[$origin_field]);
        });
      }

      // Handle sorts.
      uasort($results, function ($data1, $data2) use ($sorts) {
        $return = 0;
        foreach ($sorts as $sort) {

          $field = $sort['field'] ?? NULL;

          if (!$field) {
            continue;
          }

          $origin_field = $this->getFieldMapping($field);

          $return = $this->rssQuery->querySort($data1[$origin_field] ?? '', $data2[$origin_field] ?? '', $sort['direction'] ?? 'ASC');

          // Stop loop when the return isn't zero.
          if ($return !== 0) {
            break;
          }
        }

        return $return;
      });

      // Handle paging.
      $results = array_slice($results, $start, $limit, TRUE);
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids = NULL) {
    $data = [];

    if (!empty($ids) && is_array($ids)) {
      foreach ($ids as $id) {
        $data[$id] = $this->load($id);
      }
    }
    else {
      $data = $this->query();
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  public function load($id) {
    $data = $this->query([
      [
        'field' => $this->getFieldMapping('id'),
        'value' => $id,
        'operator' => '='
      ]
    ]);
    return reset($data);
  }

  /**
   * {@inheritdoc}
   */
  public function getXml() {
    return $this->httpClient->request('GET', $this->getEndpoint())->getBody() . '';
  }

  /**
   * {@inheritdoc}
   */
  public function setStoredXml(string $xml) {
    $this->state->set($this->getStateKey() . '.xml', trim($xml));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStoredXml() {
    $xml = $this->state->get($this->getStateKey() . '.xml');

    if (!$xml) {
      try {
        $xml = $this->getXml();
      }
      catch (\Throwable $e) {
        $xml = '';
      }

      $this->setStoredXml($xml);
    }

    return $xml;
  }

  /**
   * {@inheritdoc}
   */
  public function sendEmail($subject = NULL, Throwable $e = NULL) {
    if (!$subject) {
      $subject = $e ?
        $this->t('Failed to restore the @label rss', ['@label' => $this->getExternalEntityType()->label()]) :
        $this->t('Restore the @label rss successfully', ['@label' => $this->getExternalEntityType()->label()]);
    }

    $email = $this->getConfiguration()['email']['send'];
    $email_template = $this->getConfiguration()['email']['template'];

    if (empty($email) || empty($subject) || empty($email_template)) {
      $this->logger->warning('Failed to send email, the email address/subject/template was missing.');
      return;
    }

    $email_message = $this->token->replace($email_template, ['storage_client' => $this, 'error' => $e]);

    $this->mailManager->mail(
      'rss_external_entities',
      static::MAIL_KEY,
      $email,
      $this->currentUser->getPreferredLangcode(),
      [
        'external_entity_type' => $this->getExternalEntityType()->id(),
        'email' => $email,
        'account' => $email,
        'subject' => $subject,
        'body' => $email_message,
      ]
    );
  }

  /**
   * Convert xml to array.
   *
   * @return array
   *  The stored xml data.
   */
  protected function getXmlArray() {
    $xml = $this->getStoredXml();

    $xml_data = $xml ? $this->responseDecoderFactory->getDecoder('rss')->decode($xml) : [];

    // Get the data by item map key.
    $item_map_key = $this->getItemMapKey();
    $parts = empty($item_map_key) ? [] : explode('.', $item_map_key);

    if (count($parts) == 1) {
      $xml_data = (array) $xml_data[$item_map_key] ?? [];
    }
    elseif (count($parts) > 1) {
      $xml_data = (array) NestedArray::getValue($xml_data, $parts, $key_exists);
    }

    // Generate id field.
    foreach ($xml_data as $index => &$xml_datum) {
      $xml_datum[static::RSS_INDEX_KEY] = $index + 1;
    }
    $xml_data = array_column($xml_data, NULL, static::RSS_INDEX_KEY);

    // Hook: hook_external_rss_xml_data().
    $this->moduleHandler->alter('external_rss_xml_data', $this, $xml_data);

    return $xml_data;
  }

  /**
   * Get formatted xml data.
   *
   * @return array
   */
  protected function requestXmlDataAll() {
    $cache_key = $this->getCacheKey();

    $data = $this->cache->get($cache_key) ?: [];
    $data = $data ? $data->data : [];

    if (!$data) {
      $data = $this->getXmlArray();
      if ($data) {
        $this->cache->set($cache_key, $data, $this->getCacheMaxAge(), $this->getCacheTags());
      }
    }

    return $data;
  }

  /**
   * Get formatted xml data.
   *
   * @param int $start
   *   The start.
   * @param int $limit
   *   The limit.
   *
   * @return array
   */
  protected function requestXmlDataMultiple(int $start, int $limit) {
    return array_slice($this->requestXmlDataAll(), $start, $limit, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(
      ['user.permissions', 'url.site', 'languages'],
      $this->getExternalEntityType()->getCacheContexts()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $cache_tags = Cache::mergeTags([
      static::CACHE_KEY_PREFIX,
      static::CACHE_KEY_PREFIX . ':' . $this->getPluginId()
    ], $this->getCacheTagsToInvalidate());

    return Cache::mergeTags(
      $cache_tags,
      $this->getExternalEntityType()->getCacheTags()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate() {
    return Cache::mergeTags(
      $this->getExternalEntityType()->getCacheTagsToInvalidate(),
      [
        static::CACHE_KEY_PREFIX . ':' . $this->getPluginId() . ':' . $this->getExternalEntityType()->id(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::mergeMaxAges(
      $this->getExternalEntityType()->getPersistentCacheMaxAge(),
      $this->getExternalEntityType()->getCacheMaxAge()
    );
  }

}
