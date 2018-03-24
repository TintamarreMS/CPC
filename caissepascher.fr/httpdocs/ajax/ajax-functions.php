<?php



include("../config/config.php");
include("../".CLASSES."/".BDPDO);
include("../".CLASSES."/".PASSHASH);
include("../invoicer/phpinvoice.php");




/**
 * Login function
 * 
 * @param    $login : user login
 * @param    $pass  : user password
 *
*/
 
function user_login($login="",$pass=""){
   
    $login = $_POST['user_log'];
    $pass  = $_POST['user_pwd'];
    
    $error = 0;
    
 
  
    $Query = new SimplifiedDB();
    $Hash  = new PassHash();
    $Query->dbConnect();
    
    $tables=array("users", "tokens");
    $joins=array("users.idUsers = tokens.users_idUsers");
    $join_condition=array("LEFT JOIN");
    $columns=array("idUsers");
    $whereCondition=array("username" => $login,"actived"=>"1");
    
    $result =  $Query->dbSelectJoin($tables,$joins,$join_condition,$columns,$whereCondition);
         
  //  $result = $Query->dbSelect("users", array("idUsers"), array("username" => $login,"active")); 
    
    if(isset($result[0]["idUsers"]) && ($result[0]["idUsers"]!=''))
    {  
         $res    = $Query->dbSelect("administrator", array("encryptpass"), array("User_idUser" => $result[0]["idUsers"])); 
         if(isset($res[0]["encryptpass"]))
         {
             $encrypt = $res[0]["encryptpass"];
         
              if ($Hash->check_password($encrypt, $pass))
              {
                  session_start();          
                 $_SESSION['user_id']  = $result[0]["idUsers"];
              }
             else $error = 1;
         }
         else $error= 1;
         
    }
    else $error = 2;
    
              
  print $error;
      
}



function Credentials(){

    require_once("../ajax/ajax-functions.php");
    if(!isset($_SESSION['user_id']) || ($_SESSION['user_id'] ==''))  echo "<script>window.location='../login';</script>";
}







function GetProduct($user){
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $req0 = "SELECT property FROM users WHERE idUsers=".$user; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
    $property = $res0[0]['property'];
    
    if(($property !='0')&&($property !='6'))  $theus = $property;
    else                 $theus = $user;
    
    
    $req = "SELECT product_or_service.idProduct_Services AS pdtid,
            product_or_service.label AS pdtlab,
            product_or_service.preTaxPrice AS pdtprice,
            product_or_service.VAT AS pdtvat,
            product_or_service.categories_idcategories AS pdtcatid,
            categories.label AS pdtcatlab 
            FROM product_or_service,categories 
             WHERE product_or_service.categories_idcategories=categories.idcategories 
             AND product_or_service.cash_desk_users_idUsers=".$theus." 
             AND product_or_service.isActive='1' 
             AND categories.isdelete='0'
             order by pdtcatlab,pdtlab ASC"; 
            
    return $Query->dbExecuteQuery($req,array());
        
}






