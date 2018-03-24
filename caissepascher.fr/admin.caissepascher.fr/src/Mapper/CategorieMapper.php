<?php

    class CategorieMapper extends Mapper{

        function __construct() {
            parent::__construct();
        }
        
        
        /**
        * Search old Categorie by user id - Read
        * @param Array $where
        * @return Array
        */
        public function showsCategorieByUser($where, $active = true) {
            $resulta = $this->dbSelect("categories", "", $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }

        public function searchCategorie($whereCondition) {
            $this->order_by_column="children";
            $whereCondition["isdelete"] = 0;
            $resulta = $this->dbSelect("categories", array("idcategories", "label", "children", "faster"), $whereCondition);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
        public function isCategorieToUser($userId, $CategorieId) {
            $result = $this->dbSelect("categories", "", array("cash_desk_users_idUsers" => $userId, "isdelete" => 0, "idcategories" => $CategorieId));
            if(count($result) >= 1)
                return true;
            else
                return false;
        }
        
        public function updateCategorie($update, $where) {
            $this->dbUpdate("categories", $update, $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }

        public function createCategorie($data) {
            $this->dbInsert("categories", $data);
            if($this->rows_affected)
                return true;
            else
                return false;
        }
    }