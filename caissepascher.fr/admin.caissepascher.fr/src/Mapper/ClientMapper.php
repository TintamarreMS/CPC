<?php

    class ClientMapper extends Mapper{

        function __construct() {
            parent::__construct();
        }
        
        /**
        * Create new client - Create
        * @param Array $where
        * @return Array
        */
        public function createClient($data) {
            $this->dbInsert("clients", $data);
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        /**
        * Update old client - Update
        * @param Array $where
        * @return Array
        */
        public function updateClient($update, $where) {
            $this->dbUpdate("clients", $update, $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }

        public function updateClientChampsPerso($champs, $idClients) {
            $error = false;
            for($i = 0; $i < count($champs); $i++){
                foreach($champs[$i] as $key => $val){
                    if($this->valeurChampsClientExiste($key, $idClients)){
                        $this->dbUpdate("clients_has_champs", array("valeur" => $val), array("champs_idchamps" => $key, "clients_idClients" => $idClients));
                        if($this->rows_affected)
                            $error = false;
                        else
                            $error = true;
                    }else{
                        $this->dbInsert("clients_has_champs", array("valeur" => $val, "champs_idchamps" => $key, "clients_idClients" => $idClients));
                        if($this->rows_affected)
                            $error = false;
                        else
                            $error = true;
                    }
                }
            }
            return $error;
        }
        
        /**
        * Search old client - Read
        * @param Array $where
        * @return Array
        */
        public function searchClient($where) {
            $resulta = $this->dbSelect("clients", array('idClients', 'firstname', 'lastname', 'postcode', 'email', 'phonenumber', 'created'), $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
        /**
        * Delete  old client - Read
        * @param Array $where
        * @return Array
        */
        public function deleteClient($where) {
            $this->dbDelete("clients", $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }


        /*-------------------------------------------------ADMIN------------------------------------*/
        public function nbClientToUser($userId) {
            $this->backticks="";
            $result = $this->dbSelect("clients",array("Count(*)"),array("cash_desk_users_idUsers" => $userId));
            if($this->rows_returned)
                return $result;
            else
                return false;
        }

        public function isClientToUser($userId, $clientId) {
            $result = $this->dbSelect("clients","",array("cash_desk_users_idUsers" => $userId, "idClients" => $clientId));
            if(count($result) >= 1)
                return true;
            else
                return false;
        }

        public function valeurChampsClientExiste($champsId, $clientId) {
            $result = $this->dbSelect("clients_has_champs","",array("champs_idchamps" => $champsId, "clients_idClients" => $clientId));
            if(count($result) >= 1)
                return true;
            else
                return false;
        }

        public function valeurChampsClient($clientId) {
            $tables=array("clients", "clients_has_champs", "champs");
            $joins=array("clients.idClients = clients_has_champs.clients_idClients", "clients_has_champs.champs_idchamps = champs.idchamps");
            $join_condition=array("LEFT JOIN","LEFT JOIN");
            $columns=array("idchamps", "libelle", "valeur");
            $whereCondition=array("idClients" => $clientId);
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
        }

        public function valeurChampsUser($userId) {
            $tables=array("cash_desk", "cash_desk_has_champs", "champs");
            $joins=array("cash_desk.users_idUsers = cash_desk_has_champs.cash_desk_users_idUsers", "cash_desk_has_champs.champs_idchamps = champs.idchamps");
            $join_condition=array("LEFT JOIN","LEFT JOIN");
            $columns=array("idchamps", "libelle", "valeur");
            $whereCondition=array("users_idUsers" => $userId);
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
        }
    }