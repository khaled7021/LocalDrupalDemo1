<?php


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Drupal\contact_module_ajax\Services;

use Drupal\Core\Database\Connection;

class ContactCrudOp {

protected $database;

public function __construct(Connection $database)
{
$this->database = $database;
}

public function setData($fields){
$this->database->insert('contact_ajax')->fields($fields)->execute();
}

public function getData($page_no){
    if($page_no == "All"){
    $query = $this->database->select('contact_ajax', 'cf');
    $query->fields('cf');
    $query->range(0, 6);
    $query->orderBy('cf.id','ASC');
    $results = $query->execute()->fetchAll();
    return $results;
    }
    else{
    $query = $this->database->select('contact_ajax', 'cf');
    $query->fields('cf');
    $query->range($page_no*6, 6);
    $query->orderBy('cf.id','ASC');
    $results = $query->execute()->fetchAll();
    return $results;
    }
}

public function getDataById($num){
    $query = $this->database->select('contact_ajax', 'm')
            ->condition('m.id', $num)
            ->fields('m');
    $record = $query->execute()->fetchAssoc();
    return $record;
        
}

public function updateData($fields, $num){
     $query = $this->database;
          $query->update('contact_ajax')->fields($fields)->condition('id', $num)->execute();
}

public function deleteDataById($num)
{
 $query = $this->database;
       $query->delete('contact_ajax')->condition('id', $num)->execute();
}
}
