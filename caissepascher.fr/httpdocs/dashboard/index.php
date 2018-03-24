<?php 
require_once("../common/header.php");
require_once("../ajax/ajax-functions.php");

Credentials(); 

$wait_trans = TransWaiting();

?>


    


 
<div id="main">
  <div class="wrapper">

    <aside id="left-sidebar-nav">
     <a href="#"  class="sidebar-collapse1 btn-floating btn-medium waves-effect waves-light transparent" title="Rapport des ventes" onclick="OpenModal(2);"><i class="material-icons">timeline</i></a>
     <a href="#"  class="sidebar-collapse2 btn-floating btn-medium waves-effect waves-light  <?php if($wait_trans!='0') echo 'red'; else echo 'transparent' ?>" title="Transactions en attente" <?php if($wait_trans!='0') echo 'onclick="OpenModal(4);"';?>><i class="material-icons">hourglass_full</i></a>
     <a href="#"  class="sidebar-collapse3 btn-floating btn-medium waves-effect waves-light transparent" title="Annuler une transaction" onclick="OpenModal(5);"><i class="material-icons">delete</i></a>
     <a href="#"  id="clnom" class="sidebar-collapse4 btn-floating btn-medium waves-effect waves-light <?php echo $color; ?>" title="<?=$title?>" onclick="OpenModal(1);"><i class="material-icons">perm_contact_calendar</i></a>
    </aside>

   <!-- START CONTENT -->
   <section id="content">
    <div class="container">
      <div>
           <div class="row">
                <div class="col s12 m8 l8">
                  <div  class="card">
                    <div class="card-content">
                      <h4 class="header mt-0"></h4>
                      <div class="row">
                        <div class="col s12">
                         
 
                            <?php 
                                   $result = GetProduct($_SESSION['user_id']); 
                                   
                                   for($i=0;$i<sizeof($result);$i++)
                                   {
                                      echo '<div class="col s12 m6 l3 waves-effect waves-light add-to-cart" 
                                                 data-id="'.$result[$i]["pdtid"].'" 
                                                 data-name="'.$result[$i]["pdtlab"].'" 
                                                 data-price="'.$result[$i]["pdtprice"].'" 
                                                 data-vat="'.$result[$i]["pdtvat"].'"
                                                 data-catid="'.$result[$i]["pdtcatid"].'"
                                                 data-catlab="'.$result[$i]["pdtcatlab"].'">
                                            <div class="card gradient-45deg-blue-indigo gradient-shadow min-height-100 white-text">
                                            <div class="padding-4">
                                               <div class="col s6 m6 left-align" style="width:100%;height:60px"><h6>'.$result[$i]["pdtlab"].'</h6>
                                               <h6 class="mb-p1">'.$result[$i]["pdtcatlab"].'</h6>
                                               </div>
                                               <div class="col s6 m6 center-align">
                                                 <h5 class="mb-p">'.$result[$i]["pdtprice"].'&euro;</h5> 
                                                </div>
                                               </div>
                                            </div>
                                          </div>';
                                   }
                            ?>
                            
                            
                          
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
               
                <div class="col s12 m12 l4">
                  <div class="card">
                    <div class="card-content" >
                      <h4 class="header m-0">Panier</h4>
                      <div class="cart-container" >
                     
                          <p id="cart-text" class="no-margin grey-text lighten-3 medium-small">panier vide</p>
	                          <div class="cd-cart">
                               
			                         <div class="body">
				                        <ul></ul>
                                     </div>
	                          </div>
                          <div class="center-align">
                             <button id="pay" class="waves-effect waves-light btn bluebutton  disabled checkout" onclick="OpenModal(3);"><em>Payer <span>0</span>&euro;</em></button>
                          </div> 
                        
                       </div> <!-- cd-cart-container -->
                      
                      
                      
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!--yearly & weekly revenue chart end-->
        
           </div> 

          
            <!--end container-->
        </section>
        <!-- END CONTENT -->
        
      
