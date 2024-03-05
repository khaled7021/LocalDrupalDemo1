<?php

namespace Drupal\digital_nadeem\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\node\Entity\Node;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "rest_samplepost",
 *   label = @Translation("Rest samplepost"),
 *   uri_paths = {
 *	  
 *     "create" = "/rest/digitalnadeem/api/post/items"
 *   }
 * )
 */
class RestSamplepost extends ResourceBase {

  /**
   * Responds to POST requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {

   // You must to implement the logic of your REST Resource here.
   //$data = json_encode($data);
   $data1 = ['message' => 'Hello, You have created  article: '.$data["title"]];
  
   //=====programatically create node
				 $contentType  = 'article';
					$node = Node::create(['type' => $contentType]);
					//$node->langcode = "fr";
					$node->uid = 1;
					$node->promote = 0;
					$node->sticky = 0;
					$node->title=  $data["title"];
					$node->body = $data["body"];
					$node->field_image = ['target_id' => $data["fid"]];
					
					$node->save();
					$nid = $node->id();	 
   //=====programatically create node
	    
    $response = new ResourceResponse($data1);
    // In order to generate fresh result every time (without clearing 
    // the cache), you need to invalidate the cache.
    $response->addCacheableDependency($data1);
    return $response;
  }

}