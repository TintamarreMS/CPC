<?php

    class TransactionMapper extends Mapper{

        public $status = array('Initialiser','En cours','Terminer','Annuler');

        function __construct() {
            parent::__construct();
        }
        
        /**
        * Create new transaction - Create
        * @param Array $where
        * @return Array
        */
        public function createTransaction($data) {
            $data["reference_client"] = $this->numTransactionToUser($data["cash_desk_users_idUsers"])+1;
            $this->dbInsert("transaction", $data);
            if($this->rows_affected)
                return $this->last_insert_id;
            else
                return false;
        }

        public function searchTransactionDate($idUser, $count = false, $between = false) {

            $select = (!$count)?"SELECT idTransaction as id,reference_client as reference,amount,status,cancelled,CONCAT(firstname,' ', lastname) as employee,transaction.created,updated":"SELECT SUM(amount) as total ";

            $from = " FROM transaction LEFT JOIN employee ON transaction.employee_idEmployee = employee.idEmployee  WHERE cancelled = 0 AND  transaction.cash_desk_users_idUsers = ".$idUser;

            $where = (is_array($between))?" AND transaction.created BETWEEN '".$between[0]."' AND '".$between[1]."'":"";
            // return $select.$from.$where;
            return $this->dbExecuteQuery($select.$from.$where);
        }
        
        /**
        * Search old transaction - Read
        * @param Array $where
        * @return Array
        */
        public function searchTransaction($whereCondition, $between = false) {
            $this->backticks="";
            $tables = array("transaction", "employee");
            $joins = array("transaction.employee_idEmployee = employee.idEmployee");
            $join_condition = array("LEFT JOIN");
            $columns = array('idTransaction as id', 'reference_client as reference', 'amount', 'status', 'cancelled', 'CONCAT(firstname," ", lastname) as employee', 'transaction.created', 'updated');
            $resulta = $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
         /**
        * Search old transaction - Read
        * @param Array $where
        * @return Array
        */
        public function PrintTransaction($userId,$type) {
             
             
            if(isset($type) && ($type != ''))   $whereCondition = array("transaction.cash_desk_users_idUsers" => $userId, "transaction.status"=>''.$type.'');
            else                                $whereCondition = array("transaction.cash_desk_users_idUsers" => $userId);
          
          
            $this->backticks="";
            $tables = array("employee",
                           "transaction",
                           "clients",
                           "Transaction_has_Product_or_Services",
                           "product_or_service",
                           "Transaction_has_Payments",
                           "UserConfigs_has_PaymentMean");
           
            $joins = array("transaction.employee_idEmployee = employee.idEmployee",
                           "transaction.reference_client=clients.idClients",
                           "transaction.idTransaction=Transaction_has_Product_or_Services.Transaction_idTransaction",
                           "product_or_service.idProduct_Services=Transaction_has_Product_or_Services.Product_or_Service_idProduct_Services",
                           "transaction.idTransaction=Transaction_has_Payments.Transactions_idTransaction1",
                           "Transaction_has_Payments.PaymentMean_idPaymentMean=UserConfigs_has_PaymentMean.UserConfigs_has_PaymentMean_Id");
           
            $join_condition = array("LEFT JOIN","LEFT JOIN","LEFT JOIN","LEFT JOIN","LEFT JOIN","LEFT JOIN");
          
            $columns = array('idTransaction as id', 
                             'clients.idClients as idclt',
                             'CONCAT(clients.firstname," ",clients.lastname) as fname',
                             'clients.email as mail',
                             'clients.phonenumber as phone', 
                             'transaction.status as stat', 
                             'transaction.cancelled as canc',
                             'transaction.created', 
                             'transaction.updated', 
                             'CONCAT(employee.firstname," ",employee.lastname) as employee', 
                             'transaction.amount',
                             'Transaction_has_Product_or_Services.Product_or_Service_idProduct_Services as pdt',
                             'product_or_service.label as pdt_nom',
                             'product_or_service.preTaxPrice as pdt_price',
                             'UserConfigs_has_PaymentMean.PaymentLabel as lab',
                             'Transaction_has_Payments.Amount as p_p',
                             'Transaction_has_Payments.idPayment as id_p',
                             );
            
            $result = $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
     
    
            if($this->rows_returned)
                return $result;
            else
                return false;
                
           
         
           
        }
        
        /**
        * Search Item transaction - Read
        * @param Array $where
        * @return Array
        */
        public function searchItemTransaction($whereCondition) {
            $this->backticks="";
            $tables = array("Transaction", "Transaction_has_Product_or_Services", "Product_or_Service");
            $joins = array("Transaction.idTransaction = Transaction_has_Product_or_Services.Transaction_idTransaction", "Transaction_has_Product_or_Services.Product_or_Service_idProduct_Services = Product_or_Service.idProduct_Services");
            $join_condition = array("LEFT JOIN", "LEFT JOIN");
            $columns = array("Product_or_Service.*");
            $resulta = $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
        /**
        * Delete  old Transaction - Upadate
        * @param Array $where
        * @return Array
        */
        public function cancelTransaction($where) {
            $result = $this->dbSelect("transaction", "", $where)[0]; 
            $this->dbUpdate("transaction", array("status" => "Annuler", "cancelled" => 1), $where);
            $this->dbInsert("transaction", array("amount" => $result["amount"], "reference_client" => $this->numTransactionToUser($where["cash_desk_users_idUsers"])));
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        /**
        * Delete  old Transaction - Upadate
        * @param Array $where
        * @return Array
        */
        public function DemandeCancelTransaction($where) {
            $this->dbUpdate("transaction", array("status" => "Annuler", "cancelled" => 1), $where);
            if($this->rows_affected){
                return true;
            }else
                return false;
        }
        
        public function transactionHasPayments($idTransaction, $payments) {
            foreach($payments as $payment){
                $this->dbInsert("Transaction_has_Payments", array("Transactions_idTransaction1" => $idTransaction, "PaymentMean_idPaymentMean" => $payment->paymentMeanID, "Amount" => $payment->amount));
            }
            if($this->rows_affected){
                return true;
            }else
                return false;
        }
        
        public function transacticonHasPayments($idUser, $between = true, $type = 0) {

            if($type == 2){
                $select = "SELECT Label, SUM( Transaction_has_Payments.amount ) AS amount FROM transaction INNER JOIN Transaction_has_Payments ON Transaction_has_Payments.Transactions_idTransaction1 = transaction.idTransaction INNER JOIN paymentMean ON paymentMean.idPaymentMean = Transaction_has_Payments.PaymentMean_idPaymentMean WHERE cancelled = 0 AND transaction.cash_desk_users_idUsers = ".$idUser." AND transaction.created BETWEEN '".$between[0]."' AND '".$between[1]."' GROUP BY PaymentMean_idPaymentMean";
            }else{
                $select = "SELECT CONCAT(firstname, ' ', lastname) as label, SUM( Transaction_has_Payments.amount ) AS amount FROM transaction INNER JOIN Transaction_has_Payments ON Transaction_has_Payments.Transactions_idTransaction1 = transaction.idTransaction INNER JOIN employee ON employee.idEmployee = transaction.employee_idEmployee WHERE cancelled = 0 AND transaction.cash_desk_users_idUsers = ".$idUser." AND transaction.created BETWEEN '".$between[0]."' AND '".$between[1]."' GROUP BY label";
            }
            return $this->dbExecuteQuery($select);

        }
        
        
        /*-------------------------------------------------ADMIN------------------------------------*/
        public function nbTransactionToUser($id, $type) {
            $this->backticks="";
            $result = $this->dbSelect("transaction", array("Count(*) as total"), array("cash_desk_users_idUsers" => $id, "status" => $this->status[$type]));
            if($this->rows_returned)
                return $result;
            else
                return false;
        }


        /*******************************************************************************/
        public function numTransactionToUser($id) {
            $this->backticks="";
            $result = $this->dbSelect("transaction", array("Count(*) as total"), array("cash_desk_users_idUsers" => $id));
            if($this->rows_returned)
                return (int)$result[0]["total"];
            else
                return false;
        }

        public function paymentMean() {
            $query = "SELECT idPaymentMean as ID,Label as label FROM paymentMean";
            return $this->dbExecuteQuery($query);
        }

    }