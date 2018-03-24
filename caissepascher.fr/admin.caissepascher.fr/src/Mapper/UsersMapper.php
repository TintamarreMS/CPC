<?php

    class UsersMapper extends Mapper{

        function __construct() {
            parent::__construct();
        }
        
       
       
        public function isValidApiKey($user) {
            $tables=array("users", "tokens");
            $joins=array("users.idUsers = tokens.users_idUsers");
            $join_condition=array("LEFT JOIN");
            $columns=array("idUsers", "token_refresh", "actived");
            $whereCondition=array("type" => $user["type"], "token" => $user["token"]);
            $resulta = $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
            if(count($resulta) <= 1){
                if (strtotime($resulta[0]["token_refresh"]) - $_SERVER["REQUEST_TIME"]  > 0)
                    if($resulta[0]["actived"] == 1)
                        return $resulta[0]["idUsers"];
            }
            return false;
        }
        
        

        /**
        * Register un nouvel utilisateur 
        * @param Array $user
        * @return Array
        */
        public function registerUsers($user) {
            if(!$this->checkUsername($user["username"]))
                return false;
            $data = array("username" => trim($user["username"]), "lastname" => trim($user["lastname"]), "firstname" => trim($user["firstname"]), "compagny_name" => trim($user["compagny_name"]));

            if(isset($user["street"]) && trim($user["street"]) != "")
                array_push($data, trim($user["street"]));
            if(isset($user["zipcode"]) && trim($user["zipcode"]) != "")
                array_push($data, trim($user["zipcode"]));
            if(isset($user["city"]) && trim($user["city"]) != "")
                array_push($data, trim($user["street"]));
            if(isset($user["siret"]) && trim($user["siret"]) != "")
                array_push($data, trim($user["siret"]));

            $this->dbInsert("users", $data);
            if($this->rows_affected){
                $userId = $this->last_insert_id;
                $this->dbInsert("administrator", array("User_idUser" => $userId, "email" => $user["email"], "forfait_idforfait" => $user["forfait"], "encryptpass" => $user["passwordAdmin"]));
                if($this->rows_affected){
                    $this->dbInsert("cash_desk", array("User_idUser" => $userId, "encryptpass" => $user["passwordDesk"]));
                    if($this->rows_affected)
                        if($this->generateTokenUsers($userId))
                            return $this->selectUsersToken($userId, $user["grant_type"]);
                }
            }
            return false;
        }
        
        
        
        
        /**
        * Register un nouvel utilisateur 
        * @param Array $user
        * @param $id
        * @return Array
        */
        public function registerUserAdmin($user,$id,$mail) {
               
                
           
            $username = $this->random_username($user["firstname"]." ".$user["lastname"]);
            if($this->checkUsername($username))    $username = $this->random_username($username);
            
           
            $data = array("username" => $username, 
                          "lastname" => trim($user["lastname"]), 
                          "firstname" => trim($user["firstname"]),
                          "compagny_name"=>trim($user["compagny_name"]),
                          "property"=>$id);
                          
            $email = $user["email"];
            
            
            $pass = $this->randomPassword(8,1,"lower_case,upper_case,numbers,special_symbols");
            
            $encrypt = $this->password($pass[0]);
            
            
            

            $this->dbInsert("users", $data);
  
            if($this->rows_affected)
            {
                $userId = $this->last_insert_id;
                
                $this->dbInsert("administrator", array("User_idUser" => $userId, "encryptpass" => $encrypt,"email" =>$email, "forfait_idforfait" =>"1"));
                    if($this->rows_affected)
                    {
                         $this->dbInsert("cash_desk", array("users_idUsers" => $userId,"location" =>"Salle", "encryptpass" => $encrypt));
                         
                         $part1        = file_get_contents(__DIR__ . '/../../mail/MailHead.phtml');
                         $part3        = file_get_contents(__DIR__ . '../../mail/MailFoot.phtml');
                         
                         $part2 = '<p class=MsoNormal style="margin-left:35.4pt"><o:p>Bonjour,'.trim($user["lastname"]).' '.trim($user["firstname"]).',</o:p></p>';
                         $part2 .= '<p class=MsoNormal style="margin-left:35.4pt"><o:p>Votre compte sur Caisse pas cher vient d\'&ecirc;tre cr&eacute;&eacute;.</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Voici vos identifiants  : </o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Identifiant : '.$username.'</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Mot de passe : '.$pass[0].'</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>&nbsp;</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Rendez vous sur <a href="http://www.caissepascher.fr">http://www.caissepascher.fr</a> pour vous y connecter.</o:p></p>';
                         
                         $body = $part1.$part2.$part3;
                         
                         
                         $mail->IsSMTP();    
                         $mail->SMTPAuth = true;                       // tell the class to use SMTP
	                     $mail->Port       = "25";                    // set the SMTP server port
	                     $mail->Host       = "smtp.caissepascher.fr";  // SMTP server
	                     $mail->Username   = "contact@caissepascher.fr";   // SMTP server username
	                     $mail->Password   = "cpc;2018@";           // SMTP server password
                 
                         $mail->SetFrom('contact@caissepascher.fr', 'Caisse pas cher');
                         $mail->Subject = "Créaction de votre compte caisse pas cher";
                         $mail->MsgHTML($body);
                         $mail->AddAddress($email);
                    
                         $mail->Send();
                         

                         if($this->generateTokenUsers($userId,1))
                            return $this->selectUsersToken($userId, true);
                    }
                       
                
            }

            return false;

            
        }
        
        
        
        public function CreateUserAdmin($user,$id,$admin,$mail) {
        
          $data = array(  "firstname" => $user["firstname"],
                          "lastname" => $user["lastname"], 
                          "username" => $user["username"], 
                          "compagny_name"=>$admin["compagny_name"],
                          "property"=>$id);
                          
          $encrypt = $this->password($user["pass"]);
          $email   = $user["email"];
                          
          $this->dbInsert("users", $data);
          
          if($this->rows_affected)
            {
                $userId = $this->last_insert_id;
                
                $this->dbInsert("administrator", array("User_idUser" => $userId, "encryptpass" => $encrypt,"email" =>$email, "forfait_idforfait" =>"1"));
                if($this->rows_affected)
                {
                     $this->dbInsert("cash_desk", array("users_idUsers" => $userId,"location" =>"Salle", "encryptpass" => $encrypt));
                    
                     if(isset($email) && ($email !='')){
                        
                         $part1        = file_get_contents(__DIR__ . '/../../mail/MailHead.phtml');
                         $part3        = file_get_contents(__DIR__ . '../../mail/MailFoot.phtml');
                         
                         $part2 = '<p class=MsoNormal style="margin-left:35.4pt"><o:p>Bonjour,'.$user["lastname"].' '.$user["firstname"].',</o:p></p>';
                         $part2 .= '<p class=MsoNormal style="margin-left:35.4pt"><o:p>Votre compte sur Caisse pas cher vient d\'&ecirc;tre cr&eacute;&eacute;.</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Voici vos identifiants  : </o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Identifiant : '.$user["username"].'</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Mot de passe : '.$user["pass"].'</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>&nbsp;</o:p></p>
                                    <p class=MsoNormal style="margin-left:35.4pt"><o:p>Rendez vous sur <a href="http://www.caissepascher.fr">http://www.caissepascher.fr</a> pour vous y connecter.</o:p></p>';
                         
                         $body = $part1.$part2.$part3;
                         
                         
                         $mail->IsSMTP();    
                         $mail->SMTPAuth = true;                       // tell the class to use SMTP
	                     $mail->Port       = "25";                    // set the SMTP server port
	                     $mail->Host       = "smtp.caissepascher.fr";  // SMTP server
	                     $mail->Username   = "contact@caissepascher.fr";   // SMTP server username
	                     $mail->Password   = "cpc;2018@";           // SMTP server password
                 
                         $mail->SetFrom('contact@caissepascher.fr', 'Caisse pas cher');
                         $mail->Subject = "Créaction de votre compte caisse pas cher";
                         $mail->MsgHTML($body);
                         $mail->AddAddress($email);
                    
                         $mail->Send();
                        
                     }
                     
                     if($this->generateTokenUsers($userId,0))  return $this->selectUsersToken($userId, true);
                }
            }
        
        }
        
        
        
        
        public function isPropertyToUser($userId, $adminId) {
            $result = $this->dbSelect("users", "", array("idUsers" => $userId, "property" => $adminId));
            if(count($result) >= 1)
                return true;
            else
                return false;
        }
        

        
        
        
        /**
        * Generation new token user
        * @param Array $where
        * @return Array
        */
        public function generateTokenUsers($userId, $type = 2) {
            switch ($type) {
                case 0:
                    $type = "client_credentials";
                    break;
                case 1:
                    $type = "admin_credentials";
                    break;
                default:
                    if(!$this->generateTokenUsers($userId, 0))
                        return false;
                    $type = "admin_credentials";
            }

            $this->dbInsert("tokens", array("token" => $this->generateApiKey($userId.$type.$userId), "token_refresh" => $this->DateStrtotimeTime(), "type" => $type, "users_idUsers" => $userId));
            // $this->dbInsert("tokens", array("token" => $this->generateApiKey($userId.$type.$userId), "token_refresh" => $this->DateStrtotimeTime(), "ip_adresse" => $this->get_ip, "type" => $type, "users_idUsers" => $userId));
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        
        
        
        
        
        /**
        * Recuperation data user
        * @param Array $where
        * @return Array
        */
        public function selectUsersToken($userId, $admin = false) {
            if($admin)
                $type = "admin_credentials";
            else
                $type = "client_credentials";
            $tables = array("users", "tokens");
            $joins = array("users.idUsers = tokens.users_idUsers");
            $join_condition = array("LEFT JOIN");
            $columns = array("token", "token_refresh");
            $whereCondition = array("actived" => 1, "type" => $type, "idUsers" => $userId);
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
        }


        public function selectUsers($userId) {
            $tables=array("users", "administrator", "cash_desk");
            $joins=array("users.idUsers = administrator.User_idUser", "users.idUsers = cash_desk.users_idUsers");
            $join_condition=array("LEFT JOIN","LEFT JOIN");
            $columns=array("username", "lastname", "firstname", "compagny_name", "street", "zipcode", "city", "siret", "email", "location", "reference");
            $whereCondition=array("idUsers" => $userId);
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
        }


        public function selectUsersAll($userId) {
            $result = $this->dbSelect("users","",array("idUsers" => $userId));
            if($this->rows_returned)
                return $result;
            else
                return false;
        }


        public function moyenPayment() {
            $result = $this->dbSelect("paymentMean","");
            if($this->rows_returned)
                return $result;
            else
                return false;
        }
        
        
        
        public function MymoyenPayment($userId) {
            $result = $this->dbSelect("UserConfigs_has_PaymentMean",array("UserConfigs_has_PaymentMean_Id","PaymentLabel"),array("Id_User" => $userId));
            if($this->rows_returned)
                return $result;
            else
                return false;
        }
        
        
        
        
         public function UpdateUserAdmin($user) {

             $this->dbUpdate("users",array("firstname"=>$user['firstname'],"lastname"=>$user['lastname'],"username"=>$user['username']), array("idUsers" => $user['iduser']));
          
            if($user['email'] != "")
            {
               $this->dbUpdate("administrator", array("email"=>$user['email']), array("User_idUser"=>$user));  
            }
            
            if(isset($user['pass']) && ($user['pass'] != '')){
            
                $encrypt = $this->password($user['pass']);
                $this->dbUpdate("administrator", array("encryptpass"=>$encrypt), array("User_idUser"=>$user['iduser']));
                $this->dbUpdate("cash_desk", array("encryptpass"=>$encrypt), array("users_idUsers"=>$user['iduser']));
           }
       
        }
        
        
        
        
        /**
        * Update old Users - Update
        * @param Array $where
        * @return Array
        */
        public function updateUsers($update, $where) {
            $this->dbUpdate("users", $update, $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        /**
        * Search old Users - Read
        * @param Array $where
        * @return Array
        */
        public function searchUsers($where) {
            $resulta = $this->dbSelect("Users", "", $where);
            if($this->rows_returned)
                return $resulta;
            else
                return false;
        }
        
        
        /**
        * @param $user
        * @return Array
        */
        public function searchMyUsers($user) {
            
            $tables=array("users", "administrator","tokens");
            $joins=array("users.idUsers = administrator.User_idUser","administrator.User_idUser=tokens.users_idUsers");
            $join_condition=array("LEFT JOIN","LEFT JOIN");
            $columns=array("idUsers", "lastname", "firstname","compagny_name","email","actived");
            $whereCondition=array("property" => $user);
            
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
            
        }
        
        
        
        /**
        * @param $user
        * @return Array
        */
        public function ReturnUser($user) {
            
            $tables=array("users", "administrator");
            $joins=array("users.idUsers = administrator.User_idUser");
            $join_condition=array("LEFT JOIN");
            $columns=array("idUsers", "lastname", "firstname","username","email");
            $whereCondition=array("idUsers" => $user);
            
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
            
        }
        
        
         /**
        * @param $user
        */
        public function GetLogin($user) {
            
            return $this->dbSelect("users", array("username"), array("idUsers" => $user));

        }
        
        
        /**
        * @param $user
        */
       
        public function UpdateUser($data,$user){
            
        // $result = $this->dbUpdate("users", array("username"=>$data['username']), array("idUsers"=>$user));   
         
         if(isset($data['pass']) && ($data['pass'] != '')){
            
            $encrypt = $this->password($data['pass']);
            return $this->dbUpdate("administrator", array("encryptpass"=>$encrypt), array("User_idUser"=>$user));
            
         }
         else return $result;
       }
        
        
       
        
        /**
        * @param $user
        */
        public function ActivateUser($user) {
            
            $this->dbUpdate("tokens", array("actived"=>"1"), array("users_idUsers"=>$user));
            
            if($this->rows_affected)
                return true;
            else
                return false;
            
        }
        
        
         /**
        * @param $user
        */
        public function DisactivateUser($user) {
            
            $this->dbUpdate("tokens", array("actived"=>"0"), array("users_idUsers"=>$user));
            
            if($this->rows_affected)
                return true;
            else
                return false;
            
        }
        
        
        /**
        * @param $user
        */
        public function DeleteUser($user) {
            
            $this->dbDelete("clients",array("cash_desk_users_idUsers"=>$user));
            $this->dbDelete("product_or_service",array("cash_desk_users_idUsers"=>$user));
            $this->dbDelete("categories",array("cash_desk_users_idUsers"=>$user));
            $this->dbDelete("employee",array("cash_desk_users_idUsers"=>$user));
            $this->dbDelete("tokens",array("users_idUsers"=>$user));
            $this->dbDelete("cash_desk", array("users_idUsers"=>$user));
            $this->dbDelete("UserConfigs_has_PaymentMean", array("Id_User"=>$user));
            $this->dbDelete("administrator", array("User_idUser"=>$user));
            $this->dbDelete("users", array("idUsers"=>$user));
            if($this->rows_affected)
                return true;
            else
                return false;
            
        }
        
        
        
        
        
        
        /**
        * Token to id
        * @param Array $where
        * @return Array
        */
        public function deleteUsers($where) {
            $this->dbDelete("Users", $where);
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        
        
        
        public function getAccessToken($user) {
            $user_id = $this->checkUsername($user["client_id"]);

            if(!$user_id)
                return false;

            if($user["grant_type"] == "admin_credentials")
                if(!$this->checkPassword($user_id, $user["client_secret"], "administrator"))
                    return false;
            elseif($user["grant_type"] == "client_credentials")
                if(!$this->checkPassword($user_id, $user["client_secret"], "cash_desk"))
                    return false;
            else
                return false;

            if(!$this->upgradeRefreshToken($user_id, $user["grant_type"]))
                if(!$this->generateTokenUsers($user_id, 0))
                    return false;
            
            return ($user["grant_type"] == "admin_credentials")?$this->selectUsersToken($user_id, true):$this->selectUsersToken($user_id);
        }


        public function champsToCashDesk($userId) {
            $tables=array("cash_desk", "cash_desk_has_champs", "champs");
            $joins=array("cash_desk.users_idUsers = cash_desk_has_champs.cash_desk_users_idUsers", "cash_desk_has_champs.champs_idchamps = champs.idchamps");
            $join_condition=array("LEFT JOIN","LEFT JOIN");
            $columns=array("idchamps", "libelle");
            $whereCondition=array("users_idUsers" => $userId);
            return $this->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
        }

        
        private function checkUsername($username) {
            $resulta = $this->dbSelect("users", array("idUsers"), array("username" => $username));
            return ($this->rows_returned)?$resulta[0]["idUsers"]:false;
        }

        private function checkPassword($user_id, $password, $table) {
            $nameId = ($table == "administrator")?"User_idUser":"users_idUsers";
            $resulta = $this->dbSelect($table, array("encryptpass"), array($nameId => $user_id));
            if($this->rows_returned)
                return (PassHash::check_password($resulta[0]["encryptpass"], $password)) ? true : false ;
            else
                return false;
        }

        public function password($password) {
            return PassHash::hash($password);
        }
        
        private function upgradeRefreshToken($user_id, $type) {
            $this->dbUpdate("tokens", array("token_refresh" => $this->DateStrtotimeTime(), "ip_adresse" => $this->get_ip()), array("users_idUsers" => $user_id, "type" => $type));
            if($this->rows_affected)
                return true;
            else
                return false;
        }
        
        
      /**
       * @param $length - the length of the generated password
       * @param $count - number of passwords to be generated
       * @param $characters - types of characters to be used in the password
      */  
  
 
      public function randomPassword($length,$count, $characters) {
 
         $symbols = array();
         $passwords = array();
         $used_symbols = '';
         $pass = '';
 
        // an array of different character types    
         $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
         $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
         $symbols["numbers"] = '1234567890';
         $symbols["special_symbols"] = '!?~@#-_+<>[]{}';
 
         $characters = split(",",$characters); // get characters types to be used for the passsword
         foreach ($characters as $key=>$value) {
                $used_symbols .= $symbols[$value]; // build a string with all characters
         }
          $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1
     
          for ($p = 0; $p < $count; $p++) {
        $pass = '';
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $symbols_length); // get a random character from the string with all characters
            $pass .= $used_symbols[$n]; // add the character to the password string
        }
           $passwords[] = $pass;
        }
     
        return $passwords; // return the generated password
    }
    
    
    
    function username_exist_in_database($username){
        
        $resulta = $this->dbSelect("users", array("idUsers"), array("username" => $username));

        if($this->rows_returned){
        }
}
    
    
    
    
    
    
    public function random_username($string_name, $rand_no = 200){

    while(true){
        $username_parts = array_filter(explode(" ", strtolower($string_name))); //explode and lowercase name
        $username_parts = array_slice($username_parts, 0, 2); //return only first two arry part
    
        $part1 = (!empty($username_parts[0]))?substr($username_parts[0], 0,4):""; //cut first name to 8 letters
        $part2 = (!empty($username_parts[1]))?substr($username_parts[1], 0,4):""; //cut second name to 5 letters
        $part3 = ($rand_no)?rand(0, $rand_no):"";
        
        $username = $part1. str_shuffle($part2). $part3; //str_shuffle to randomly shuffle all characters 
            return $username;
        
    }
}


         //FUNCTION TO SEND MAIL TO COMPTABILITY

          public function SendMail($mail_adress,$subject,$content="")
          {
                 require_once('../mail/class.phpmailer.php');
  
          
	             $mail = new PHPMailer(true); //New instance, with exceptions enabled
                // $part1        = file_get_contents( './mail/MailHead.phtml');
                // $part3        = file_get_contents('./mail/MailFoot.phtml');
         
	            // $body         = $part1.$content.$part3;
                
                 $body = $content;
                 $body         = preg_replace('/\\\\/','', $body); //Strip backslashes

	             
                 $mail->IsSMTP();    
                 $mail->SMTPAuth = true;                       // tell the class to use SMTP
	             $mail->Port       = "25";                    // set the SMTP server port
	             $mail->Host       = "smtp.caissepascher.fr";  // SMTP server
	             $mail->Username   = "contact@caissepascher.fr";   // SMTP server username
	             $mail->Password   = "cpc;2018@";           // SMTP server password
                 
                 $mail->SetFrom('contact@caissepascher.fr', 'Caisse pas cher');
                 $mail->Subject = "Créaction de votre compte caisse pas cher";
                 $mail->MsgHTML($body);
                 $mail->AddAddress($mail_adress);
                 
                 if($mail->Send()) {
                    echo "Message sent!";
                 } else {
                      echo "Mailer Error: " . $mail->ErrorInfo;
                  }

          }
  
   
}






