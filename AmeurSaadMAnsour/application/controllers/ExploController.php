<?php

class ExploController extends Zend_Controller_Action
{
	var $idBase = "flux_livrenum";
	var $urlRedir = '/auth/login?redir=explo&idBase=';
	
    public function indexAction()
    {

	    	//chargement des inclanations        
	    	$dbDoc = new Model_DbTable_Flux_Doc();

    		$this->view->arrInclinations = $dbDoc->findByTronc("inclination");
    		$this->view->arrInclinaisons = $dbDoc->findByTronc("inclinaison");

    		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
		    // l'identité existe ; on la récupère
		    $this->view->identite = $auth->getIdentity();
		    $ssUti = new Zend_Session_Namespace('uti');
		    $ssUti->redir = $this->urlRedir.$this->dbNom;
		    $this->view->uti = $ssUti->uti;
		}else{			
		    $this->view->identite = false;
		}   
		 		
    }

    public function sauvesvgAction()
    {
    		$dbDoc = new Model_DbTable_Flux_Doc();
    		
    		$idDoc = $dbDoc->ajouter(array("titre"=>$this->_getParam('titre', "mon svg")
    			,"url"=>$this->_getParam('url')
    			,"data"=>$this->_getParam('svg')),false);
        $this->view->idDoc = $idDoc;
        
        if($this->_getParam('idUti')){
        		$dbUtiDoc = new Model_DbTable_Flux_UtiDoc();
        		$dbUtiDoc->ajouter(array("doc_id"=>$idDoc,"uti_id"=>$this->_getParam('idUti')));
        }
    		
    }
    
    public function ecologyAction()
    {
    
    }
    
}

