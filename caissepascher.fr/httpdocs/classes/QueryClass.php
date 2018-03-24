<?php


class QueryClass {

	var $db;
	var $query = '';
	var $errors = array();


	
	/**
	 * Constructeur
	 *
	 */
	function __construct() {
	}

	
	/**
	 * Ouverture d'une connexion sur la base de donnees
	 */
	function Open_Connection() {
       
      $this->db =  dbConnect(DB_HOST,DB_HOST,DB_PASS,DB_NAME);
                                                        
       if(!$this->db)  $this->setErrors('La connexion à la base de données a échoué.  Erreur de connexion');
       
       return $this->db;
	}
    

	
	/**
	 * Fermeture de la connexion
	 */
	function Close_Connexion() {
		pg_close($this->db);
	}
    


	/**
	 * Retourne la requête courante
	 */	
	function getQuery() {
		return $this->query;
	}


	/**
	 * Retourne la ligne suivante d'une ressource, de manière associative
	 */	
	function fetch_assoc($result) {
		if (pg_result_status($result) != PGSQL_COMMAND_OK &&
				pg_result_status($result) != PGSQL_TUPLES_OK &&
				pg_result_status($result) != PGSQL_EMPTY_QUERY)
			$this->setErrors($query.'Erreur de sélection : '.pg_result_error($result));
		return @pg_fetch_assoc($result);
	}



	/**
	 * Enregistre une erreur
	 *
	 * @param $error  l'erreur
	 */	
	function setErrors($error) {
		array_push($this->errors,$error);
	}



	/**
	 * Retourne le tableau des erreurs
	 */		
	function getErrors() {
		return $this->errors;
	}



	/**
	 * Commence une transaction exclusive
	 */		
	function beginTran($table) {
		pg_query($this->db,"BEGIN WORK; LOCK ".$table." IN ACCESS EXCLUSIVE MODE");
	}

	/**
	 * Termine une transaction
	 */		
	function endTran() {
		pg_query($this->db,"COMMIT WORK;");
	}


	
	/**
	 * Requête de sélection
	 *
	 * @param $fields Les attributs de sélection
	 * @param $tables Les tables
	 * @param $conditions Clause 'where'
	 * @param $orderby Clause 'Order by'
	 * @param $groupby Clause 'Group by'
	 */	
	function qSelect($fields, $tables, $conditions='', $orderby='', $groupby='', $limit='', $returntype = 0) {
		
		$query = 'select ';
		
		if (!empty($fields)) {
			if (is_array($fields)) {
				for ($i = 0; $i < sizeof($fields); $i++) {
					$query .= $fields[$i] . ',';
				}
				
				$query = remLastChar($query,1);
			}
			else {
				$query .= $fields;
			}
				
			$query .= ' from ';
		}
		else {
			$this -> setErrors('Pas de champ défini!');
		}
		
		
                
                
                if (!empty($tables)) {
			if (is_array($tables)) {
				for ($i = 0; $i < sizeof($tables); $i++) {
					$query .= $tables[$i] . ',';
				}
			
				$query = remLastChar($query,1) . ' ';
			}
			else {
				$query .= $tables . ' ';
			}
		}
		else {
			$this -> setErrors('Pas de table définie!');
		}
		
		
                
                
                if (!empty($conditions)) {
		    $query .= 'where ';
			if (is_array($conditions)) 
                        {
                             if(sizeof($conditions)==1) $query .= $conditions[0].' ';

                             else
                             {

                             	    for ($i = 0; $i < sizeof($conditions); $i++)
                                         {
					  $egal1 = split('=',$conditions[$i+1]);
					  $egal2 = split('=',$conditions[$i]);

					     if (!strcmp($egal1[0],$egal2[0]))
						$query .= $conditions[$i] . ' or  ';
					    else
						$query .= $conditions[$i] . ' and ';
				          }
			
				     $query = remLastChar($query,4) . ' ';
	                      }
                       }
			 
			 


			  else {
				if (strcmp($conditions,''))
                                 $query .= $conditions . ' ';

			        }

		}
		
		
                
                
                
                if (!empty($groupby)) {
		    $query .= 'group by ';
			
			if (is_array($groupby)) {
				for ($i = 0; $i < sizeof($groupby); $i++) {
					$query .= $groupby[$i] . ',';
				}
			
				$query = remLastChar($query,1) . ' ';	
			}
			else {
				if (strcmp($groupby,'')) {
				    $query .= $groupby . ' ';
				}			
			}
		}
		
	
        
        
        
        	if (!empty($orderby) && ($orderby[0] != "")) {
		    $query .= 'order by ';
			
			if (is_array($orderby)) {
				for ($i = 0; $i < sizeof($orderby); $i++) {
					if ($orderby[$i] != "") {
						if (!strcmp(strtolower($orderby[$i]),'desc') || !strcmp(strtolower($orderby[$i]),'asc')) {
							$query = remLastChar($query,1);
								if (!strcmp(strtolower($orderby[$i]),'desc'))
						    	$query .= ' desc ,';
						    else
						    	$query .= ' asc ,';
						}
						else {
							$query .= $orderby[$i] . ' ';
						}
					}
				}
				$query = remLastChar($query,1) . ' ';
			}
			else {
				if (strcmp($orderby,'')) {
				    $query .= $orderby . ' ';
				}			
			}
		}
		
	
        
        
        	if (!empty($limit)) {
			$query .= 'limit ';
			
		    if (is_array($limit)) {
				for ($i = 0; $i < sizeof($limit); $i++) {
					$query .= $limit[$i] . ',';
				}
			
				$query = remLastChar($query,1) . ' ';	
			}
			else {
				if (strcmp($limit,'')) {
				    $query .= $limit . ' ';
				}			
			}
		}
		
		$this->query = $query;

		$result = @pg_query($this->db,$query) or $this->setErrors($query.'. Erreur de sélection '.pg_last_error($this->db));

                if (pg_result_status($result) != PGSQL_COMMAND_OK &&
				pg_result_status($result) != PGSQL_TUPLES_OK &&
				pg_result_status($result) != PGSQL_EMPTY_QUERY)
			$this->setErrors($query.'Erreur de sélection : '.pg_result_error($result));
			
		// Le retour peut être :
		if ($result) {
			switch($returntype) {
				case 0:
					// La collection de 'result'
					return $result;
							
				case 1:
					// La ligne suivante sous forme associative
					return @pg_fetch_assoc($result);
	
				case 2:
					// Le nombre de tuples trouvés
					return @pg_num_rows($result);
					
				case 3:
					// La requête
					return $this->query;
			}
		}
	}

//----------------------------------------------------------------------------------------------------
	
