<?php
namespace Drupal\dn_students\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class StudentController.
 *
 * @package Drupal\dn_students\Controller
 */
class StudentController extends ControllerBase {

/**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The StudentController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   */
  public function __construct(FormBuilder $formBuilder) {
    $this->formBuilder = $formBuilder;
  }
/**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function manageStudents() {
    $form['#attached']['library'][] = 'dn_students/global_styles';
	  $form['form'] = $this->formBuilder()->getForm('Drupal\dn_students\Form\StudentForm');

    $session = \Drupal::request()->getSession();
    // Retrieve data from the session.
    $paginationData = $session->get('paginationNo');
	  $render_array = $this->formBuilder()->getForm('Drupal\dn_students\Form\StudentTableForm',$paginationData);
	  $form['form1'] = $render_array;
	  $form['form']['#suffix'] = '<div class="pagination">'.getPager().'</div>';
    return $form;
  }
  /**
   * {@inheritdoc}
   * Deletes the given student
   */
  public function deleteStudentAjax($cid) {
     $res = \Drupal::database()->query("delete from students where id = :id", array(':id' => $cid));
	// $render_array = \Drupal::formBuilder()->getForm('Drupal\dn_students\Form\StudentTableForm','All');

  $session = \Drupal::request()->getSession();
  // Retrieve data from the session.
  $paginationData = $session->get('paginationNo');

	 $render_array = $this->formBuilder->getForm('Drupal\dn_students\Form\StudentTableForm',$paginationData);
	$response = new AjaxResponse();
	  //$response->addCommand(new HtmlCommand('.result_message',$render_array ));
	   $response->addCommand(new HtmlCommand('.result_message','' ));
	   $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
	   $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link', 'removeClass', array('active')));
	   $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link:first', 'addClass', array('active')));

    return $response;

  }

   /**
   * {@inheritdoc}
   * update the given student
   */
  public function editStudentAjax($cid) {

	  $conn = Database::getConnection();
      $query = $conn->select('students', 'st');
      $query->condition('id', $cid)->fields('st');
      $record = $query->execute()->fetchAssoc();

	 $render_array = \Drupal::formBuilder()->getForm('Drupal\dn_students\Form\StudentEditForm',$record);
	 //$render_array['#attached']['library'][] = 'dn_students/global_styles';
	$response = new AjaxResponse();
	 $response->addCommand(new OpenModalDialogCommand('Edit Form', $render_array, ['width' => '800']));

    return $response;
  }

/**
   * {@inheritdoc}
   * Deletes the given student
   */

  public function tablePaginationAjax($no){
	  $response = new AjaxResponse();
	  $render_array = \Drupal::formBuilder()->getForm('Drupal\dn_students\Form\StudentTableForm',$no);
	   $response->addCommand(new HtmlCommand('.result_message','' ));
	    $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));


	 return $response;

  }

/**
   * Ajax callback method.
   */
  public function ajaxCallback(Request $request) {
    // Retrieve the value sent via Ajax.
    $paginationNo = $request->request->get('paginationNo');

    // Process the value or perform any other logic.
    $session = $request->getSession();

    // Store data in the session.
    $session->set('paginationNo', $paginationNo);

    // Return a JsonResponse with any data you want to send back.
    $data = ['status' => 'success', 'message' => 'Ajax request successful'];
    return new JsonResponse($data);
  }

}
