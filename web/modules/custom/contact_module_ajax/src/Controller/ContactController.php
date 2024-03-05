<?php
namespace Drupal\contact_module_ajax\Controller;
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

/**
* Class ContactController.
*
* @package Drupal\contact_module_ajax\Controller
*/
class ContactController extends ControllerBase {
  
  /**
  * The form builder.
  *
  * @var \Drupal\Core\Form\FormBuilder
  */
  protected $formBuilder;
  
  /**
  * The ContactController constructor.
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
  
  // To show Form and Table in one page with pager
  /**
  * {@inheritdoc}
  */
  public function manageContacts() {
    $form['form'] = $this->formBuilder()->getForm('Drupal\contact_module_ajax\Form\ContactForm');
    $form['form']['#attached']['library'][]='core/drupal.dialog.ajax';
    $form['form']['#attached']['library'][]='contact_module_ajax/global-styles';
    // $render_array = $this->formBuilder()->getForm('Drupal\contact_module_ajax\Form\ContactTableForm','All');
    // $form['form1'] = $render_array;
    $form['form']['#suffix'] = '<div class="pagination">'.getPager_ajax().'</div><div class="table-data-contact"></div>';
    
    return $form;
  }

  /**
  * {@inheritdoc}
  * Deletes the data for given id
  */
  public function deleteContactAjax($cid) {
    $res = \Drupal::database()->query("delete from contact_ajax where id = :id", array(':id' => $cid)); 
    $render_array = $this->formBuilder->getForm('Drupal\contact_module_ajax\Form\ContactTableForm','All');
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('.result_message','' ));
    $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
    $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link', 'removeClass', array('active')));
    $response->addCommand(new \Drupal\Core\Ajax\InvokeCommand('.pagination-link:first', 'addClass', array('active')));
    
    return $response;
  }
  
  /**
  * {@inheritdoc}
  * update the given contact
  */
  public function editContactAjax($cid) {
    
    $conn = Database::getConnection();
    $query = $conn->select('contact_ajax', 'ct');
    $query->condition('id', $cid)->fields('ct');
    $record = $query->execute()->fetchAssoc();
    $render_array = \Drupal::formBuilder()->getForm('Drupal\contact_module_ajax\Form\ContactEditForm',$record);
    $render_array['#attached']['library'][] = 'contact_module_ajax/global-styles';
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand('Edit Form', $render_array, ['width' => '800']));
    
    return $response;
  }
  
  /**
  * {@inheritdoc}
  * Deletes the given contact
  */
  
  // public function tablePaginationAjax($no){
  //   $response = new AjaxResponse();
  //   $render_array = \Drupal::formBuilder()->getForm('Drupal\contact_module_ajax\Form\ContactTableForm',$no);
  //   $response->addCommand(new HtmlCommand('.result_message',''));
  //   $response->addCommand(new \Drupal\Core\Ajax\AppendCommand('.result_message', $render_array));
  
  //   return $response;
  // }  
}