	/**
	 * Requête de mise à jour
	 *
	 * @param $tables La table
	 * @param $fields Les attributs à mettre à jour
	 * @param $values Les nouvelles valeurs  [exp_valeur indique que 'valeur' est une expression]
	 * @param $idfield Clause 'where'
	 * @param $idvalue Valeur de la clause 'where'
	 */		
	function qUpdate($table,$fields,$values,$idfield,$idvalue){
	
		if (!empty($table)) {
	
			if (!empty($fields)) {
		
			    if (is_array($fields)) {
				
					if (!empty($values)) {
				
						if (is_array($values)) {
						
							if (sizeof($fields) == sizeof($values)) {
						    	$len = sizeof($fields);
								
								$query = 'update '.$table.' set ';
							
								for ($i = 0; $i < $len; $i++) {
									$value_tab = explode('_',$values[$i]);
									if ($value_tab[0] == 'exp')
										$query .= $fields[$i] ."=". substr($values[$i],4) .",";
									else
										$query .= $fields[$i] ."='".$values[$i]."',";
								}
						
								$query = remLastChar($query,1) . ' where ';
								
								if (is_array($idfield)) {
									for ($i = 0; $i < sizeof($idfield); $i++) {
										if (!strcmp($idfield[$i+1],$idfield[$i])) {
											$query .= $idfield[$i].' = \''.$idvalue[$i].'\' or  ';
										}
										else {
											$query .= $idfield[$i].' = \''.$idvalue[$i].'\' and ';
										}
									}
								
									$query = remLastChar($query,4).' ';	
								}
								else {
									if (strcmp($idfield,'')) {
										$query .= $idfield.' = \''.$idvalue.'\' ';
									}
								}
								
								$this->query = $query;
								//test
								//echo "<br> qUpdate->query = " . $query;
								@pg_query($this->db,$query) or $this->setErrors($query.' Erreur de mise à jour '.pg_last_error($this->db));
								
								return;
							}
							else {
								$this-> setErrors('Le nombre de champ n\'est pas égale au nombre de valeurs!');
							}	
						}
					}
					else {
						$this-> setErrors('Pas de valeur définie');		
					}
			  }
			}
			else {
				$this-> setErrors('Pas de champ défini!');
			}    
		}
		else {
			$this-> setErrors('Pas de table définie!');
		}
	}

//----------------------------------------------------------------------------------------------------
	
