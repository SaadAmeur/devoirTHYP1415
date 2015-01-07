<?php
class AuthController extends Zend_Controller_Action
{
    public function loginAction()
    {
    	try {
    		
		$s = new Flux_Site($this->_getParam('idBase', 0));
		$ssExi = new Zend_Session_Namespace('uti');
		$ssExi->dbNom = $this->_getParam('idBase', 0);	
		
		if(isset($ssExi->redir)){
			$redir=$ssExi->redir;
		} 
		if($this->_getParam('redir', 0)){
			$redir='/'.$this->_getParam('redir', 0);
			$ssExi->redir = $redir;
		}
		
    		// Obtention d'une référence de l'instance du Singleton de Zend_Auth
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();

    		if($this->_getParam('ajax', 0)){
    			$this->view->ajax = true;
            $adapter = new Zend_Auth_Adapter_DbTable(
                $s->db,
                'flux_uti',
                'login',
                'mdp'
                );		                
            $adapter->setIdentity($this->_getParam('login'));
            $adapter->setCredential($this->_getParam('mdp'));
            $login = $this->_getParam('login');
            // Tentative d'authentification et stockage du résultat
			if($login)$result = $auth->authenticate($adapter);			
    		}else{    		
			$loginForm = new Form_Auth_Login();
			if ($this->getRequest()->isPost()) {
		        $formData = $this->getRequest()->getPost();
		        
		        if(isset($formData["crea"])) $this->_redirect('auth/inscription');
		        if(isset($formData["ajax"])) $this->view->ajax = true;
		        else $this->view->ajax = false;
		        
		        if ($loginForm->isValid($formData)) {
		            $adapter = new Zend_Auth_Adapter_DbTable(
		                $s->db,
		                'flux_uti',
		                'login',
		                'mdp'
		                );		                
		            $adapter->setIdentity($loginForm->getValue('login'));
		            $adapter->setCredential($loginForm->getValue('password'));
		            $login = $loginForm->getValue('login');
		            
		            // Tentative d'authentification et stockage du résultat
					$result = $auth->authenticate($adapter);
	        		}
	        
	        		$this->view->form = $loginForm;
    			}
		}    		
    			
    		if ($result && $result->isValid()) {		            	
			//met en sessions les informations de l'existence
			$s = new Flux_Site($this->_getParam('idBase'));
			$dbUti = new Model_DbTable_Flux_Uti($s->db);
			$rs = $dbUti->findByLogin($login);
    			$ssExi->uti = $rs;
		            	
	        	if($this->view->ajax){
				$this->view->rs = $dbUti->findByLogin($login);
	        }else{
	        		$ssExi->uti = false;	        	
		    		$this->_redirect($redir);
		    		return;
	        }
	    }else{
	    		if($result){
		    		switch ($result->getCode()) {
	            		case 0:
							$this->view->erreur = "Problème d'identification. Veuillez contacter le webmaster.";		            	
		            		break;		            		
	            		case -1:
							$this->view->erreur = "Le login n'a pas été trouvé.";		            	
		            		break;		            		
	            		case -2:
							$this->view->erreur = "Le login est ambigue.";		            	
		            		break;		            		
	            		case -2:
							$this->view->erreur = "Le login et/ou le mot de passe ne sont pas bons.";		            	
		            		break;		            		
	            	}
	    		}else{
	    			//echo $redir;
	        		$ssExi->uti = false;	        	
	    			$this->_redirect($redir);            	
	    		}
	   }
    	}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
		    echo "Message: " . $e->getMessage() . "\n";
		}	        
    }

    public function inscriptionAction()
    {
    	try {
    		if($this->_getParam('ajax', 0)){
			$formData['ip_inscription'] = $_SERVER['REMOTE_ADDR'];
			$formData['date_inscription'] = date('Y-m-d H:i:s');
			$formData['login'] = $this->_getParam('login');
			$formData['mdp'] = $this->_getParam('mdp');
			$ssExi = new Zend_Session_Namespace('uti');
			//echo "ssExi->dbNom = ".$ssExi->dbNom;
			$s = new Flux_Site($this->_getParam('idBase'));
			$dbUti = new Model_DbTable_Flux_Uti($s->db);
			$this->view->rs = $dbUti->ajouter($formData, true, true);
			$this->view->ajax=true;
			$ssExi->uti = $this->view->rs;    			
    		}else{    		
	    		$this->view->form = $form = new Form_Auth_Inscription();
	   	    	if($this->getRequest()->isPost()){
					$formData = $this->getRequest()->getPost();
					$this->view->ajax = isset($formData['ajax']);
					if(isset($formData['idBase']))$ssExi->dbNom=$formData['idBase'];						
					if($form->isValid($formData)){
						//supprime les données superflux
						unset($formData['envoyer']);					
						unset($formData['ajax']);					
						unset($formData['idBase']);					
						
						$formData['ip_inscription'] = $_SERVER['REMOTE_ADDR'];
						$formData['date_inscription'] = date('Y-m-d H:i:s');
						//print_r($formData);
						$ssExi = new Zend_Session_Namespace('uti');
						//echo "ssExi->dbNom = ".$ssExi->dbNom;
						if(isset($ssExi->dbNom)){
							$site = new Flux_Site();
							$db = $site->getDb($ssExi->dbNom);						
							$dbUti = new Model_DbTable_Flux_Uti($db);
						}else $dbUti = new Model_DbTable_Flux_Uti();					
						$idUti = $dbUti->ajouter($formData);
						if($this->view->ajax)
							$this->view->idUti = $idUti;
						else
							$this->_redirect('auth/login');
					}else{
						if($this->view->ajax)
							$this->view->idUti = -1;
						else
							$form->populate($formData);
					}
					
				}    			        
    		}
    	}catch (Zend_Exception $e) {
			echo "Récupère exception: " . get_class($e) . "\n";
		    echo "Message: " . $e->getMessage() . "\n";
		}	        
    }
    
}