function GetCategorieProduct($user){
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $req0 = "SELECT property FROM users WHERE idUsers=".$user; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
    $property = $res0[0]['property'];
    
    if(($property !='0')&&($property !='6'))  $theus = $property;
    else                 $theus = $user;
   
    $from   = "product_or_service";
    $select = array("idProduct_Services","label","preTaxPrice","VAT","categories_idcategories");
    $where  = array("cash_desk_users_idUsers" => $theus,"isActive"=>"1");

    return $Query->dbSelect($from, $select, $where);         
}





 
function open_modal_client(){

    session_start();
   
   if(isset($_SESSION['ClientId']) && ($_SESSION['ClientId']) != '')
   {
      
      $Query = new SimplifiedDB();
      $Query->dbConnect();
    
      $from   = "clients";
      $select = array("idClients","firstname","lastname","postcode","email","phonenumber");
      $where  = array("idClients"=>$_SESSION['ClientId']);
    
      $result =  $Query->dbSelect($from, $select, $where); 
 
      $id_clt     = $result[0]["idClients"];
      $nom_clt    = $result[0]["firstname"];
      $prenom_clt = $result[0]["lastname"];
      $cp_clt     = $result[0]["postcode"];
      $email_clt  = $result[0]["email"];
      $tel_clt    = $result[0]["phonenumber"];
      
      echo '<script>UserSelected();</script>';
      
   }
   else{
    
      $id_clt     = "";
      $nom_clt    = "";
      $prenom_clt = "";
      $cp_clt     = "";
      $email_clt  = "";
      $tel_clt    = "";
   }
   
   $back = '<div class="row">
                <div class="col s12 m12">
                  <div class="card-panel">
                    <h5 class="header1">Fiche client</h5>
                    <div class="row">
                      <form class="FormClient col s12" action="#">
                         
                         <div class="row">
                          
                          <div class="input-field col s6">
                             <i class="material-icons prefix ">search</i>
                             <input type="text" id="autocomplete-input" class="autocomplete" />
                             <label for="autocomplete-input">Rechercher</label>
                          </div>
                          
                          <div class="input-field col s6">
                             <input type="hidden" id="idClient" name="idClient" value="'.$id_clt.'"/>
                          </div>';
                          
                          if($id_clt!='')
                          {
                              $back.='<div class="input-field col s6">
                                         <a id="unuseClient" class="btn waves-effect waves-light right" style="visibility:visible;" Onclick="UnuseThisClient();">D&eacute;sactiver
                                         <i class="material-icons right">done</i>
                                         </a>
                                      </div>';
                          }
                          
                          
                   $back.='<div class="input-field col s6">
                                <a id="useClient" class="btn waves-effect waves-light right disabled" style="visibility:hidden" Onclick="UseThisClient();">Activer
                                  <i class="material-icons right">done</i>
                                </a>
                           </div>
                          
                          
                         </div>
                         <div class="row">
                            <div class="input-field col s6">
                                <i class="material-icons prefix">contact_phone</i>
                                <label for="telClient">N&deg; t&eacute;l</label>
                                <input id="telClient" name="telClient" type="text" data-error=".errorTxt1" value="'.$tel_clt.'">
                                <div class="errorTxt1"></div>
                           </div>
                        </div> 
                        
                        <div class="row">
                            <div class="input-field col s6">
                                <i class="material-icons prefix blue-color">contacts</i>
                            <label for="nomClient">Nom *</label>
                            <input id="nomClient" type="text" name="nomClient" data-error=".errorTxt2" value="'.$nom_clt.'">
                            <div class="errorTxt2"></div>
                           </div>
                           <div class="input-field col s6">
                               <i class="material-icons prefix blue-color">contacts</i>
                            <label for="prenomClient">Pr&eacute;nom *</label>
                            <input id="prenomClient" type="text" name="prenomClient" data-error=".errorTxt3" value="'.$prenom_clt.'">
                            <div class="errorTxt3"></div>
                          </div>
                        </div> 
                        
                         <div class="row">
                            <div class="input-field col s6">
                                <i class="material-icons prefix blue-color">my_location</i>
                                <label for="cpClient">Cp</label>
                                <input id="cpClient" type="text" name="cpClient" data-error=".errorTxt4" value="'.$cp_clt.'">
                                <div class="errorTxt4"></div>
                           </div>
                           <div class="input-field col s6">
                               <i class="material-icons prefix">contact_mail</i>
                               <label for="emailClient">E-mail</label>
                               <input id="emailClient" type="email" name="emailClient" data-error=".errorTxt5" value="'.$email_clt.'">
                               <div class="errorTxt5"></div>
                          </div>
                        </div> 
                        
                        <div class="input-field col s12">
                            <a class="btn waves-effect waves-light left modal-close">Fermer
                              <i class="material-icons right">close</i>
                            </a>
                            <button id="saveClient" class="btn waves-effect waves-light right newclt" onclick="NewClient();">Sauvegarder
                              <i class="material-icons right">done</i>
                            </button>
                       </div>
                        
                      </form>
                      
                    </div>
                  </div>
                </div>
              </div>
              
              <script>
              AutoCompleteUser();
              jQuery(document).ready(function($){
                 $("#telClient").keypress(function(event){
                  if(!isNumberOnly(event, this)) event.preventDefault();
                   });
                   
                   $("#cpClient").keypress(function(event){
                  if(!isNumberOnly(event, this)) event.preventDefault();
                   });
                
                });
              
              </script>
              ';


 
     print $back;
}



