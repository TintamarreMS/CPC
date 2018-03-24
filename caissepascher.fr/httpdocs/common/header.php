<?php
  //set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
  
  session_start();
  
  if(isset($_SESSION['ClientId']) && ($_SESSION['ClientId']!=''))
   {
     $color = "red";
     $title = $_SESSION['ClientFname']."&nbsp;".$_SESSION['ClientLname'];
   }
   else
   {
     $color = "transparent";
     $title = "Fiche client";
   } 
  
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="msapplication-tap-highlight" content="no"/>
    <meta name="description" content="Caisse pas cher, responsive and based on Material Design by Google. "/>
    <title>Dashboard | Caisse pas cher</title>
    <link rel="icon" href="../images/favicon/favicon-32x32.ico" sizes="32x32" />
    <link href="../css/themes/overlay-menu/materialize.css" type="text/css" rel="stylesheet" />
    <link href="../css/themes/overlay-menu/style.css" type="text/css" rel="stylesheet" />
    <link href="../css/layouts/style-fullscreen.css" type="text/css" rel="stylesheet" />
    <link href="../css/custom/custom.css" type="text/css" rel="stylesheet" />
    <link href="../vendors/perfect-scrollbar/perfect-scrollbar.css" type="text/css" rel="stylesheet" />
</head>
  
<body>

<!-- Start Page Loading -->
<div id="loader-wrapper">
<!-- <div id="loader"></div> -->
<div class="loader-section section-left"></div>
<div class="loader-section section-right"></div>
</div>
<header id="header" class="page-topbar">
      <div class="navbar-fixed">
        <nav class="navbar-color">
          <div class="nav-wrapper">
            <ul class="right">
               <li><a href="http://admin.caissepascher.fr" target="_blank"   class="btn-floating btn-medium waves-effect waves-light transparent" title="Acc&egrave;s au back office"><i class="material-icons">settings</i></a></li>
                <li><a href="#" class="waves-effect waves-block waves-light" onclick="location.href='../logout'" title="D&eacute;connexion"><i class="material-icons">exit_to_app</i></a></li>
              </ul>
           
            
          </div>
        </nav>
      </div>
</header>
