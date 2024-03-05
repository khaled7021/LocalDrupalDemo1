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

/**
* Provides the form for edit contact.
*/
class ContactEditForm extends FormBase {
  
  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'contact_module_ajax_form_edit';
  }
  
  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state,$record = NULL) {
    $conn = Database::getConnection();
    
    $language = \Drupal::languageManager()->getLanguages();
    if(isset($record['id'])){
      $form['id'] = [
        '#type' => 'hidden',
        '#attributes' => array(
          'class' => ['txt-class'],
        ),
        '#default_value' => (isset($record['id'])) ? $record['id'] : '',
      ];
    }

    $form['fullname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
      '#maxlength' => 20,
      '#attributes' => array(
        'class' => ['txt-class'],
      ),
      '#default_value' => (isset($record['fullname'])) ? $record['fullname'] : '',
    ];

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
      '#maxlength' => 40,
      '#attributes' => array(
        'class' => ['txt-class'],
      ),
      '#default_value' => (isset($record['email'])) ? $record['email'] : '',
    ];

    $form['phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#required' => TRUE,
      '#maxlength' => 15,
      '#attributes' => array(
        'class' => ['txt-class'],
      ),
      '#default_value' => (isset($record['phone'])) ? $record['phone'] : '',
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['Save'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      // '#ajax' => ['callback' => '::updateContactData'] ,
      '#value' => (isset($record['fullname'])) ? $this->t('Update') : $this->t('Save') ,
    ];
    
    $form['#prefix'] = '<div class="form-div-edit" id="form-div-edit">';
    $form['#suffix'] = '</div>';
    
    // $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    return $form;
    
  }
  
  /**
  * {@inheritdoc}
  */
  public function validateForm(array & $form, FormStateInterface $form_state) {
  }
  
  public function updateContactData(array $form, FormStateInterface $form_state) {
    // $response = new AjaxResponse();
    
    // // If there are any form errors, re-display the form.
    // if ($form_state->hasAnyErrors()) {
    //   $response->addCommand(new ReplaceCommand('#form-div-edit', $form));
    // }
    // else {
    //   $conn = Database::getConnection();
    //   $field = $form_state->getValues();
    //   $re_url = Url::fromRoute('contact_module_ajax.contact');
      
    //   $fields["fullname"] = $field['fullname'];
    //   $fields["email"] = $field['email'];
    //   $fields["phone"] = $field['phone'];
      
    //   $conn->update('contact_ajax')->fields($fields)->condition('id', $field['id'])->execute();
    //   $response->addCommand(new OpenModalDialogCommand("Success!", 'The table has been submitted.', ['width' => 800]));
    //   $render_array = \Drupal::formBuilder()->getForm('Drupal\contact_module_ajax\Form\ContactTableForm','All');
      
    //   $response->addCommand(new HtmlCommand('.result_message','' ));
    //   $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
    //   $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link', 'removeClass', array('active')));
    //   $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link:first', 'addClass', array('active')));      
    // }
    
    // return $response;
  }
  
  
  /**
  * {@inheritdoc}
  */
  public function submitForm(array & $form, FormStateInterface $form_state) {
  }
  
}