function open_modal_delete_transac(){
    
     $back = '<div class="row">
                <div class="col s12 m12">
                  <div class="card-panel">
                    <h5 class="header1">Annuler une transaction</h5>
                    <div class="row">
                      <form class="FormCancelTrans col s12" action="#">
                       <div class="row">
                           <div class="input-field col s6">
                             <input type="text" id="id_transac" name="id_transac" value=""/>
                             <label for="id_transac">N&deg; transaction</label>
                          </div>
                          
                          <div class="input-field col s6">
                                <button id="deltrans" class="btn waves-effect waves-light right newclt" onclick="DeleteTrans();">Annuler
                              <i class="material-icons right">remove_shopping_cart</i>
                            </button>
                           </div>
                        </div>
                       </form>
                       </div>
                  </div>
                </div>
              </div>';

     print $back;    
        
}


function open_modal_report()
{
   $back = '<div class="row">
                <div class="col s12 m12">
                  <div class="card-panel">
                    <h5 class="header1">Rapport des ventes</h5>
                    <div class="row">
   
                      
                    </div>
                  </div>
                </div>
              </div>
              

              ';

     print $back;   
}








function update_client(){
    
  session_start(); 
  
  $id      = $_POST['id'];
  $nom     = $_POST['fname'];  
  $prenom  = $_POST['lname']; 
  $tel     = $_POST['tel']; 
  $cp      = $_POST['cp']; 
  $email    = $_POST['email']; 
  
           
  $return = 0;               
  

  $Query = new SimplifiedDB();
  $Query->dbConnect();
  
  if(isset($id) && ($id!= '')) //Update
  {
      $table = "clients";
      $into  = array( "firstname"=>$nom,
                      "lastname"=>$prenom,
                      "postcode"=>$cp,
                      "email"=>$email,
                      "phonenumber"=>$tel);
      
      $where = array("idClients"=>$id);
                      
      $Query->dbUpdate($table,$into,$where);
  }
  else //Create
  {
      
     $req0 = "SELECT property FROM users WHERE idUsers=".$_SESSION['user_id']; 
     $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $_SESSION['user_id']; 
      
      $table = "clients";
      $into  = array( "firstname"=>$nom,
                      "lastname"=>$prenom,
                      "postcode"=>$cp,
                      "email"=>$email,
                      "phonenumber"=>$tel,
                      "cash_desk_users_idUsers"=>$theus);
                      
       $Query->dbInsert($table,$into);
  }
  
    
    
}





function give_clients(){
    
   session_start(); 
  
  $id = $_SESSION['user_id'];
              
  

  $Query = new SimplifiedDB();
  $Query->dbConnect(); 
  
     $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
     $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $id; 
  
  
  
  
  $table  = "clients";
  $select = array("idClients","firstname","lastname","phonenumber");
  $where  = array("cash_desk_users_idUsers"=>$theus,"isDelete"=>"0");
  
  $result = $Query->dbSelect($table, $select, $where); 
  
  $back = "";
  
  for($i=0;$i<sizeof($result);$i++)
  {
     if($back == "") $back .= '{"id":"'.$result[$i]['idClients'].'","firstname":"'.$result[$i]['firstname'].'","lastname":"'.$result[$i]['lastname'].'","phonenumber":"'.$result[$i]['phonenumber'].'"}';
     else $back .= ',{"id":"'.$result[$i]['idClients'].'","firstname":"'.$result[$i]['firstname'].'","lastname":"'.$result[$i]['lastname'].'","phonenumber":"'.$result[$i]['phonenumber'].'"}';

  }
  
  
  print "[".$back."]";
  
}




function client_select(){
  
 $user = $_POST['us'];
 
 $Query = new SimplifiedDB();
 $Query->dbConnect();
    
 $from   = "clients";
 $select = array("firstname","lastname","postcode","email","phonenumber");
 $where  = array("idClients"=>$user);
    
 $result =  $Query->dbSelect($from, $select, $where); 
 
 
 $nom    = $result[0]["firstname"];
 $prenom = $result[0]["lastname"];
 $cp     = $result[0]["postcode"];
 $tel    = $result[0]["phonenumber"];
 $email  = $result[0]["email"];  


    
 $back = '{ "fname":"'.$nom.'","lname":"'.$prenom.'","cp":"'.$cp.'","email":"'.$email.'","tel":"'.$tel.'"}';
    
 print $back;    
   
}



