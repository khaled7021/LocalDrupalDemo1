<?php

namespace Drupal\dn_injection\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Form\FormState;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Provides the form for adding countries.
 */
class StudentForm extends FormBase {

  // Step 1: Add an object variable to save the value passed in the class constructor.
  // So here we are adding below $messenger variable inside our form class StudentForm in
  // path – /src/Form/StudentForm.php

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'StudentForm_form';
  }

  // Step 2: Add a  class constructor that saves the values passed from the previous method in
  // object properties. Inside constructor  uses the services that are loaded by
  // StudentForm::create and stores them in properties of the class.
  // If you have more services, the order in which the services are loaded in
  // SrudentForm::create must be equal to the order of the parameters in the StudentForm::__construct method.
  /**
   * Constructs a  object.

   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct( MessengerInterface $messenger) {
    //parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->messenger = $messenger;
  }

  // Step 3: Add a  static  public create() method that gets the values from the dependency container,
  // and creates a new object of your class. So inside your class create below create
  // function with container Interface.

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$record = NULL) {


    $form['student_name'] = [
      '#type' => 'textfield',
      '#required' => 1,
     '#title' => 'Student Name',
     '#description' => '',

     '#attributes' => array(
       'class' => ['form-control'],
       'placeholder' => ''
       ),
      '#default_value' =>''

    ];
    $form['subject'] = [
       '#type' => 'textfield',
       '#required' => 1,
      '#title' => 'Subject',
      '#description' => '',

      '#attributes' => array(
        'class' => ['form-control'],
        'placeholder' => ''
        ),
       '#default_value' =>''

     ];


    $form['marks'] = [
       '#type' => 'textfield',
       '#required' => 1,
      '#title' => 'Marks',
      '#description' => '',

      '#attributes' => array(
        'class' => ['form-control'],
        'placeholder' => ''
        ),
       '#default_value' =>''

     ];

     $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#default_value' => 'Submit' ,
        '#attributes' => array(
          'class' => ['btn-default btn']

          )
      ];


    return $form;

  }

   /**
   * {@inheritdoc}
   */
  public function validateForm(array & $form, FormStateInterface $form_state) {


  }


   public function clearForm(array &$form, FormStateInterface $form_state) {



   }


  //  Step 4- Call the service functions
  //  Finally we are calling service function using object variable created in step-1 with
  //  $this keyword.
  //  Here we are using below addMessage() function for displaying message with submitted details in form.
  //  $this->messenger->addMessage();
  /**
   * {@inheritdoc}
   */
 public function submitForm(array & $form, FormStateInterface $form_state) {
   $stud_name  = $form_state->getValue('student_name');
   $subject  = $form_state->getValue('subject');
   $marks  = $form_state->getValue('marks');
   $this->messenger->addMessage( $this->t('You have provided name as %stud_name subject as %subject and marks as %marks', ["%stud_name"=>$form_state->getValue('student_name'),"%subject"=>$form_state->getValue('subject'),"%marks"=>$form_state->getValue('marks')]));


  }

}
