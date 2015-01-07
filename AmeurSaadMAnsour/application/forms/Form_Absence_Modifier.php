<?php

/**
 * Ce fichier contient la classe Form_Absence_Modifier.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * Modifier une entrée Absence.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Form_Absence_Modifier
{
           
    public function init()
    {
        
        $id = new Zend_Form_Element_Text('id');
        $id->setRequired(true)
            ->addValidators(array(new Zend_Validate_Int(), new Zend_Validate_StringLength()));
        
        $date = new Zend_Form_Element_('date');
        $date->setRequired(true)
            ->addValidators(array());
        
        $nom_absent = new Zend_Form_Element_Text('nom_absent');
        $nom_absent->setRequired(true)
            ->addValidators(array(new Zend_Validate_Alnum(true), new Zend_Validate_StringLength(550)));
        
        
        
        $this->addElements(array($id, $date, $nom_absent, ));             
   
    }
            
}