function client_use(){
    
  session_start();  

 $user = $_POST['us'];
    
 $Query = new SimplifiedDB();
 $Query->dbConnect();
    
 $from   = "clients";
 $select = array("firstname","lastname");
 $where  = array("idClients"=>$user);
    
 $result =  $Query->dbSelect($from, $select, $where); 
 
 
 $nom    = $result[0]["firstname"];
 $prenom = $result[0]["lastname"];
 
 $back = '{ "fname":"'.$nom.'","lname":"'.$prenom.'"}';

 $_SESSION['ClientId']    = $user;
 $_SESSION['ClientFname'] = $nom;
 $_SESSION['ClientLname'] = $prenom;
    
print $back;  
  
}  




function DisplayRemise(){
   
    session_start();  
    
    $id = $_SESSION['user_id'];
   
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
     $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $id; 
         
    return $Query->dbSelect("reductions", array("idReductions","label","DiscountPercentage","isPercentage","Amount"), array("cash_desk_users_idUsers" => $theus,"isActive" => "1")); 
       
}


function give_reduc(){
    
    $id = $_POST['id'];
    $Query = new SimplifiedDB();

    $Query->dbConnect();
         
    $result = $Query->dbSelect("reductions", array("DiscountPercentage","isPercentage"), array("idReductions"=>$id));

 
 
 $percent    = $result[0]["DiscountPercentage"];
 $ispercent  = $result[0]["isPercentage"];

 

  
   $back = '{ "percent":"'.$percent.'","ispercent":"'.$ispercent.'"}';
    
 print $back;     
}






function DisplayPrestatire(){
   
    session_start();  
    
    $id = $_SESSION['user_id'];
   
    $Query = new SimplifiedDB();

    $Query->dbConnect();
    
    
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $id; 
    
    
         
    return $Query->dbSelect("employee", array("idEmployee","firstname","lastname"), array("cash_desk_users_idUsers" => $theus,"isActive" => "1","isDelete"=>"0")); 
       
}



function Payement(){
    
   
    session_start();  
    
    $id = $_SESSION['user_id'];
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
      $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
      $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $id; 
         
    return $Query->dbSelect("UserConfigs_has_PaymentMean", array("UserConfigs_has_PaymentMean_Id","PaymentLabel"), array("Id_User" => $theus)); 
       
}





function save_transac(){
    
    session_start();  
    
    

    $employee = $_POST['prestataire'];
    $amount   = $_POST['mont'];
    $id       = $_SESSION['user_id'];
    if(isset($_SESSION['ClientId']) && ($_SESSION['ClientId'] != ''))  $client = $_SESSION['ClientId'];
    else                                                               $client = 0;
    
   
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                                      $theus = $id; 
    
    $table = "transaction";
    $into  = array(   "amount"=>$amount,
                      "status"=>'En cours',
                      "employee_idEmployee"=>$employee,
                      "cash_desk_users_idUsers"=>$theus,
                      "reference_client"=>$client);
                                    
      $last = $Query->dbInsert($table,$into);
      
      print $property;
}





function pay_transac(){
    
    session_start();  
    
    $id       = $_SESSION['user_id'];
    $employee = $_POST['prestataire'];
    $amount   = $_POST['mont']; 

    
    if(isset($_SESSION['ClientId']) && ($_SESSION['ClientId'] != ''))  $client = $_SESSION['ClientId'];
    else                                                               $client = 0;
    
    
     
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                                      $theus = $id; 
     
    
    $table = "transaction";
    $into  = array(   "amount"=>$amount,
                      "status"=>'Terminer',
                      "employee_idEmployee"=>$employee,
                      "cash_desk_users_idUsers"=>$theus,
                      "reference_client"=>$client);
                
    $last = $Query->dbInsert($table,$into);
    
    print $last;
}






