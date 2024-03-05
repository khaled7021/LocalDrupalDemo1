<?php

namespace Drupal\rss_external_entities\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rss_external_entities\RssManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;

/**
 * Provides a block with external rss.
 *
 * @Block(
 *   id = "external_rss",
 *   admin_label = @Translation("External Rss"),
 *   category = @Translation("External Entities")
 * )
 */
class ExternalRss extends BlockBase implements ContainerFactoryPluginInterface {

  const NUMBER_RANGE = ['#min' => 3, '#max' => 4, '#default_value' => 3];

  /**
   * The rss manager.
   *
   * @var \Drupal\rss_external_entities\RssManagerInterface
   */
  protected $rssManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * ExternalRss constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param $plugin_id
   *   The plugin id.
   * @param $plugin_definition
   *   The plugin definition.
   * @param \Drupal\rss_external_entities\RssManagerInterface $rss_manager
   *   The rss manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RssManagerInterface $rss_manager, EntityTypeManagerInterface $entity_type_manager, FileUrlGeneratorInterface $file_url_generator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->rssManager = $rss_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('rss_external_entities.rss_manager'),
      $container->get('entity_type.manager'),
      $container->get('file_url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'storage' => '',
      'description' => ['value' => '', 'format' => ''],
      'numbers_shown' => static::NUMBER_RANGE['#default_value'],
      'items_per_row' => static::NUMBER_RANGE['#default_value'],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $storages = $this->rssManager->getAllRssStorageClient();
    array_walk($storages, function (&$value) {
      $value = $value->getExternalEntityType()->getLabel();
    });

    $form['storage'] = [
      '#type' => 'select',
      '#options' => $storages,
      '#title' => $this->t('Data storage'),
      '#description' => $this->t('Which data storage you want to shown ?'),
      '#default_value' => $config['storage'],
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#default_value' => $config['description']['value'],
      '#format' => $config['description']['format'],
    ];

    $form['numbers_shown'] = [
      '#type' => 'number',
      '#title' => $this->t('Numbers Shown'),
      '#description' => $this->t('How many items to display in "external rss" list. (Range: @min-@max, Default: @default_value)', [
        '@min' => static::NUMBER_RANGE['#min'],
        '@max' => static::NUMBER_RANGE['#max'],
        '@default_value' => static::NUMBER_RANGE['#default_value'],
      ]),
      '#attributes' => [
        'style' => 'width: 4em;',
      ],
      '#default_value' => $config['numbers_shown'],
    ] + static::NUMBER_RANGE;

    $form['items_per_row'] = [
      '#type' => 'number',
      '#title' => $this->t('Items per row'),
      '#description' => $this->t('How many items to display on one line. (Range: @min-@max, Default: @default_value)', [
        '@min' => static::NUMBER_RANGE['#min'],
        '@max' => static::NUMBER_RANGE['#max'],
        '@default_value' => static::NUMBER_RANGE['#default_value'],
      ]),
      '#attributes' => [
        'style' => 'width: 4em;',
      ],
      '#default_value' => $config['items_per_row'],
    ] + static::NUMBER_RANGE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['storage'] = $values['storage'];
    $this->configuration['description'] = $values['description'];
    $this->configuration['numbers_shown'] = $values['numbers_shown'];
    $this->configuration['items_per_row'] = $values['items_per_row'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $plugin_id = $this->getPluginId();
    $config = $this->getConfiguration();

    $external_entity_type = $config['storage'];
    $numbers_shown = $config['numbers_shown'];
    $items_per_row = $config['items_per_row'];

    // Get view builder.
    $view_builder = $this->entityTypeManager->getViewBuilder($external_entity_type);

    // Get entities.
    $entity_storage = $this->entityTypeManager->getStorage($external_entity_type);
    $entities = array_map(function ($id) use ($entity_storage) {
      return $entity_storage->load($id);
    }, $entity_storage->getQuery()->range(0, $numbers_shown)->accessCheck(TRUE)->execute());

    // Assemble build array.
    $build = [
      '#attributes' => [
        'class' => [
          str_replace('_', '-', $plugin_id),
          str_replace('_', '-', $plugin_id . '--storage--' . $external_entity_type),
          str_replace('_', '-', $plugin_id . '--items-per-row--' . $items_per_row),
        ],
      ],
      '#configuration' => $this->getConfiguration(),
      '#plugin_id' => $plugin_id,
      '#base_plugin_id' => $this->getBaseId(),
      '#derivative_plugin_id' => $this->getDerivativeId(),
      '#external_entity_type_id' => $external_entity_type,
      'description' => [
        '#type' => 'processed_text',
        '#format' => $config['description']['format'],
        '#text' => $config['description']['value'],
      ],
      'items' => array_map(function ($entity) use ($view_builder) {
        return $view_builder->view($entity);
      }, $entities),
    ];

    // Build cache.
    $rss_storage = $this->rssManager->getRssStorageClient($external_entity_type);

    $cacheable_metadata = CacheableMetadata::createFromRenderArray($build)->addCacheableDependency($rss_storage);
    foreach ($entities as $entity) {
      $cacheable_metadata->addCacheableDependency($entity);
    }
    $cacheable_metadata->applyTo($build);

    $build['#cache']['keys'] = [
      $external_entity_type,
      'numbers_shown:' . $numbers_shown,
      'items_per_row:' . $items_per_row,
    ];

    return $build;
  }

}
