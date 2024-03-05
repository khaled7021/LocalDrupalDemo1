<?php
namespace Drupal\contact_module_ajax\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use Drupal\Core\Link;

/**
* Provides the list of Contacts.
*/
class ContactTableForm extends FormBase {
  
  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'contact_module_ajax_table_form';
  }
  
  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state,$pageNo = NULL) {
    
    //$pageNo = 2;
    $header = [
      'id' => $this->t('Id'),
      'fname' => $this->t('Full Name'),
      'email' => $this->t('Email'),
      'phone'=> $this->t('Phone'), 
      'opt' => $this->t('Operations')
    ];
    
    if($pageNo != ''){
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $this->get_contacts($pageNo),
        '#empty' => $this->t('No users found'),
        '#attributes' => ['id' => ['contact-table']],
      ];
    }
    else {
      $form['table'] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $this->get_contacts("All"),
        '#empty' => $this->t('No records found'),
        '#attributes' => ['id' => ['contact-table']],
      ];
    }

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'contact_module_ajax/global-styles';
    $form['#theme'] = 'contact_form';
    $form['#prefix'] = '<div class="result_message">';
    $form['#suffix'] = '</div>';
    $form['#cache'] = [
      'max-age' => 0
    ];
    return $form;
    
  }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }
  /**
  * {@inheritdoc}
  */
  public function submitForm(array & $form, FormStateInterface $form_state) {	  
    
  }
  
  // function to generate contact table data(rows of table) based on page no
  function get_contacts($opt) {
    $res = array();
    //$opt = 2;
    if($opt == "All"){
      
      $results = \Drupal::database()->select('contact_ajax', 'ct');
      $results->fields('ct');
      $results->range(0, 6);
      $results->orderBy('ct.id','ASC');
      $res = $results->execute()->fetchAll();
      $ret = [];
    }
    else{

      $query = \Drupal::database()->select('contact_ajax', 'ct');
      $query->fields('ct');
      $query->range($opt*6, 6);
      $query->orderBy('ct.id','ASC');
      $res = $query->execute()->fetchAll();
      $ret = [];
    }

    foreach ($res as $row) {
        
      $edit = Url::fromUserInput('/ajax/contact_module_ajax/contacts/edit/' . $row->id);
      
      //array('attributes' => array('onclick' => "return confirm('Are you Sure')"))
      $delete = Url::fromUserInput('/del/contact_module_ajax/contacts/delete/' . $row->id,array('attributes' => array('onclick' => 'if(!confirm("Really Delete?")){return false;}')));
      $edit_link = Link::fromTextAndUrl(t('Edit'), $edit);
      $delete_link = Link::fromTextAndUrl(t('Delete'), $delete);
      $edit_link = $edit_link->toRenderable();
      $delete_link  = $delete_link->toRenderable();
      
      //to display a dialog box add use-ajax class to link
      $edit_link['#attributes'] = ['class'=>'use-ajax'];
      $delete_link['#attributes'] = ['class'=>'use-ajax', 'data-dialog-type' => 'dialog'];
      
      // $mainLink = t('@linkApprove  @linkReject', array('@linkApprove' => $edit_link, '@linkReject' => $delete_link));
      
      $ret[] = [
        'id' => $row->id,
        'fullname' => $row->fullname,
        'email' => $row->email,
        'phone' => $row->phone,
        'action1' => array(
          'data' => $edit_link,
          'style' => 'display: inline-flex;',
        ),
        'action2' => array(
          'data' => $delete_link,
          'style' => 'display: inline-flex;',
        ),
      ];
    }
    return $ret;
  }  
}