function insert_transac_product(){
    
    $prod      = $_POST['prod'];
    $trans     = $_POST['trans'];
   
   
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $table = "Transaction_has_Product_or_Services";
    $into  = array(  "Product_or_Service_idProduct_Services"=>$prod,
                      "Transaction_idTransaction"=>$trans,
                      "quantity"=>"1");
                      
     $last = $Query->dbInsert($table,$into);
     print $last;
}




function insert_transac_payment(){
    
    $mont         = $_POST['mont'];
    $trans        = $_POST['trans'];
    $paymenttype  = $_POST['paymenttype'];
   
   
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $table = "Transaction_has_Payments";
    $into  = array(  "Amount"=>$mont,
                      "Transactions_idTransaction1"=>$trans,
                      "PaymentMean_idPaymentMean"=>$paymenttype);
                      
     $last = $Query->dbInsert($table,$into);
}





function TransWaiting(){
    
    $nb = 0;  
    
    $id       = $_SESSION['user_id'];
   
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
      $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $id; 
    
    $result =  $Query->dbSelect("transaction", array("idTransaction"), array("cash_desk_users_idUsers" => $theus,"status" => "En cours")); 
    
    for($i=0;$i<sizeof($result);$i++)
    {
        $nb++;
    }
    
    return $nb;
    
}


function open_modal_wait_transac(){
    
  session_start();
  
  $id = $_SESSION['user_id']; 
  
  $Query = new SimplifiedDB();
  $Query->dbConnect();
  
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $id; 
    
  $result =  $Query->dbSelect("transaction", array("idTransaction","amount","created"), array("cash_desk_users_idUsers" => $theus,"status" => "En cours")); 
  
  
  $back = '<div class="row">
                <a href="#"><i class="material-icons right" style="padding:5px 5px 5px 5px;" Onclick="CloseModal();">close</i></a>
                <div class="col s12 m12"> 
                  <div class="card-panel">
                    <h5 class="header1">Transactions en attente</h5>
                    <div class="row">
                      <form class="FormTrans col s12" action="#">
                      <br/>
                      <table class="striped"><thead><tr><th>R&eacute;f</th><th>Montant</th><th>Date</th><th></th></tr></thead>
                      <tbody>';
                        for($i=0;$i<sizeof($result);$i++)
                       {
                            $idtransac = $result[$i]['idTransaction'];
                            
                            $back .='<tr><td>'.$idtransac.'</td><td>'.$result[$i]['amount'].'&euro;</td><td>'.$result[$i]['created'].'</td><td><i class="material-icons delete" title="Supprimer d&eacute;finitivement" Onclick="ConfirmDeleteTransac('.$idtransac.');">delete_forever</i>&nbsp;&nbsp;<i class="material-icons delete" title="Editer" Onclick="EditTransac('.$idtransac.');">visibility</i></td></tr>';
                       } 

        $back .='</tbody>
                 </table>';
               
        $back .='</form>
                      
                    </div>
                  </div>
                </div>
              </div>';

     print $back;
    
  
}





function remove_transac(){
    
 $id = $_POST['trans'];
 
 $Query = new SimplifiedDB();
 $Query->dbConnect();
 
 $Query->dbDelete("Transaction_has_Product_or_Services",array("Transaction_idTransaction"=>$id));
 
 $Query->dbDelete("Transaction_has_Reductions",array("Transaction_idTransaction"=>$id));
 
 $Query->dbDelete("Transaction_has_Payments",array("Transactions_idTransaction1"=>$id));
 
 $Query->dbDelete("transaction",array("idTransaction"=>$id));
 
 print open_modal_wait_transac();
    
    
    
}






function insert_transac_remise()
{
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
 
    $trans = $_POST['trans'];
    $reduc = $_POST['reduc'];
    
     $table = "Transaction_has_Reductions";
     $into  = array( "Transaction_idTransaction"=>$trans,
                      "Reductions_idReductions"=>$reduc);
                      
     $Query->dbInsert($table,$into);
    
}




