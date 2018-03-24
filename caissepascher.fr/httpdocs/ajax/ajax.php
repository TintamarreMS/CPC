<?php

include("./ajax-functions.php");

// Require an action parameter
if (empty( $_REQUEST['action'] ) )   die( '0' );


@header( 'Content-Type: text/html; charset=UTF-8');
@header( 'X-Robots-Tag: noindex' );




$core_actions_get = array(  'user_login',
                            'client_select',
                            'client_use',
                            'open_modal_client',
                            'open_modal_report',
                            'open_modal_pay',
                            'update_client',
                            'give_clients',
                            'give_reduc',
                            'save_transac',
                            'pay_transac',
                            'insert_transac_product',
                            'insert_transac_payment',
                            'open_modal_wait_transac',
                            'remove_transac',
                            'insert_transac_remise',
                            'unset_client',
                            'open_modal_delete_transac',
                            'cancel_trans',
                            'get_client_transac',
                            'get_products_transac',
                            'get_remise_transac',
                            'get_amount_transac',
                            'get_payments_transac',
                            'get_prestataire_transac',
                            'del_transac_remise_payment',
                            'update_transac_amount',
                            'set_pay_transac',
                            'get_client_selection');


$core_actions_post = array( 'user_login',
                            'client_select',
                            'client_use',
                            'open_modal_client',
                            'open_modal_report',
                            'open_modal_pay',
                            'update_client',
                            'give_clients',
                            'give_reduc',
                            'save_transac',
                            'pay_transac',
                            'insert_transac_product',
                            'insert_transac_payment',
                            'open_modal_wait_transac',
                            'remove_transac',
                            'insert_transac_remise',
                            'unset_client',
                            'open_modal_delete_transac',
                            'cancel_trans',
                            'get_client_transac',
                            'get_products_transac',
                            'get_remise_transac',
                            'get_amount_transac',
                            'get_payments_transac',
                            'get_prestataire_transac',
                            'del_transac_remise_payment',
                            'update_transac_amount',
                            'set_pay_transac',
                            'get_client_selection');



// Register core Ajax calls.
if ( ! empty( $_GET['action'] )  && in_array( $_GET['action'],  $core_actions_get ) )   $_GET['action']();
if ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], $core_actions_post ) )  $_POST['action']();





?>