<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="msapplication-tap-highlight" content="no"/>
    <meta name="description" content="Caisse pas cher, responsive and based on Material Design by Google. "/>
  
    <title>Login Page | Caisse pas cher</title>
  
    <!-- CORE CSS-->
    <link href="./materialize.css" type="text/css" rel="stylesheet">
    <link href="./style.css" type="text/css" rel="stylesheet">
    <!-- CSS for Overlay Menu (Layout Full Screen)-->
    <link href="../css/layouts/style-fullscreen.css" type="text/css" rel="stylesheet">
    <link href="../css/layouts/page-center.css" type="text/css" rel="stylesheet">
    <!-- Custome CSS-->
    <link href="../css/custom/custom.css" type="text/css" rel="stylesheet">
    
  </head>
  <body class="login">

    <div id="loader-wrapper">
      <div class="loader-section section-left"></div>
      <div class="loader-section section-right"></div>
    </div>
    <!-- End Page Loading -->
    <div id="login-page" class="row" >
      <div class="col s12 z-depth-4 login-panel" >
        <form id="login-form" class="login-form" method="post" >

            <div class="section" >
   
            
                <div class="col s12">
                  <ul id="tabs-swipe" class="tabs tablogin">
                    <li class="tab col s6"><a href="#caisse" class="active">Caisse</a></li>
                    <li class="tab col s6"><a href="#admin">Administrateur</a></li>
                  </ul>
                  <div id="caisse" class="col s12 carousel carousel-item gray-text">
                     
                     <br /><br />
                     
                      <div class="col s12 mt-1"></div>
                      <div class="row margin">
                      <div class="input-field col s12">
                        <i class="material-icons prefix pt-5">person_outline</i>
                         
                        <input id="username" type="text"/>
                        <label for="username">Identifiant * <span id="login-error" class="login-error-message"></span></label>
                      </div>
                      </div>
                      <div class="row margin">
                       <div class="input-field col s12">
                         <i class="material-icons prefix pt-5">lock_outline</i>
                         
                          <input id="pass" type="password"/>
                          <label for="pass">Mot de passe * <span id="pass-error" class="login-error-message"></span></label>
                          
                          
                          
                      </div>
                      </div> 
                      <div class="row">
                      <br /><br />
                         <div class="input-field col s12">
                          <input type="submit" class="btn waves-effect waves-light col s12" value="Connexion" />
                         </div>
                      </div>
                 </div>
                 
                  

                </div>
                
                <div id="admin" class="col s12 carousel carousel-item gray-text">
                <br /><br /><br />
                  <div class="col s12 mt-1"></div>
                  <div class="row margin">
                      <div class="input-field col s12"></div>
                  </div>
                 <div class="row margin">
                 <div class="input-field col s12"></div>
                </div>
               
               <div class="row"><br /><br /><br /><br />
                 <div class="input-field col s12">
                   <a href="http://admin.caissepascher.fr" target="_blank" class="btn waves-effect waves-light col s12">Acc&egrave;s au back office</a>
                 </div>
                </div>
             </div>
          
         </div>

          
        </form>
      </div>
    </div>
    <!-- ================================================
    Scripts
    ================================================ -->
    <!-- jQuery Library -->
    <script type="text/javascript" src="../vendors/jquery-3.2.1.min.js"></script>
    <!--materialize js-->
    <script type="text/javascript" src="../js/materialize.min.js"></script>
  
    <!--plugins.js - Some Specific JS codes for Plugin Settings-->
    <script type="text/javascript" src="../js/plugins.js"></script>
    <!--ui-navbar.js - Some Specific JS codes for Plugin Settings-->

    <!--custom-script.js - Add your own theme custom JS-->
    <script type="text/javascript" src="../js/custom-script.js"></script>
    <script type="text/javascript" src="../js/login.js"></script>
  </body>
</html>