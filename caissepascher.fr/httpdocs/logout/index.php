<?php


session_start();
session_unset();
session_destroy();

include("../config/config.php");

echo "\n<SCRIPT language = \"javascript\">window.location='../';</SCRIPT>\n";


?>