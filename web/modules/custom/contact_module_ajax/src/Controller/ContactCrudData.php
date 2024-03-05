<?php

namespace Drupal\contact_module_ajax\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\vl_util\Logger as Logger;

class ContactCrudData {

  public function getContactAjax() {
    $page_no = $_GET['page'];
    $record = \Drupal::service('contact_module_ajax.data_handler_info')->getData($page_no);
    $build=[];
    $build[]=[
      '#theme' =>'contact_table',
      '#rows' =>$record,
    ];
    $build['#attached']['library'][]='contact_module_ajax/global-styles';
    $build['#attached']['library'][]='core/drupal.dialog.ajax';
     // Render the template to HTML
    $contact_table = \Drupal::service('renderer')->renderPlain($build);
    // dpm($contact_table);contact_ajax
    // $output_table= !empty($contact_table)?\Drupal\Core\Render\Markup::create($contact_table):'';
    // dpm($output_table);
    $response = new Response($contact_table);
    // dpm($response);

    return $response;
  }

  public function insertContactAjax() {
    $params = [];
    $params['fullname'] = \Drupal::request()->request->get('fullname');
    $params['email'] = \Drupal::request()->request->get('email');
    $params['phone'] = \Drupal::request()->request->get('phone');

    // inserted data
    $record = \Drupal::service('contact_module_ajax.data_handler_info')->setData($params);
    $message = "data inserted successfully";
    return new JsonResponse($message);
  }

  public function updateContactAjax() {
    $id = \Drupal::request()->request->get('id');
    $params['fullname'] = \Drupal::request()->request->get('fullname');
    $params['email'] = \Drupal::request()->request->get('email');
    $params['phone'] = \Drupal::request()->request->get('phone');

    $record = \Drupal::service('contact_module_ajax.data_handler_info')->updateData($params, $id);
    $render_array = \Drupal::formBuilder()->getForm('Drupal\contact_module_ajax\Form\ContactTableForm','All');
    // $response->addCommand(new HtmlCommand('.pagination','' ));
    //     $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.pagination', getPager_ajax()));
    $html = '';
    $html = getPager_ajax();
    $html .= \Drupal::service('renderer')->render($render_array);
    $message = " data inserted successfully";
    return new Response($html);
  }
}
