<?php

namespace Drupal\custom_rest_api\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "rest_sample",
 *   label = @Translation("Rest sample"),
 *   uri_paths = {
 *     "canonical" = "/rest/get/node/data"
 *   }
 * )
 */
class RestSample extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {

     // Implement the logic of your REST Resource here.
     $nids = [16, 17, 18];
     $nids = \Drupal::entityQuery('node')->condition('nid', $nids, 'IN')->accessCheck(TRUE)->execute();
     if($nids) {
       $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
       $data[] = [];
       foreach ($nodes as $key => $value) {
        if($value->getType() == 'custom_basic_page') {
          $node_field_body_text = !empty($value->get('field_body_text')->getValue()) ? $value->get('field_body_text')->getValue() : '';
          if (!empty($node_field_body_text)) {
            foreach ($node_field_body_text as $node_field_body_text_value) {
              $para_field_body_text = Paragraph::load($node_field_body_text_value['target_id']);
              $para_field_body_text_value = !empty($para_field_body_text->field_body->value) ? $para_field_body_text->field_body->value : '';
            }
          }
          $data[] = ['id' => $value->id(),'title' => $value->getTitle(), 'type' => $value->getType(), 'paragraph_data' => $para_field_body_text_value];
        }
        elseif($value->getType() == 'custom_article') {
          $node_field_body_text = !empty($value->get('field_body')->getValue()) ? $value->get('field_body')->getValue() : '';
          if (!empty($node_field_body_text)) {
            foreach ($node_field_body_text as $node_field_body_text_value) {
              $para_field_body_text = Paragraph::load($node_field_body_text_value['target_id']);
              $para_field_body_text_value = !empty($para_field_body_text->field_long_body->value) ? $para_field_body_text->field_long_body->value : '';
              $para_field_picture_text = !empty($para_field_body_text->field_picture_text->value) ? $para_field_body_text->field_picture_text->value : '';
              $para_field_image = !empty($para_field_body_text->field_image->target_id) ? $para_field_body_text->field_image->target_id : '';
            }
          }
          $data[] = ['id' => $value->id(),'title' => $value->getTitle(), 'type' => $value->getType(), 'paragraph_data' => [$para_field_body_text_value, $para_field_picture_text, $para_field_image] ];
        }
        elseif($value->getType() == 'custom_blog') {
          $node_field_body_text = !empty($value->get('field_blog_link')->getValue()) ? $value->get('field_blog_link')->getValue() : '';
          if (!empty($node_field_body_text)) {
            foreach ($node_field_body_text as $node_field_body_text_value) {
              $para_field_body_text = Paragraph::load($node_field_body_text_value['target_id']);
              $para_field_link_text = !empty($para_field_body_text->field_link_text->value) ? $para_field_body_text->field_link_text->value : '';
              $para_field_link_url = !empty($para_field_body_text->field_link_url->uri) ? $para_field_body_text->field_link_url->uri : '';
            }
          }
          $data[] = ['id' => $value->id(),'title' => $value->getTitle(), 'type' => $value->getType(), 'paragraph_data' => [$para_field_link_text, $para_field_link_url]];
        }
       }
     }

   $response = new ResourceResponse($data);
    // In order to generate fresh result every time (without clearing
    // the cache), you need to invalidate the cache.
    $response->addCacheableDependency($data);
    return $response;
  }

}
