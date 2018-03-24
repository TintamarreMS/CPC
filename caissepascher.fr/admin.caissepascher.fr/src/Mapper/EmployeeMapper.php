<?php

    class EmployeeMapper extends Mapper{

        function __construct() {
            parent::__construct();
        }
        
        
        /**
        * Search old employee by user id - Read
        * @param Array $where
        * @return Array
        */
        public function showsEmployeeByUser($where, $active = true) {
            $where["isActive"] = $active;
            $resulta = $this->dbSelect("Employee", "", $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }

        public function searchEmployee($where) {
            $where["isDelete"] = 0;
            $resulta = $this->dbSelect("employee", array('idEmployee', 'firstname', 'lastname', 'isActive', 'created'), $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
        public function isEmployeeToUser($userId, $employeeId) {
            $result = $this->dbSelect("employee", "", array("cash_desk_users_idUsers" => $userId, "idEmployee" => $employeeId, "isDelete" => 0));
            if(count($result) >= 1)
                return true;
            else
                return false;
        }
        
        public function updateEmployee($update, $where) {
            $this->dbUpdate("employee", $update, $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }

        public function createEmployee($data) {
            $this->dbInsert("employee", $data);
            if($this->rows_affected)
                return true;
            else
                return false;
        }
    }