</div>
<!-- END WRAPPER -->
        
 </div>        
 
 
      <div id="modal-form" class="modal Client"></div>
      <div id="modal-form" class="modal Wait"></div>
      <div id="modal-form" class="modal Confirm"></div>
      <div id="modal-form" class="modal Delete"></div>
            
      <div class="modal Mreport">
              <div class="row">
                <div class="col s12 m12">
                  <div class="card-panel">
                    <h4 class="header2"><b>Rapport quotidien des ventes</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button id="send-right" class="waves-effect waves-light btn bluebutton send-right" title="Envoyer par mail" onclick="#"><i class="material-icons">mail</i></button></h4>
                    <!-- onclick="location.href='../invoicer/'" -->
                    <div class="row" >
                        <ul id="tabs-swipe-demo" class="tabs">
                           <li class="tab col s3"><a href="#product_report">Par produits</a></li>
                           <li class="tab col s3"><a class="active" href="#employee_report">Par prestataires</a></li>
                           <li class="tab col s3"><a href="#trans_report">Par transactions</a></li>
                        </ul>
                          <div id="product_report" class="col s12 white" style="height: 1500px;overflow-y: auto;">
                          <?php 
                          PerProducts();
                          ?>
                          <br /><br /><br /><br /><br />
                          </div>
                          <div id="employee_report" class="col s12 white" style="height: 1500px;overflow-y: auto;">
                          <?php 
                          PerPrestataires();
                          ?>
                          </div>
                          <div id="trans_report" class="col s12 white" style="height: 1500px;overflow-y: auto;">
                          <?php 
                          PerTransactions();
                          ?>
                          
                          </div>
                     </div>
                 </div>
                 </div>
              </div>
       </div>
       
       
       <div class="modal Cart">
              <div class="row">
                <div class="col s12 m12">
                  <div class="card-panel">
                  <h6 class="header2 left-align"><b>Client : </b>
                  <span class="cartclt">
                  <?php  if(isset($_SESSION['ClientId']) && ($_SESSION['ClientId']) != '') echo $_SESSION['ClientFname']."&nbsp;".$_SESSION['ClientLname'];
                         else echo "";
                  ?>
                  </span>
                  
                  </h6>
                    <h5 class="header2 center-align"><b>R&eacute;capitulatif</b></h5>
                    <input type="hidden" id="idTransaction" name="idTransaction" value=""/>
                    <div class="row">
                      <form class="FormCart s12" action="#" >
                      <div class="bodyPay" >
                        <ul>
                       
                        </ul>
                        <br />
                        <br />
                        <div class="totalht"> Total HT : <span class="htprice"></span><b>&euro;</b></div>
                        <div class="totalttc">Total TTC : <span class="ttcprice"></span><b>&euro;</b></div>
                        
                         
                        <div class="row s8 m10 select_ticket" >
                          <br />
                         <select id="list_remise" onchange="ChangeRemise(this.value);">
                              <option value="0" selected>R&eacute;duction</option>
                              <?php
                                $result = DisplayRemise();
                                for($i=0;$i<sizeof($result);$i++)
                                {
                                  echo '<option value="'.$result[$i]['idReductions'].'" >';
                                  
                                  if($result[$i]['isPercentage'] == '1')   echo $result[$i]['label'].'&nbsp;&nbsp; -'.$result[$i]['DiscountPercentage'].'%';
                                  else                                     echo $result[$i]['label'].'&nbsp;&nbsp; -'.$result[$i]['DiscountPercentage'].'&euro;';
                                  
                                  
                                  echo'</option>';  
                                }
                              ?>
        
                          </select>
                       </div>
                     
                      <div class="row s8 m10 m12" id="rowtotalttcf"  ><div class="totalttcf">R&eacute;duc. Total TTC : <span class="ttcpricef"></span><b>&euro;</b></div></div>   
                      
                      <div class="row s8 m10 select_ticket">
                      <select id="employee" name="employee">
                              <option value="" disabled selected>Prestataire</option>
                              <?php
                                $result = DisplayPrestatire();
                                for($i=0;$i<sizeof($result);$i++)
                                {
                                  echo '<option value="'.$result[$i]['idEmployee'].'" >'.$result[$i]['firstname'].' '.$result[$i]['lastname'].'</option>';  
                                }
                              ?>
        
                        </select>
                      </div>
                     
                      <div class="rowp">
                      <div class="input-field col s6">
                             <select id="payement_type" name="payement_type">
                                   <option value="" disabled selected>Moyen de paiement</option>
                                   <?php
                                          $result = Payement();
                                          for($i=0;$i<sizeof($result);$i++)
                                          {
                                            echo '<option value="'.$result[$i]['UserConfigs_has_PaymentMean_Id'].'" >'.utf8_decode($result[$i]['PaymentLabel']).'</option>';  
                                          }
                                    ?>
        
                              </select>
                        </div>

                        
                         <div class="input-field col s5" >
                            <label for="paypart1">Paiement partiel</label>
                            <input id="paypart1" name="paypart1" type="text" value="" class="prixpart"/><span class="europart1">&nbsp;&nbsp;&euro;</span>
           
                        </div> 
                        
                        
                
                          
                      </div>
                      
                      <div id="payement2" class="rowp hidden">
                      <div  class="input-field col s6" >
                             <select id="payement_type1" name="payement_type1">
                                   <option value="" disabled selected>Moyen de paiement 2</option>
                                   <?php
                                          $result = Payement();
                                          for($i=0;$i<sizeof($result);$i++)
                                          {
                                            echo '<option value="'.$result[$i]['UserConfigs_has_PaymentMean_Id'].'" >'.utf8_decode($result[$i]['PaymentLabel']).'</option>';  
                                          }
                                    ?>
        
                              </select>
                      </div>
                      <div class="input-field col s5" >
                            <input id="paypart2" name="paypart2" type="text" value="" class="prixpart2 active" disabled="true"/><span class="europart2" disabled="true">&nbsp;&nbsp;&euro;</span>
                        </div> 
                      </div>

                        
                      </div>
                  
                      <div class="input-field col s12">
                           <table >
                           <tr>
                           <td>
                            <a id="saveTransac" class="btn waves-effect waves-light left" onclick="SaveTrasaction();">Mettre en attente
                              <i class="material-icons right">hourglass_full</i>
                            </a>
                           </td>
                           <td>
                           <a id="printTransac" class="btn waves-effect waves-light center" onclick="Pay(1);">Payer.Imprimer
                              <i class="material-icons right">print</i><i class="material-icons right">payment</i>
                            </a>
                           </td>
                           <td>
                           <a id="payTransac" class="btn waves-effect waves-light right newclt" onclick="Pay(0);">Payer
                              <i class="material-icons right">payment</i>
                            </a>
                           </td>
                           </tr>
                          </table>
                     </div>
                       
                      
                  
                      </form>
                       
                    </div>
                  </div>
                </div>
              </div>
             
       </div>
       
     
      