function cancel_trans(){
    
    session_start();
  
    $id = $_SESSION['user_id']; 
    
    
    $id_trans = $_POST['id_tr'];
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
      $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                 $theus = $id; 
    
     $result =  $Query->dbSelect("transaction", array("idTransaction","status","cancelled"), array("idTransaction" => $id_trans,"cash_desk_users_idUsers"=>$theus)); 
    
   if(isset($result[0]['idTransaction']))
   {
 
    $id_tr  = $result[0]['idTransaction'];
    $status = $result[0]['status'];
    $cancel = $result[0]['cancelled'];
    
    if(isset($id_tr) && ($id_tr !=''))
    {
       if($status=='En cours')     $back = "Cette transaction n&#146;a pas &eacute;t&eacute; valid&eacute;e!"; 
       elseif($status=='Annuler')  $back = "Cette transaction est d&eacute;j&agrave annul&eacute;e!"; 
       else{
             $table = "transaction";
             $into  = array("status"=>"Annuler",
                          "cancelled"=>"1");
      
             $where = array("idTransaction"=>$id_trans);
                      
             $Query->dbUpdate($table,$into,$where);
          
             $back = "Transaction annul&eacute;e!"; 
       }
    }
    else
    {
       $back = "Aucune transaction n&#146;a &eacute;t&eacute; trouv&eacute;e!";  
    }
    
  } 
  else  $back = "Aucune transaction n&#146;a &eacute;t&eacute; trouv&eacute;e!";  
  
  
   
    print $back;
      
    
}






function get_client_transac(){
    
    $id = $_POST['trans'];
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $req = "SELECT clients.idClients AS id,
                   clients.firstname AS firs,
            	   clients.lastname AS last
                   FROM clients,transaction WHERE clients.idClients=transaction.reference_client AND transaction.idTransaction=".$id; 
            
   $result =  $Query->dbExecuteQuery($req,array());
   
   if(isset($result[0]['id']) && ($result[0]['id']!=""))
   {
       print $result[0]['firs']." ".$result[0]['last'];
   }
   
   else print "";
   
  
     
}

function get_client_selection(){
    
      session_start();
    
   if(isset($_SESSION['ClientId']) && ($_SESSION['ClientId']) != '') print $_SESSION['ClientFname']." ".$_SESSION['ClientLname'];
   else print "";  
}




function get_products_transac(){
    
    $id = $_POST['trans'];
    
    $back="";
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $req = "SELECT product_or_service.label AS label,
                   product_or_service.preTaxPrice AS prix,
                   product_or_service.VAT AS vat,
                   Transaction_has_Product_or_Services.Product_or_Service_idProduct_Services AS id
                   FROM product_or_service,Transaction_has_Product_or_Services
                   WHERE Transaction_has_Product_or_Services.Product_or_Service_idProduct_Services=product_or_service.idProduct_Services
                   AND Transaction_has_Product_or_Services.Transaction_idTransaction=".$id." ORDER BY id ASC"; 
            
   $result =  $Query->dbExecuteQuery($req,array());
   
   for($i=0;$i<sizeof($result);$i++)
  {
     if($back == "")   $back .= '{"label":"'.$result[$i]['label'].'","prix":"'.$result[$i]['prix'].'","vat":"'.$result[$i]['vat'].'"}';
     else              $back .= ',{"label":"'.$result[$i]['label'].'","prix":"'.$result[$i]['prix'].'","vat":"'.$result[$i]['vat'].'"}';

  }
  
  
  if($back != "")   print "[".$back."]";
  else              print $back;
  
  
  
    
}




function get_remise_transac(){
    
    $id = $_POST['trans'];
    
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $req = "SELECT Reductions_idReductions AS red
                   FROM Transaction_has_Reductions
                   WHERE Transaction_idTransaction=".$id; 
            
   $result =  $Query->dbExecuteQuery($req,array());
   
   if(isset($result[0]['red']) && ($result[0]['red']!=''))  print $result[0]['red'];
   else                                                     print "";
  
  
  
    
}




function get_amount_transac()
{
    $id = $_POST['trans'];
    
    $back="";
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $req = "SELECT amount AS amount
                   FROM transaction
                   WHERE idTransaction=".$id; 
            
   $result =  $Query->dbExecuteQuery($req,array());
   
   if(isset($result[0]['amount']) && ($result[0]['amount']!=''))  print $result[0]['amount'];
   else                                                           print ""; 
    
}





