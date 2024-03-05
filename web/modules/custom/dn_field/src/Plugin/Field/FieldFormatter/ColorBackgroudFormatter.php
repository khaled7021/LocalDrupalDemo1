<?php

namespace Drupal\dn_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'field_example_color_background' formatter.
 *
 * @FieldFormatter(
 *   id = "field_sample_color_background",
 *   label = @Translation("Change the background of the output text"),
 *   field_types = {
 *     "field_dn_color_picker"
 *   }
 * )
 */
class ColorBackgroudFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('The content area color has been changed to @code', ['@code' => $item->value]),
        '#attributes' => [
          'style' => 'background-color: ' . $item->value,
        ],
      ];
    }
    return $elements;
  }

}
