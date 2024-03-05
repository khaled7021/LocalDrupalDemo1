<?php

namespace Drupal\dn_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'field_example_colorpicker' widget.
 *
 * @FieldWidget(
 *   id = "field_colorpicker",
 *   module = "dn_field",
 *   label = @Translation("Color Picker"),
 *   field_types = {
 *     "field_dn_color_picker"
 *   }
 * )
 */
class ColorPickerWidget extends TextWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    //echo "dhggg";exit;
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['value'] += [
      '#suffix' => '<div class="field-example-colorpicker"></div>',
      '#attributes' => ['class' => ['edit-field-example-colorpicker']],
      '#attached' => [
        // Add Farbtastic color picker and javascript file to trigger the
        // colorpicker.
        'library' => [
          'core/jquery.farbtastic',
          'dn_field/colorpicker',
        ],
      ],
    ];

    return $element;
  }

}
