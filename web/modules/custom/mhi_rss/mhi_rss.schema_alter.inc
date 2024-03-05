<?php

/**
 * @file
 * Primary module hooks for MHI Components module.
 */

/**
 * Implements hook_config_schema_info_alter().
 */
function mhi_rss_config_schema_info_alter(&$definitions) {
  if (isset($definitions['views.style.rss_fields'])) {
    $definitions['views.style.rss_fields']['mapping'] = [
      'channel' => [
        'type' => 'mapping',
        'label' => 'Channel',
        'mapping' => [
          'core' => [
            'type' => 'mapping',
            'label' => 'Channel elements: Core',
            'mapping' => [
              'mhi_rss' => [
                'type' => 'mapping',
                'label' => 'MHI rss',
                'mapping' => [
                  'linkDisplay' => [
                    'type' => 'label',
                    'label' => 'Link Display',
                  ],
                ],
              ],
              'views_rss_core' => [
                'type' => 'mapping',
                'label' => 'Views rss core',
                'mapping' => [
                  'description' => [
                    'type' => 'label',
                    'label' => 'Description',
                  ],
                  'language' => [
                    'type' => 'label',
                    'label' => 'Language',
                  ],
                  'category' => [
                    'type' => 'label',
                    'label' => 'Category',
                  ],
                  'image' => [
                    'type' => 'label',
                    'label' => 'Image',
                  ],
                  'copyright' => [
                    'type' => 'label',
                    'label' => 'Copyright',
                  ],
                  'managingEditor' => [
                    'type' => 'label',
                    'label' => 'Managing Editor',
                  ],
                  'webMaster' => [
                    'type' => 'label',
                    'label' => 'Web Master',
                  ],
                  'generator' => [
                    'type' => 'label',
                    'label' => 'Generator',
                  ],
                  'docs' => [
                    'type' => 'label',
                    'label' => 'Docs',
                  ],
                  'cloud' => [
                    'type' => 'label',
                    'label' => 'Cloud',
                  ],
                  'ttl' => [
                    'type' => 'label',
                    'label' => 'TTL',
                  ],
                  'skipHours' => [
                    'type' => 'label',
                    'label' => 'Skip Hours',
                  ],
                  'skipDays' => [
                    'type' => 'label',
                    'label' => 'Skip Days',
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
    ];
  }
}
