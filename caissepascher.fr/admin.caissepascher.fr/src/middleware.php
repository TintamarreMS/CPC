<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

require_once __DIR__ . '/Middleware/ClientMiddleware.php';
require_once __DIR__ . '/Middleware/DashboardMiddleware.php';
require_once __DIR__ . '/Middleware/TransactionMiddleware.php';
require_once __DIR__ . '/Middleware/UserMiddleware.php';

/**
 * Vérification params nécessaires posté ou non
 */
function verifyRequiredParams($required_fields, $request_params = array()) {
    global $app;
    $error = false;
    $error_fields = "";

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
    if ($error) {
        //Champ (s) requis sont manquants ou vides
        // echo erreur JSON et d'arrêter l'application
        $response = array();
        $response["error"] = true;
        $response["message"] = 'Champ(s) requis ' . substr($error_fields, 0, -2) . ' est (sont) manquant(s) ou vide(s)';
        $response["status_code"] = 400;
        return $response;
    }
    return true;
}

/**
 * Validation adresse e-mail
 */
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = "Adresse e-mail n'est pas valide";
        $response["status_code"] = 401;
       return $response;
    }
    return true;
}

/**
 * Faisant écho à la réponse JSON au client
 * @param String $status_code  Code de réponse HTTP
 * @param Int $response response Json
 */
function echoRespnse($status_code, $data, $response) {
    // Code de réponse HTTP
    //$retour->withStatus($status_code);

    // la mise en réponse type de contenu en JSON
    $response->withHeader('Content-Type','application/json');

    return $response->withJson($data,$status_code);

}




function fileCsv ($json, $name, $keyArray = null) {
    $jsonArray = json_decode( $json, true );
    
    $tmpName = tempnam(sys_get_temp_dir(), $name);
    $file = fopen($tmpName, 'w');
    
    $lenghtArray = count($jsonArray);
   
    if($keyArray == null)
    {
        foreach($jsonArray[0] as $key => $val)
            fputcsv($file, $key , ";");
    } 
    else  fputcsv($file, $keyArray , ";");
    
    
    foreach($jsonArray as $key => $val)
    {
       fputcsv($file, $val , ";");  
    }

    
    fclose($file);
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename='.$name.'.csv');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tmpName));
    
    ob_clean();
    flush();
    readfile($tmpName);
    
    unlink($tmpName);

};






function fileText ($json, $name, $keyArray = null) {
    $jsonArray = json_decode( $json, true );
    $tmpName = tempnam(sys_get_temp_dir(), $name);
    $file = fopen($tmpName, 'w');
    $lenghtArray = count($jsonArray);
    if($keyArray == null){
        foreach($jsonArray[0] as $key => $val)
            fputcsv($file, $key , ";");
    }
    else
        fputcsv($file, $keyArray , ";");
    
    foreach($jsonArray as $key => $val)
        fputcsv($file, $val , ";");
    fclose($file);
    
    header('Content-Description: File Transfer');
    header('Content-Type: text/txt; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$name.'.txt');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tmpName));
    
    ob_clean();
    flush();
    readfile($tmpName);
    
    unlink($tmpName);
};





function filePdf ($json = "", $name = "data") {
    require_once __DIR__ . '/include/Pdf.php';
    $pdf = new PDF();
    // Titres des colonnes
    $header = array('Pays', 'Capitale', 'Superficie (km²)', 'Pop. (milliers)');
    // Chargement des données
    $data = $pdf->LoadData('pays.txt');
    $pdf->SetFont('Arial','',14);
    $pdf->AddPage();
    $pdf->BasicTable($header,$data);
    $pdf->AddPage();
    $pdf->ImprovedTable($header,$data);
    $pdf->AddPage();
    $pdf->FancyTable($header,$data);
    $pdf->Output();
};