function get_prestataire_transac()
{
    
    $id = $_POST['trans'];
    
    $back="";
    
    $Query = new SimplifiedDB();
    $Query->dbConnect();
    
    $req = "SELECT employee_idEmployee AS employee
                   FROM transaction
                   WHERE idTransaction=".$id; 
            
   $result =  $Query->dbExecuteQuery($req,array());
   
   if(isset($result[0]['employee']) && ($result[0]['employee']!=''))  print $result[0]['employee'];
   else                                                               print ""; 
}






function del_transac_remise_payment(){
    
    $id       = $_POST['trans'];
    $employee = $_POST['empl'];
 
    $Query = new SimplifiedDB();
    $Query->dbConnect();
 
    //delete payments
    $back = $Query->dbDelete("Transaction_has_Payments",array("Transactions_idTransaction1"=>$id)); 
    //Delete reduction
    $Query->dbDelete("Transaction_has_Reductions",array("Transaction_idTransaction"=>$id));
    //update emplyee
    $Query->dbUpdate("transaction",array("employee_idEmployee"=>$employee),array("idTransaction"=>$id));
    
   print $back; 
}



function update_transac_amount(){
    
    $id       = $_POST['trans'];
    $amount   = $_POST['mont'];
    
    $Query    = new SimplifiedDB();
    $Query->dbConnect();
    
     $Query->dbUpdate("transaction",array("amount"=>$amount),array("idTransaction"=>$id));
 
}



function get_payments_transac(){
    
  $id  = $_POST['trans'];  
  
  $Query = new SimplifiedDB();
  $Query->dbConnect(); 
  
  $table  = "Transaction_has_Payments";
  $select = array("idPayment","PaymentMean_idPaymentMean","Amount");
  $where  = array("Transactions_idTransaction1"=>$id);
  
  $result = $Query->dbSelect($table, $select, $where); 
  
  $back = "";
  
  for($i=0;$i<sizeof($result);$i++)
  {
     if($back == "") $back .= '{"id":"'.$result[$i]['PaymentMean_idPaymentMean'].'","mont":"'.$result[$i]['Amount'].'"}';
     else $back .= ',{"id":"'.$result[$i]['PaymentMean_idPaymentMean'].'","mont":"'.$result[$i]['Amount'].'"}';

  }
     
  print "[".$back."]";
}




function set_pay_transac(){
    
    $id       = $_POST['trans'];
    
    $Query    = new SimplifiedDB();
    $Query->dbConnect();
    
     $Query->dbUpdate("transaction",array("status"=>"Terminer"),array("idTransaction"=>$id));
 
}





function dateFR($datetime) {

$date = new DateTime($datetime);

//affichage de la date au format francophone:
return $date->format('d/m/Y H:i:s');
}




function unset_client(){
    
   session_start();
   unset($_SESSION['ClientId']); 
}






function PerProducts(){
  
  
  date_default_timezone_set('Europe/Berlin');
  
  $Total = 0;
  $id = $_SESSION['user_id']; 
  
  $yesterday = date("Y-m-d 23:59:59", strtotime('yesterday'));
  
  $Query = new SimplifiedDB();
  $Query->dbConnect();
  
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                                      $theus = $id; 
  
  
  $req = "SELECT product_or_service.idProduct_Services AS id,
                 product_or_service.label AS nom,
                 product_or_service.preTaxPrice AS prix,
                 transaction.amount AS mont,
                 transaction.created As cre
                 FROM transaction,product_or_service,Transaction_has_Product_or_Services
                 WHERE transaction.idTransaction=Transaction_has_Product_or_Services.Transaction_idTransaction
                 AND Transaction_has_Product_or_Services.Product_or_Service_idProduct_Services=product_or_service.idProduct_Services  
                 AND transaction.status='Terminer'
                 AND transaction.created>'".$yesterday."'
                 AND transaction.cash_desk_users_idUsers=".$theus; 
            
   $result =  $Query->dbExecuteQuery($req,array());
  

  
  
  $back = '<table class="striped"><thead><tr><th>R&eacute;f</th><th>Nom</th><th>Prix</th></tr></thead>
                      <tbody>';
                        for($i=0;$i<sizeof($result);$i++)
                       {
                            $idtransac = $result[$i]['id'];
                            $Total+=$result[$i]['prix'];
                            
                            $back .='<tr><td>'.$idtransac.'</td><td>'.$result[$i]['nom'].'</td><td><b>'.$result[$i]['prix'].'&euro;</b></td></tr>';
                       }
                       
                        $back .='<tr><td></td><td></td><td><b>Total : '.$Total.'&euro;</b></td></tr>';  

        $back .='</tbody>
                 </table>';

     print $back;
      
    
}



