<?php

    class ReductionMapper extends Mapper{

        function __construct() {
            parent::__construct();
        }
        
        /**
        * Search old reduction by user id - Read
        * @param Array $where
        * @return Array
        */
        public function showsReductionByUser($where, $active = true) {
            $where["isActive"] = $active;
            $resulta = $this->dbSelect("reductions", "", $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
    }