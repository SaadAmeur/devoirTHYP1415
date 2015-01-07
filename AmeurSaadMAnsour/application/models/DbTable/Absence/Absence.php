<?php
/**
 * Ce fichier contient la classe Absence.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
*/

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Classe ORM qui représente la table 'absence'.
 *
 * @copyright  2008 Gabriel Malkas
 * @license    "New" BSD License
 */
class Model_DbTable_Absence_Absence extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'absence';
    
    /*
     * Clé primaire de la table.
     */
    protected $_primary = 'id';
    
    /**
     * Recherche une entrée Absence avec la clé primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public static function edit($id, $data)
    {        
        $db = Zend_Registry::get('dbAdapter');
        $db->update('absence', $data, 'absence.id = ' . $id);
    }
    
    /**
     * Recherche une entrée Absence avec la clé primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public static function remove($id)
    {
        $db = Zend_Registry::get('dbAdapter');
        $db->delete('absence', 'absence.id = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Absence avec certains critères
     * de tri, intervalles
     */
    public static function get($order=null, $limit=0, $from=0)
    {
        $db = Zend_Registry::get('dbAdapter');
        
        $query = $db->select()
                    ->from( array("%ftable%" => "absence") );
                    
        if($order != null)
        {
            $query->order($order);
        }

        if($limit != 0)
        {
            $query->limit($limit, $from);
        }

        return $db->fetchAll($query);
    }
    
    /*
     * Recherche une entrée Absence avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id
     */
    public static function findById($id)
    {
        $db = Zend_Registry::get('dbAdapter');

        $query = $db->select()
                    ->from( array("a" => "absence") )                           
                    ->where( "a.id = " . $id );

        return $db->fetchRow($query); 
    }
    /*
     * Recherche une entrée Absence avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param date $date
     */
    public static function findByDate($date)
    {
        $db = Zend_Registry::get('dbAdapter');

        $query = $db->select()
                    ->from( array("a" => "absence") )                           
                    ->where( "a.date = " . $date );

        return $db->fetchRow($query); 
    }
    /*
     * Recherche une entrée Absence avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom_absent
     */
    public static function findByNom_absent($nom_absent)
    {
        $db = Zend_Registry::get('dbAdapter');

        $query = $db->select()
                    ->from( array("a" => "absence") )                           
                    ->where( "a.nom_absent = " . $nom_absent );

        return $db->fetchRow($query); 
    }
    
   public static function ajout_absence($date,$nom_absent){
	try {
		//$db = Zend_Registry::get('dbAdapter');
		
		$resultat= $this->prepare("INSERT INTO absence (date,nom_absent) VALUES (?,?)");
		$resultat->bindValue(1, $date, PDO::PARAM_INT);    
		$resultat->bindValue(2, $nom_absent, PDO::PARAM_STR);    
		$resultat->execute();
		return true;
	} 
	catch (PDOException $exc) 
	{
		echo $exc->getMessage();
		return false;
	}  
}
    
}