function PerPrestataires(){
  
  
  date_default_timezone_set('Europe/Berlin');
  $id = $_SESSION['user_id']; 
  
  $yesterday = date("Y-m-d 23:59:59", strtotime('yesterday'));
  
  $Query = new SimplifiedDB();
  $Query->dbConnect();
  
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                                      $theus = $id; 
  
  
   $req = "SELECT idEmployee,firstname,lastname
                 FROM employee
                 WHERE	isActive='1'
                 AND isDelete='0'
                 AND cash_desk_users_idUsers=".$theus; 
            
   $result =  $Query->dbExecuteQuery($req,array());
  

   $Total = 0;
  
  $back = '<table class="striped"><thead><tr><th>Prestataire</th><th>Total</th></tr></thead>
                      <tbody>';
                        for($i=0;$i<sizeof($result);$i++)
                       {
                            $mnt = 0;
                            
                            $req1 = "SELECT amount
                                     FROM transaction
                                    WHERE	employee_idEmployee='".$result[$i]['idEmployee']."'
                                    AND status='Terminer'
                                    AND created>'".$yesterday."'"; 
            
                             $result1 =  $Query->dbExecuteQuery($req1,array());
                             
                             for($k=0;$k<sizeof($result1);$k++)
                             {
                                $mnt = $mnt+$result1[$k]['amount'];
                             }
                            
                             $Total += $mnt;
                            
                            $back .='<tr><td>'.$result[$i]['firstname'].' '.$result[$i]['lastname'].'</td><td><b>'.$mnt.'&euro;</b></td></tr>';
                       }
                       
               $back .='<tr><td></td><td><b>Total : '.$Total.'&euro;</b></td></tr> 

            </tbody>
                 </table>';

     print $back;
      
    
}



function PerTransactions(){
  
  
  date_default_timezone_set('Europe/Berlin');
  
  $id = $_SESSION['user_id']; 
  
  $yesterday = date("Y-m-d 23:59:59", strtotime('yesterday'));
  
  $Query = new SimplifiedDB();
  $Query->dbConnect();
  
  
    $req0 = "SELECT property FROM users WHERE idUsers=".$id; 
    $res0 = $Query->dbExecuteQuery($req0,array());
    
     $property = $res0[0]['property'];
    
     if(($property !='0')&&($property !='6'))  $theus = $property;
     else                                      $theus = $id; 
  
  
   $req = "SELECT idEmployee,firstname,lastname
                 FROM employee
                 WHERE	isActive='1'
                 AND cash_desk_users_idUsers=".$theus; 
            
   $result =  $Query->dbExecuteQuery($req,array());
  

   $Total = 0;
  
  $back = '<table class="striped"><thead><tr><th>Id transaction</th><th>Total</th></tr></thead>
                      <tbody>';
                        for($i=0;$i<sizeof($result);$i++)
                       {
                            
                            $req = "SELECT idTransaction,amount
                                     FROM transaction
                                     WHERE cash_desk_users_idUsers=".$theus."
                                     AND status='Terminer'
                                     AND created>'".$yesterday."'"; 
            
                             $result =  $Query->dbExecuteQuery($req,array());
                             
                             for($i=0;$i<sizeof($result);$i++)
                             {
                                $Total+= $result[$i]['amount'];
                                
                                $back .='<tr><td>'.$result[$i]['idTransaction'].'</td><td><b>'.$result[$i]['amount'].'&euro;</b></td></tr>';
                             }
                            
                            
                            
                       }
                       
               $back .='<tr><td></td><td><b>Total : '.$Total.'&euro;</b></td></tr> 

            </tbody>
                 </table>';

     print $back;
      
    
}








?>