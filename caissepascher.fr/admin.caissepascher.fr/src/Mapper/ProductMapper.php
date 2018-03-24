<?php

    class ProductMapper extends Mapper{

        function __construct() {
            parent::__construct();
        }
        
        
        /**
        * Search old product or service - Read
        * @param Array $where
        * @return Array
        */
        public function showsProductByUser($where) {
            $resulta = $this->dbSelect("Product_or_Service", "", $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
        public function updateProduct($update, $where) {
            $this->dbUpdate("product_or_service", $update, $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        
        public function desactivateProduct($id) {
            $this->dbUpdate("product_or_service",array("isActive"=>"0") ,  array("idProduct_Services" => $id));
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        
        
         public function activateProduct($id) {
            $this->dbUpdate("product_or_service",array("isActive"=>"1") ,  array("idProduct_Services" => $id));
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        
        public function createProduct($data) {
            $this->dbInsert("product_or_service", $data);
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        /*-------------------------------------------------ADMIN------------------------------------*/
        public function nbProductToUser($userId) {
            $this->backticks="";
            $result = $this->dbSelect("product_or_service",array("Count(*)"),array("cash_desk_users_idUsers" => $userId, "isActive" => 1));
            if($this->rows_returned)
                return $result;
            else
                return false;
        }

        public function searchProducts($whereCondition) {
            $this->backticks="";
            $this->group_by_column="idProduct_Services";
            $this->order_by_column="idcategories";
            $tables=array("product_or_service", "categories");
            $joins=array("product_or_service.categories_idcategories = categories.idcategories");
            $join_condition=array("LEFT JOIN");
            $columns=array("idProduct_Services as Id", "product_or_service.label as label", "preTaxPrice as Prix", "VAT as Tax", "product_or_service.isActive as isActive", "product_or_service.created as created", "categories.label as categorie");
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
        }
        
        public function isProductToUser($userId, $productServicesId) {
            $result = $this->dbSelect("product_or_service", "", array("cash_desk_users_idUsers" => $userId, "idProduct_Services" => $productServicesId));
            if(count($result) >= 1)
                return true;
            else
                return false;
        }
        
        public function searchCategorie($userId) {
            $result = $this->dbSelect("categories", "", array("cash_desk_users_idUsers" => $userId, "isdelete" => 0));
            if($this->rows_returned)
                return $result;
            else
                return false;
        }
    }