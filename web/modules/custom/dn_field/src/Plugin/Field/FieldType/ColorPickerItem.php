<?php
namespace Drupal\dn_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'dn_field' field type.
 *
 * @FieldType(
 *   id = "field_dn_color_picker",
 *   label = @Translation("DN Color picker"),
 *   module = "dn_field",
 *   description = @Translation("Demonstrates a field composed of an RGB color."),
 *   default_widget = "field_colorpicker",
 *   default_formatter = "field_sample_color_background"
 * )
 */

class ColorPickerItem extends FieldItemBase{

    /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Hex value'));

    return $properties;
  }

}

