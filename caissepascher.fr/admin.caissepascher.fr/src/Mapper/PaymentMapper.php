<?php

    class PaymentMapper extends Mapper{

        function __construct() {
            parent::__construct();
        }
        
        
           
        /**
        * Search old employee by user id - Read
        * @param Array $where
        * @return Array
        */
        public function showsPaymentByUser($where) {
            $resulta = $this->dbSelect("UserConfigs_has_PaymentMean", "", $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }

        public function searchPayment($where) {
            $resulta = $this->dbSelect("UserConfigs_has_PaymentMean", array('UserConfigs_has_PaymentMean_Id', 'Id_User', 'PaymentLabel'), $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
        public function isPaymentToUser($userId, $PaymentId) {
            $result = $this->dbSelect("UserConfigs_has_PaymentMean", "", array("Id_User" => $userId, "UserConfigs_has_PaymentMean_Id" => $PaymentId));
            if(count($result) >= 1)
                return true;
            else
                return false;
        }
        
        public function updatePayment($update, $where) {
            $this->dbUpdate("UserConfigs_has_PaymentMean", $update, $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }

        public function createPayment($data) {
            $this->dbInsert("UserConfigs_has_PaymentMean", $data);
            if($this->rows_affected)
                return true;
            else
                return false;
        }

        
     
    }