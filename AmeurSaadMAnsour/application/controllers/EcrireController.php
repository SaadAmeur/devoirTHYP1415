<?php

class EcrireController extends Zend_Controller_Action
{
	var $rss = array("CDNL1415"=>"https://picasaweb.google.com/data/feed/base/user/112098438788633053665/albumid/6064761146942390433?alt=rss&kind=photo&authkey=Gv1sRgCJG9i8HQ-b61kwE&hl=en_US"
		,"THYP1415"=>"http://picasaweb.google.com/data/feed/base/user/107401320610499259896/albumid/6065229773950541889?alt=rss&kind=photo&authkey=Gv1sRgCNak7e60l-7VlgE&hl=fr"
		);

    public function indexAction()
    {
        // action body
    }

    public function samszoAction()
    {
    		$dbUti = new Model_DbTable_Flux_Uti();
    		$this->view->data = $dbUti->findByRole($this->_getParam('role', "THYP 14-15"),true, true);
    }
    public function ajouteventAction()
    {
    		//ajouter l'enregistrement dans la base	
    		$idUti = $this->_getParam('idUti');
    		$idTag = $this->_getParam('idTag');
  		$this->view->idUti = $idUti;
    		$dbUT = new Model_DbTable_flux_utitag();
    		$dbUT->ajouter(array("uti_id"=>$idUti,"tag_id"=>$idTag),false);
  		$this->view->data = $dbUT->findByUti_id($idUti);
    }
    
    

	
	public function ajoutAbsenceAction(){
		//TODO: ajouter l'enregistrement dans la base
		$this->view->idUti = $this->_getParam('uti_id');
    }

	
	public function ecrireAction(){
		
		$dbUti = new Model_DbTable_Flux_Uti();
    	$this->view->data = $dbUti->findByRole($this->_getParam('role', "THYP 14-15"),true, true);
    }
  
    public function sauvefluxthypAction(){
        
        
        $absence = new  Model_DbTable_Absence_Absence();
        $rs = $absence->fetchAll();
        $this->view->absence = $rs;        
               
        
        $this->dbD = new Model_DbTable_Flux_Doc();
		$trombi = $this->_getParam('trombi', 0);
        
        
        
        $this->view->trombi =  $trombi;   
        $chemin = $_SERVER['DOCUMENT_ROOT']."/AmeurSaadMAnsour/data/trombi/".$trombi;
		$this->view->chemin = $chemin;
        
        $url = $this->rss['THYP1415']; 
        $xml = simplexml_load_file($url);
        $titre = $xml->channel->title;	    		
        //enregistre le flux rss comme document
        $data = array("url"=>$url, "titre"=>$titre, "data"=>$xml->asXML());
        $this->view->data = $data; 
        
        $this->dbD->ajouter($data,true,true);
        
        
    }
    
    public function sauveabsenceAction() {
        
		$this->db = new  Model_DbTable_Absence_Absence();
                     
				if($_POST)
				{
				/* VALUES */
				$date= date('Y-m-d H:i:s');
				$nom_absent=$_POST['nom_absent'];
                                $this->db->ajout_absence($date, $nom_absent);					
                                
                                } else { 
 
						header('HTTP/1.1 500 Looks like mysql error, could not insert record!');
						exit();
				}
                    
    }
    

    public function sauvefluxAction()
    {
	    	if(!$this->dbD)$this->dbD = new Model_DbTable_Flux_Doc();
	    	$trombi = $this->_getParam('trombi', 0);
	    	if($trombi){
	    		$chemin = $_SERVER['DOCUMENT_ROOT']."/AmeurSaadMAnsour/data/trombi/".$trombi;
	    		$this->view->chemin = $chemin;
	    		$this->view->path = "http://".$_SERVER['HTTP_HOST'];//.$_SERVER['REQUEST_URI'];
	    		$url = $this->rss[$trombi];
	    		//vérifier si le rss est enregistré
	    		$rs = $this->dbD->findByUrl($url);
	    		if(!count($rs)){
				//charge le flux rss dans un objet PHP				
		    		$xml = simplexml_load_file($url);
		    		$titre = $xml->channel->title;
		    		//enregistre le flux rss comme document
				$data = array("url"=>$url, "titre"=>$titre, "data"=>$xml->asXML());
			    	//print_r($data);	    	
		    		$rs = $this->dbD->ajouter($data,true,true);
	    		}else{
	    			$rs = $rs[0];    			
	    			$xml = simplexml_load_string($rs['data']);
	    		}
			$this->view->doc = $rs;
			//
	    		foreach ($xml->channel->item as $tof) {
	    			//print_r($tof);
				$this->sauveImage($rs['doc_id'], $tof->enclosure["url"]."", $tof->title."", $chemin);
			}
	    	}
    }
    
    
     /**
     * sauveImage
     *
     * enregistre l'image du document
     * 
     * @param int $idDocParent
     * @param string $url
     * @param string $titre
     * @param string $chemin
     * 
     * @return int
     */
	function sauveImage($idDocParent, $url, $titre, $chemin){

	    	if(!$this->dbD)$this->dbD = new Model_DbTable_Flux_Doc();
	    	if(!$this->dbDT)$this->dbDT = new Model_DbTable_Flux_DocTypes();
	    	if(!$this->dbUD)$this->dbUD = new Model_DbTable_flux_utidoc();
    	
	    	//création du répertoire de stockage de l'image
		if(!is_dir($chemin)) @mkdir($chemin,0777,true);
    			
		//création des données du document
		$extension = pathinfo($url, PATHINFO_EXTENSION);
    		$type = $this->dbDT->getIdByExtension($extension);
    		$arrDoc['type']=$type;
    		$arrDoc['titre']=$titre;
    		$arrDoc['parent']=$idDocParent;    	
	    	//ajoute le document
	    	$idDoc = $this->dbD->ajouter($arrDoc);

		$path = $chemin."/".$idDoc.".".$extension;
		$urlLocal = str_replace($_SERVER['DOCUMENT_ROOT'], $this->view->path, $path);     	
		if(!is_file($path)){
    		//enregistre l'image sur le disque local
			if(!$img = file_get_contents($url)) { 
			  echo 'pas de fichier : '.$url."<br/>";
			}else{
				if(!$f = fopen($path, 'w')) { 
				  echo 'Ouverture du fichier impossible '.$path."<br/>";
				}elseif (fwrite($f, $img) === FALSE) { 
				  echo 'Ecriture impossible '.$path."<br/>";
				}else{
					echo 'Image '.$titre.' enregistrée : <a href="'.$urlLocal.'">local</a> -> <a href="'.$url.'">En ligne</a><br/>';
				} 				
			}				
		}else{
			echo 'Image '.$titre.' enregistrée : <a href="'.$urlLocal.'">local</a> -> <a href="'.$url.'">En ligne</a><br/>';
		} 
	    	$this->dbD->edit($idDoc, array("url"=>$urlLocal));
		
	    	//création des liens avec le flux
	    //$this->dbUD->ajouter(array("doc_id"=>$idDoc,"uti_id"=>$this->user));
    	    	    	
		return $idDoc;   	
	} 	
    
}