</div>      
      <!-- END MAIN -->
      <!-- //////////////////////////////////////////////////////////////////////////// -->

  <!--  Scripts  ================================================ -->
      <!-- jQuery Library -->
       <script type="text/javascript" src="../vendors/jquery-3.2.1.min.js"></script> 
       <script type="text/javascript" src="../vendors/angular.min.js"></script>
      <!--materialize js-->
      <script type="text/javascript" src="../js/materialize.min.js"></script>
      <!--prism-->
      <script type="text/javascript" src="../vendors/prism/prism.js"></script>
      <!--scrollbar-->
      <script type="text/javascript" src="../vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
      <script type="text/javascript" src="../vendors/noUiSlider/nouislider.js"></script>
      <script type="text/javascript" src="../vendors/jquery-validation/jquery.validate.min.js"></script>
      <script type="text/javascript" src="../vendors/jquery-validation/additional-methods.min.js"></script>
      <!--plugins.js - Some Specific JS codes for Plugin Settings-->
      <script type="text/javascript" src="../js/plugins.js"></script>
      <script type="text/javascript" src="../js/scripts/advanced-ui-modals.js"></script>
      <!--custom-script.js - Add your own theme custom JS-->
      <script type="text/javascript" src="../js/custom-script.js"></script>
      <script type="text/javascript" src="../js/scripts/form-elements.js"></script>
      <script type="text/javascript"  src="../js/main.js"></script> <!-- Resource jQuery -->
      <script type="text/javascript"  src="../js/functions.js"></script>
       
   
  </body>
</html>