<?php

namespace Drupal\test_module\Form;

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
      '#ajax' => ['callback' => '::saveDataAjaxCallback'] ,
    ];
    $form['actions']['clear'] = [
      '#type' => 'submit',
      '#value' => 'Clear',
    ];

    // $form['#attached']['library'][] = 'contact_module_ajax/global-styles';
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
    public function saveDataAjaxCallback(array &$form, FormStateInterface $form_state) {

      $field = $form_state->getValues();
      $fields["fullname"] = $field['fullname'];
      $fields["email"] = $field['email'];
      $fields["phone"] = $field['phone'];
      $css = ['border' => '1px solid red'];
      $text_css = ['color' => 'red'];
      $response = new AjaxResponse();

          //========Field value validation
          // $pattern1 = '/^[A-Za-z]{3,}\s[A-Za-z]{3,}$/';
          $name_pattern = '/[^A-Za-z\s]/';
          $email_pattern = '/^[a-zA-Z0-9._%+-]$/';
          $phone_pattern = '/[^0-9]/';
      if($fields["fullname"] == ''  || preg_match($name_pattern, $fields["fullname"])) {

        $message = ('Full Name not valid.');
        //$response = new \Drupal\Core\Ajax\AjaxResponse();
        $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#edit-fname', $css));
        $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#error-message', $text_css));
        $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#error-message', $message));
        return $response;
      }
      elseif(!\Drupal::service('email.validator')->isValid($fields["email"])) {
        // elseif($fields["email"] == ''  || preg_match($email_pattern, $fields["email"])) {

        $message = ('Email not valid.');

        //$response = new \Drupal\Core\Ajax\AjaxResponse();
        $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#error-message', $text_css));
        $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#error-message', $message));
        return $response;
      }
      else if($fields["phone"] == '' || strlen($fields["phone"]) < 10 || strlen($fields["phone"]) > 10
      || preg_match($phone_pattern, $fields["phone"])) {

        $message = ('Phone Number not valid.');

        //$response = new \Drupal\Core\Ajax\AjaxResponse();
        $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#error-message', $text_css));
        $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#error-message', $message));
        return $response;
      }
      else {
        $css = ['border' => ''];
        $text_css = ['color' => ''];
        $response->addCommand(new \Drupal\Core\Ajax\CssCommand('#error-message', $text_css));
        $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#error-message', ''));

        return $response;
      }
    }
    /**
    * {@inheritdoc}
    */
    public function submitForm(array & $form, FormStateInterface $form_state) {

    }
  }