	/**
	 * Requête d'insertion
	 *
	 * @param $tables La table
	 * @param $fields Les attributs de la table
	 * @param $values Les valeur à insérer [exp_valeur indique que 'valeur' est une expression]
	 */			
	function qInsert($table, $fields='', $values) {
		
		if (!empty($table)) {
		    
			if (!empty($fields)) {
				
				if (!empty($values)) {
					$query = 'insert into '.$table. ' (';
					
					if (is_array($fields)) {
						for ($i = 0; $i < sizeof($fields); $i++) {
							$query .= $fields[$i].',';
						}
												
						$query = remLastChar($query,1) . ') ';
						
					}
					else {
						$query .= $fields .') ';
					}
					
					$query .= 'values(';
					
					if (is_array($values)) {
					
						for ($i = 0; $i < sizeof($values); $i++) {
							$value_tab = explode('_',$values[$i]);
							if ($value_tab[0] == 'exp')
								$query .= substr($values[$i],4).",";
							else
								$query .= "'".$values[$i]."',";
						}
						
						$query = remLastChar($query,1) . ')';	
					}
					else {
						$query .= $values .')';
					}
				}
				else {
					$this-> setErrors('Pas de valeur définie!');	
				}
			
				$this->query = $query;

				$result = @pg_query($this->db,$query) or $this->setErrors($query.'Erreur d\'insertion  '.pg_last_error($this->db));
				return $result;
			}
			else {
				if (!empty($values)) {
					$query = 'insert into '.$table. ' values(';
					
					if (is_array($values)) {
					
						for ($i = 0; $i < sizeof($values); $i++) {
							$query .= "'".$values[$i]."',";
						}
						
						$query = remLastChar($query,1) . ')';	
					}
					else {
						$query .= $values .')';
					}
				}
				else {
					$this-> setErrors('Pas de valeur définie!');	
				}
				
				$this->query = $query;
				
				$result = @pg_query($this->db,$query) or $this->setErrors($query.'Erreur d\'insertion  '.pg_last_error($this->db));
			}
		}
		else {
			$this-> setErrors('Pas de table définie!');	
		}
	}

//----------------------------------------------------------------------------------------------------
	
	/**
	 * Requête de suppression
	 *
	 * @param $tables La table
	 * @param $idfield Identifiant de tuple
	 * @param $idvalue Valeur de l'identifiant
	 */		
	function qDelete($table,$idfield,$idvalue) {
		
		if (!empty($table)) {
			$query = 'delete from '.$table;

			if (!empty($idfield)) {
			    $query .= ' where ';
				
				if (is_array($idfield)) {
					for ($i = 0; $i < sizeof($idfield); $i++) 
					{
						if (isset($idfield[$i+1]) && (!strcmp($idfield[$i+1],$idfield[$i]))) 
						{    
							   $query .= $idfield[$i].' = '.$idvalue[$i].' or  ';
						}	
						else 
						{  
							$query .= $idfield[$i].' = '.$idvalue[$i].' and ';
						}
						
					}
				
					$query = remLastChar($query,4).' ';	
				}
				
				
				else {
					if (strcmp($idfield,'')) {
						$query .= $idfield.' = '.$idvalue.' ';
					}
				}
			}

			$this->query = $query;
		
			@pg_query($this->db,$query) or $this->setErrors($query.'Erreur de suppression  '.pg_last_error($this->db));
			return;			
		}
		else {
			$this-> setErrors('Pas de table définie!');	
		}
	}
    
    
 //----------------------------------------------------------------------------------------------------
	
	/**
	 * Requête de suppression avec operateurs de comparaison
	 *
	 * @param $tables La table
	 * @param $idfield Identifiant de tuple
     * @param $operator operateur entre $idfield et $idvalue ( ><= ...)
	 * @param $idvalue Valeur de l'identifiant
	 */		
	function qDeleteOperator($table,$idfield,$operator,$idvalue) {
		
		if (!empty($table)) {
			$query = 'delete from '.$table;

			if (!empty($idfield)) {
			    $query .= ' where ';
				
				if (is_array($idfield)) 
                {
					for ($i = 0; $i < sizeof($idfield); $i++) 
					{
						if (isset($idfield[$i+1]) && (!strcmp($idfield[$i+1],$idfield[$i]))) 
						{    
							   $query .= $idfield[$i].' '.$operator[$i].' '.$idvalue[$i].' or  ';
						}	
						else 
						{  
							$query .= $idfield[$i].' '.$operator[$i].' '.$idvalue[$i].' and ';
						}
						
					}
				
					$query = remLastChar($query,4).' ';	
				}
				
				
				else 
                {
					if (strcmp($idfield,'')) 
                    {
						$query .= $idfield.' '.$operator.' '.$idvalue.' ';
					}
				}
			}

			$this->query = $query;
		
			@pg_query($this->db,$query) or $this->setErrors($query.'Erreur de suppression   '.pg_last_error($this->db));
			return;			
		}
		else {
			$this-> setErrors('Pas de table définie!');	
		}
	}



//----------------------------------------------------------------------------------------------------
	
	/**
	 * Vidage d'une table
	 *
	 * @param $tables La table
	 */
	function qEmptyTable($table) {
		if (!empty($table)) {
			$query = 'delete * from '.$table;
			$this->query = $query;
			@pg_query($this->db,$query) or $this->setErrors($query.'Erreur de vidage   '.pg_last_error($this->db));
			return;			
		}
		else {
			$this-> setErrors('Pas de table définie!');	
		}
	}

//----------------------------------------------------------------------------------------------------
	
	/**
	 * Suppression d'une table
	 *
	 * @param $tables La table
	 */			
	function qDropTable($table) {
		if (!empty($table)) {
			$query = 'drop table '.$table;
			$this->query = $query;
			@pg_query($this->db,$query) or $this->setErrors($query.'Erreur de suppression de table   '.pg_last_error($this->db));
			return;			
		}
		else {
			$this-> setErrors('Pas de table définie!');	
		}
	}

//----------------------------------------------------------------------------------------------------
	
}
?>
