<?php

namespace Drupal\contact_module_ajax\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormState;
use Drupal\Core\Link;

/**
* Provides the form for adding countries.
*/
class ContactForm extends FormBase {
  
  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'contact_module_ajax_form';
  }
  
  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state,$record = NULL) {
    $form['message'] = [
      '#markup' => '<div id="error-message"></div>',
    ];
    $form['fullname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#maxlength' => 20,
      '#attributes' => array(
        'class' => ['txt-class','contact_fname'],
      ),
      '#default_value' =>'',
      '#prefix' => '<div id="div-fname">',
      '#suffix' => '</div><div id="div-fname-message"></div>',
    ];
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#maxlength' => 40,
      '#attributes' => array(
        'class' => ['txt-class','contact_email'],
      ),
      '#default_value' => '',
      '#suffix' => '<div id="div-email-message"></div>',
    ];
    $form['phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#maxlength' => 15,
      '#attributes' => array(
        'class' => ['txt-class','contact_phone'],
      ),
      '#default_value' =>  '',
      '#suffix' => '<div id="div-phone-message"></div>',
    ];
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['Save'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Save') ,
      '#attributes' => array(
        'class' => ['contact-save-ajax'],
      ),
    ];
    $form['actions']['clear'] = [
      '#type' => 'submit',
      '#value' => 'Clear',
    ];

    $form['#attached']['library'][] = 'contact_module_ajax/global-styles';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attributes']['class'][] = 'contact-common-form';
    return $form;
    
  }
  
  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }  
    
    // /**
    // * Our custom Ajax responce.
    // */
    // public function saveDataAjaxCallback(array &$form, FormStateInterface $form_state) {
      
    //   $conn = Database::getConnection();      
    //   $field = $form_state->getValues();
    //   $re_url = Url::fromRoute('contact_module_ajax.contact');
    //   $fields["fullname"] = $field['fullname'];
    //   $fields["email"] = $field['email'];
    //   $fields["phone"] = $field['phone'];
    //   $css = ['border' => '1px solid red'];
    //   $text_css = ['color' => 'red'];
    //   $response = new AjaxResponse();

    //       //========Field value validation
    //   if($fields["fullname"] == ''  || preg_match('/[^A-Za-z\s]/', $fields["fullname"])) {
        
    //     $message = ('Full Name not valid.');
    //     //$response = new \Drupal\Core\Ajax\AjaxResponse();
    //     $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#edit-fname', $css));
    //     $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#error-message', $text_css));
    //     $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#error-message', $message));
    //     return $response;
    //   }
    //   // elseif(!\Drupal::service('email.validator')->isValid($fields["email"])) {
       
    //   //   $message = ('Email not valid.');
        
    //   //   //$response = new \Drupal\Core\Ajax\AjaxResponse();
    //   //   $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#error-message', $text_css));
    //   //   $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#error-message', $message));
    //   //   return $response;
    //   // }
    //   else if($fields["phone"] == '' || strlen($fields["phone"]) < 10) {
       
    //     $message = ('Phone Number not valid.');
        
    //     //$response = new \Drupal\Core\Ajax\AjaxResponse();
    //     $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#error-message', $text_css));
    //     $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#error-message', $message));
    //     return $response;
    //   }
    //   else {
    //     //insert data into contact table.
    //     $conn->insert('contact_ajax')->fields($fields)->execute();
    //     // attach drupal.dialog.ajax library for dialog box
    //     $dialogText['#attached']['library'][] = 'core/drupal.dialog.ajax';
    //     $render_array = \Drupal::formBuilder()->getForm('Drupal\contact_module_ajax\Form\ContactTableForm','All');
    //     //$render_array['#attached']['library'][] = 'contact_module_ajax/global_styles';
    //     $response->addCommand(new HtmlCommand('.result_message','' ));
    //     $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
    //     $response->addCommand(new HtmlCommand('.pagination','' ));
    //     $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.pagination', getPager_ajax()));
    //     $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link', 'removeClass', array('active')));
    //     $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link:first', 'addClass', array('active')));
    //     $response->addCommand(new InvokeCommand('.txt-class', 'val', ['']));
    //     $response->addCommand(new HtmlCommand('#error-message', ''));
      
    //     return $response;
    //   }
    // }
    /**
    * {@inheritdoc}
    */
    public function submitForm(array & $form, FormStateInterface $form_state) {
      
    }  
  }
  
  