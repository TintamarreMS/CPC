


function OpenModal(id){
      
      switch(id){
        case 1:
                         act = 'open_modal_client';
                         jQuery.post('../ajax/ajax.php', {
                          action: act,
		                     }).done(function(response) 
                             {
                                 $('.Client').html(response); 
                                 $('.Client').modal('open'); 
                                                   
		                     });
        break;
        
        case 2:
                         act = 'open_modal_report';
                         
                         jQuery.post('../ajax/ajax.php', {
                          action: act,
		                     }).done(function(response) 
                             {
                                 
                                                   
		                     });
                             
                             $('.Mreport').modal('open'); 
        break;
        
        
        
        case 3:
                                   //Reset 
                                   
                                    jQuery.post('../ajax/ajax.php', {
                                    action: "get_client_selection",
		                            }).done(function(response) 
                                   {
                                       $('.cartclt').text(response);
                                                   
		                          });
                                   
                                    
                                   
                                   $('#list_remise').val(0);
                                   $('#list_remise').material_select(); 
                                   
                                   $('#employee').val('');
                                   $('#employee').material_select(); 
                                   
                                   $('#payement_type').val('');
                                   $('#payement_type').material_select(); 
                                   
                                   $('#paypart1').val('');
                                   
                                   $('#payement_type1').val('');
                                   $('#payement_type1').material_select(); 
                                   
                                   $('#paypart2').val('');
                                   
                                   
                                   
                                   
                                   
                                   $('.Cart').modal('open'); 
                                   
                                   var Paylist =  $('.bodyPay').find('ul').eq(0);
                                   
                                   var totalht  = 0;
                                   var totalttc = 0;
                                   
                                   Paylist.empty();
                                   
                                   $('.cart-container').find('.body').find('ul').eq(0).find('li').each(function(){
                                   
                                    var Pid = $(this).find('.name').attr('id');
                                    var Pname = $(this).find('.name').html();
                                    var Pprice = $(this).find('.price').html();
                                    var Pvat = $(this).find('.price').attr('vat');
                                    
                                    totalttc+=Number(Pprice);
                                    
                                    totalht+= Number(Pprice)-Number((Number(Pprice)*Number(Pvat))/100);

                                    
                                    Paylist.append('<li class="productcart"><img src="../images/icon/product-preview.png"></span><span class="namecart" id="'+Pid+'">'+Pname+'</span><span class="pricecart" vat="'+Pvat+'">'+Pprice+'</span><span class="eurocart">&euro;</span></li>');
                               
                               });
                               
                               
                               $('.htprice').html(totalht.toFixed(2));
                               $('.ttcprice').html(totalttc.toFixed(2));
                               
        break;
        
        
        case 4:
                         act = 'open_modal_wait_transac';

                         jQuery.post('../ajax/ajax.php', {
                          action: act,
		                     }).done(function(response) 
                             {
                                 $('.Wait').html(response); 
                                 $('.Wait').modal('open'); 
                                                   
		                     });
        break;
        
        case 5:
                   act = 'open_modal_delete_transac';
                   
                   jQuery.post('../ajax/ajax.php', {
                          action: act,
		                     }).done(function(response) 
                             {
                                 $('.Delete').html(response); 
                                 $('.Delete').modal('open'); 
                                                   
		                     });
        
        
        break;
        
        
        
        
      }
     
 
}




function ChangeRemise(val){
  
  if(val!='0')
  {
  
       jQuery.post('../ajax/ajax.php', {
                            action: "give_reduc",
                            id:val
		                     }).done(function(response) 
                             {
                                 var obj = {};
                                   
                                    obj = JSON.parse(response);
                                    var isperc = obj.ispercent;
                                    var perc = obj.percent;
                                    
                                    switch(isperc){
                                        case '1':
                                                  var ttc = Number($('.ttcprice').text());
                                                  var red = ((ttc*perc)/100);
                                                  
                                                  var ttcN = (ttc - red); 
                                                  $('.ttcpricef').html(ttcN.toFixed(2));
                                                  
                                                  if($('#paypart1').val()!='')
                                                    {
                                                        var pp1 = Number($('#paypart1').val());
            
                                                           var pp2 = Number(ttcN.toFixed(2))-pp1;
                                                           if(pp2< (ttcN.toFixed(2)))      $('#paypart2').val(pp2);
                                                    }
                                        break;
                                        
                                        case '0':
                                                  
                                                 var ttc = Number($('.ttcprice').text());
                                                 
                                                 var ttcN = (ttc -perc); 
                                                  
                                                  $('.ttcpricef').html(ttcN.toFixed(2));
                                                  
       
                                                   if($('#paypart1').val()!='')
                                                    {
                                                        var pp1 = Number($('#paypart1').val());
            
                                                           var pp2 = Number(ttcN.toFixed(2))-pp1;
                                                           if(pp2< (ttcN.toFixed(2)))      $('#paypart2').val(pp2.toFixed(2));
            
                                                    }
                                        break;
                                    }
                                    
                                    
                                   
                                    $('#rowtotalttcf').css('display','block');
                                    
                                                   
		                     });
                             
         
                             
  }
  
  else{
         
         
         $('.ttcpricef').html('');
         $('#rowtotalttcf').css('display','none');
         
         var ttc = Number($('.ttcprice').text());
         if($('#paypart1').val()!='')
         {
            var pp2 = ttc - Number($('#paypart1').val());
            
            if(pp2<ttc)  $('#paypart2').val(pp2);
         }
       
      }

}








function NewClient(){
    
    
    $(".FormClient").validate({

    rules: {

        nomClient: {
               required: true,
            },
        prenomClient: {
               required: true,
            },
            
        emailClient: {
               email:true, 
            },
            
        cpClient: {
                     maxlength: 5,
            },
        
      },
    
    messages: {
        nomClient: "Entrer le nom",
        prenomClient: "Entrer le  pr&eacute;nom",
        cpClient:"5 chiffres au maximum",
    },
 
    submitHandler: function(form, event)
    { 
       event.preventDefault();
       var txt = "";
       
       var idClt     = $("#idClient").val();
       var nomClt    = $("#nomClient").val();
       var prenomClt = $("#prenomClient").val();
       var telClt    = $("#telClient").val();
       var cpClt    = $("#cpClient").val();
       var emailClt  = $("#emailClient").val();
       
       $("#saveClient").prop('disabled', true);
    
      if((idClt == '') || (idClt == undefined))
           txt = "La fiche client est cr&eacute;&eacute;e";
      else txt = "La fiche client est mise &agrave; jour"; 
        jQuery.post('../ajax/ajax.php', {
                   action: 'update_client',
                   id:idClt,
                   fname:nomClt,
                   lname:prenomClt,
                   tel:telClt,
                   cp:cpClt,
                   email:emailClt
		              }).done(function(response) 
                      {
                           $("#saveClient").prop('disabled', false);
                           Materialize.toast(txt, 4000); 
                           $('.modal').modal('close'); 
                          
		             });

    },
    

    errorElement : 'div',
    errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    }
 });
}






function DeleteTrans(){

    $(".FormCancelTrans").validate({

    rules: {
        id_transac: {
                required: true,
             },
      },
    messages: {
        id_transac:{
            required: "Entrer un num&eacute;ro de transaction",
        },
     
    },
 
    
    submitHandler: function(form, event)
    { 
       
       
       event.preventDefault();
       
       $("#deltrans").prop('disabled', true);
       
       var transact = $("#id_transac").val();
    
        jQuery.post('../ajax/ajax.php', {
                   action: 'cancel_trans',
                   id_tr:transact
		              }).done(function(response) 
                      {
                           $("#deltrans").prop('disabled', false);
                          
                           Materialize.toast(response, 4000); 
                           
                          // $('.modal').modal('close'); 
                          
		             });

    },
    
    errorElement : 'div',
    errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    }
 });

}





function AutoCompleteUser(){  
    
     var dataClients = {};
     
     var id_clt = '';
    
    jQuery.post('../ajax/ajax.php', {
                   action: 'give_clients'
		              }).done(function(response) 
                      { 
                           var ClientsArray = {};
                             ClientsArray = JSON.parse(response);
                            
                            for (var item in ClientsArray) 
                            { 
                              dataClients[ClientsArray[item].firstname+" "+ClientsArray[item].lastname+" "+ClientsArray[item].phonenumber] = ClientsArray[item].id;
                            }
                            
                            
                            $('input.autocomplete').autocomplete({
                            data: dataClients,
                            limit: Infinity, // The max amount of results that can be shown at once. Default: Infinity.
                             onAutocomplete: function(data) {
                               
                               for (var item in dataClients) 
                               {
                                   if(data.trim() === item.trim())
                                   {
                                      id_clt = dataClients[item];
                                   }
                                    
                               }
                               
                               GetClient(id_clt);
                               
                               $('#unuseClient').css('visibility','hidden');
                               $('#unuseClient').slideUp();
                               
                  
                               },
                              minLength: 2, // The minimum length of the input for the autocomplete to start. Default: 1.
                            });
                            
                           
                           
                            
                            
		             });
            
}




function GetClient(id)
{
    
    jQuery.post('../ajax/ajax.php', {
                   action: 'client_select',
			       us: id
		              }).done(function(response) 
                      {
                                   
                            try{
                                   var obj = {};
                                   
                                   obj = JSON.parse(response);
                                   
                                   var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea';
                                   $(document).ready(function() {
                                   $(input_selector).each(function(index, element) {
                                       if($(element).val().length > 0) {
                                         $(this).siblings('label, i').addClass('active');
                                           }
                                       });
                                   });
                                   
                
                                   $('#useClient').css("visibility","visible");
                                   $('#useClient').removeClass("disabled");
                                   
                                   $('#idClient').val(id);
                                   $('#nomClient').val(obj.fname);
                                   $('#prenomClient').val(obj.lname);
                                   $('#telClient').val(obj.tel);
                                   $('#cpClient').val(obj.cp);
                                   $('#emailClient').val(obj.email);
                                   $('#autocomplete-input').val('');
                                   
                               }  
                             catch(e){}
                 

		            });
}




function UseThisClient(){
    
  if($('#idClient').value!='')
  {
     var id = $('#idClient').val();
     
     jQuery.post('../ajax/ajax.php', {
                   action: 'client_use',
			       us: id
		              }).done(function(response) 
                      {
                            try{
                                   var obj = {};
                                   
                                    obj = JSON.parse(response);
                                    $('#nomClient').val(obj.fname);
                                    $('#prenomClient').val(obj.lname);
                                    
                                    $('#clnom').removeClass('transparent').addClass('red');
                                    $('#clnom').attr('title',obj.fname+' '+obj.lname);
                                    
                                     Materialize.toast("Client&nbsp;:&nbsp;"+obj.fname+"&nbsp;"+obj.lname+"&nbsp;s&eacute;lectionn&eacute;", 4000); 
                                     
                                     $('.cartclt').text(obj.fname+" "+obj.lname);
                                     
                                     $('.modal').modal('close'); 
  
                               }  
                             catch(e){}
                 

		            });
  }   
 
}



function UnuseThisClient(){
    
     
     jQuery.post('../ajax/ajax.php', {
                   action: 'unset_client',
		              }).done(function(response) 
                      {
                            try{
                                    $('#idClient').val('');
                                    $('#nomClient').val('');
                                    $('#prenomClient').val('');
                                    $('#telClient').val('');
                                    $('#cpClient').val('');
                                    $('#emailClient').val('');
                                    
                                    $('#clnom').removeClass('red').addClass('transparent');
                                    $('#clnom').attr('title','Ficher client');
                                    
                                    $('#unuseClient').css('visibility','hidden');
                                    
                                    var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea';
                                   $(document).ready(function() {
                                   $(input_selector).each(function(index, element) {
                                       if($(element).val().length > 0) {
                                         $(this).siblings('label, i').removeClass('active');
                                           }
                                       });
                                   });
                                    
                                    
                                     $('.cartclt').text('');
                                     Materialize.toast("Le client est d&eacute;sactiv&eacute;", 4000); 
  
                               }  
                             catch(e){}
                             
                             
                             $('.Client').modal('close');
                 

		            });
  }   
 





function SaveTrasaction(){
    
     var error = 0;
     
     if($('#list_remise').val() != 0)   var amount   = $('.ttcpricef').text();
     else                               var amount   = $('.ttcprice').text();
    
     
  
     if( $('#employee').val() == null ){
       Materialize.toast('Renseigner le prestataire.', 4000); 
       error = 1; 
     }
       
      if($('#paypart1').val()!='')//parial payment
       {
           if( $('#payement_type').val() == null )
           {
              Materialize.toast('Renseigner le moyen paiement.', 4000);  
              error = 1;
           }
           if(!$.isNumeric($('#paypart1').val()))
           {
             Materialize.toast('Format non valide, paiement partiel.', 4000);  
             error = 1;
           }
           
           if(Number($('#paypart1').val())!= Number(amount))
           {
            
              if( $('#payement_type1').val() == null )
              {
                   Materialize.toast('Renseigner le moyen paiement 2.', 4000);  
                   error = 1;
              }
           }
           
           
        
       } 
       
       
       
      if(error==0)
      {
          
          
          
          $("#saveTransac").prop('disabled', true);
          
          var transId;
          
          var employee = $('#employee').val();
          
          
          
            
             if($("#idTransaction").val()=='')
             {
              //Traitements
              jQuery.post('../ajax/ajax.php', {
                   action: 'save_transac',
                   prestataire:employee,
                   mont:amount
		              }).done(function(response) 
                      {
                          transId = response;
                          
                          
                          $('.bodyPay').find('ul').eq(0).find('li').each(function(){
                                   
                          var Pid = $(this).find('.namecart').attr('id');
                            
                            jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_product',
                            prod:Pid,
                            trans:transId
		                      }).done(function(response) 
                              {
		                       });
             
                      });
                     
                      
                      if($('#list_remise').val() != 0)
                      {
                          var remise = $('#list_remise').val();
                          
                            
                            jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_remise',
                            reduc:remise,
                            trans:transId
		                      }).done(function(response) 
                              {
		                       });
                      }
                      
                      
                      if( $('#payement_type').val() != null )
                      {
                          var ptypeOne = $('#payement_type').val() ;
                          
                          if($('#paypart1').val() == "")    var payOne = amount;
                          else                              var payOne = $('#paypart1').val();
                          
                          jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_payment',
                            mont:payOne,
                            trans:transId,
                            paymenttype:ptypeOne
		                      }).done(function(response) 
                              {
		                       });
                          
                      }
                      
                      if( $('#paypart2').val() != '' )
                      {
                          var ptypeTwo = $('#payement_type1').val() ;
                          var payTwo   = $('#paypart2').val();
                          
                            jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_payment',
                            mont:payTwo,
                            trans:transId,
                            paymenttype:ptypeTwo
		                      }).done(function(response) 
                              {
		                       });
                      }
    
                      Materialize.toast('Panier enregistr&eacute; sous le num&eacute;ro : '+transId,2000);  
                 });
   
            }
            
            else{
                
                  transId = $("#idTransaction").val();
                  
  
                  //Del reduction / payments
                  jQuery.post('../ajax/ajax.php', {
                  action: 'del_transac_remise_payment',
                  trans:transId,
                  empl:employee
                  }).done(function(response) 
                  {
                      if(response == "")
                      {
                                  if( $('#payement_type').val() != null )
                                   {
                          
                                       var ptypeOne = $('#payement_type').val() ;
                          
                                       if($('#paypart1').val() == "")    var payOne = amount;
                                       else                              var payOne = $('#paypart1').val();
                          
                                       jQuery.post('../ajax/ajax.php', {
                                                 action: 'insert_transac_payment',
                                                 mont:payOne,
                                                 trans:transId,
                                                 paymenttype:ptypeOne
		                                       }).done(function(response) {});
                          
                                     }
                      
                                    if( $('#paypart2').val() != '' )
                                    {
    
                                        var ptypeTwo = $('#payement_type1').val() ;
                                        var payTwo   = $('#paypart2').val();
                          
                                        jQuery.post('../ajax/ajax.php', {
                                        action: 'insert_transac_payment',
                                        mont:payTwo,
                                        trans:transId,
                                        paymenttype:ptypeTwo
		                                }).done(function(response) {});
                                   }
                      }
                  });
                  
                  
                  
                    jQuery.post('../ajax/ajax.php', {
                            action: 'update_transac_amount',
                            mont:amount,
                            trans:transId
		                      }).done(function(response) 
                              {
                                 
		                       });
                    
                    
                    
                    if($('#list_remise').val() != 0)
                      {
                          var remise = $('#list_remise').val();
                          
                            console.log("Remise:",remise);
                            
                            jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_remise',
                            reduc:remise,
                            trans:transId
		                      }).done(function(response) 
                              {
                             
		                       });
                      }
                      
                      
                  
    
                      
                      Materialize.toast('Transaction N&deg;'+transId+' mise &agrave; jour.', 2000);
    
            }
                      
          
          UnsetUser();
          
          $("#saveTransac").prop('disabled', false);

          setInterval(function(){ Reload(); }, 2000);
          
   
      } 
  

}





/****

Pay transaction

**/

function Pay(print){
    
   
   if( $('#idTransaction').val() != '')
   {
             
             var error = 0;
             
             if($('#list_remise').val() != 0)   var amount   = $('.ttcpricef').text();
              else                              var amount   = $('.ttcprice').text();
             
             
             
  
           if( $('#employee').val() == null ){
             Materialize.toast('Renseigner le prestataire.', 4000); 
             error = 1; 
           }
       
          if($('#paypart1').val()!='')//parial payment
         {
           if( $('#payement_type').val() == null )
           {
              Materialize.toast('Renseigner le moyen paiement.', 4000);  
              error = 1;
           }
           if(!$.isNumeric($('#paypart1').val()))
           {
             Materialize.toast('Format non valide, paiement partiel.', 4000);  
             error = 1;
           }
           
           if(Number($('#paypart1').val())!= Number(amount))
           {
              
              if( $('#payement_type1').val() == null )
              {
                   Materialize.toast('Renseigner le moyen paiement 2.', 4000);  
                   error = 1;
              }
           }
           
        }
        
        if(error == 0) 
        {
               
                  transId = $("#idTransaction").val();
                  
                 
                  
                  if(print==0) $("#payTransac").prop('disabled', true);
                  else         $("#printTransac").prop('disabled', true); 
                  
                  
                  if($('#list_remise').val() != 0)   var amount   = $('.ttcpricef').text();
                  else                               var amount   = $('.ttcprice').text();
                  
                  var employee        = $('#employee').val();
                  
  
                  //Del reduction / payments
                  jQuery.post('../ajax/ajax.php', {
                  action: 'del_transac_remise_payment',
                  trans:transId,
                  empl:employee
                  }).done(function(response) 
                  {
                     
                       if(response == "")
                       {
                        
                           if( $('#payement_type').val() != null )
                           {
                               var ptypeOne = $('#payement_type').val() ;
                          
                               if($('#paypart1').val() == "")    var payOne = amount;
                               else                              var payOne = $('#paypart1').val();
                          
                               jQuery.post('../ajax/ajax.php', {
                                   action: 'insert_transac_payment',
                                   mont:payOne,
                                   trans:transId,
                                   paymenttype:ptypeOne
		                          }).done(function(response) {});
                          
                            }
                      
                           if( $('#paypart2').val() != '' )
                           {
                                var ptypeTwo = $('#payement_type1').val() ;
                                var payTwo   = $('#paypart2').val();
                          
                                jQuery.post('../ajax/ajax.php', {
                                   action: 'insert_transac_payment',
                                   mont:payTwo,
                                   trans:transId,
                                    paymenttype:ptypeTwo
		                           }).done(function(response) {});
                           }
                       }
                    
                  });
                  
                  
                  
                    jQuery.post('../ajax/ajax.php', {
                            action: 'update_transac_amount',
                            mont:amount,
                            trans:transId
		                      }).done(function(response) 
                              {
                             
		                       });
                    
                    
                    
                    if($('#list_remise').val() != 0)
                      {
                          var remise = $('#list_remise').val();
                          
                            console.log("Remise:",remise);
                            
                            jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_remise',
                            reduc:remise,
                            trans:transId
		                      }).done(function(response) 
                              {
                             
		                       });
                      }
                      
                      
                      jQuery.post('../ajax/ajax.php', {
                            action: 'set_pay_transac',
                            trans:transId,
		                      }).done(function(response) 
                              {
		                       }); 
                      
                      
                      Materialize.toast('Transaction N&deg;'+transId+' regl&eacute;e.', 5000); 
                      
                      UnsetUser();
                      $("#payTransac").prop('disabled', false);
                      $("#printTransac").prop('disabled', false); 
                      setInterval(function(){ Reload(); }, 2000);
                      
                      
                      
                         
            
        }
    
   }//End Edit
   else //Pay Create
   {
        var error = 0;
          
        if($('#list_remise').val() != 0)   var amount   = $('.ttcpricef').text();
        else                               var amount   = $('.ttcprice').text();
       
       if( $('#employee').val() == null )
       {
          Materialize.toast('Renseigner le prestataire.', 4000);  
          error = 1;
       }
       if( $('#payement_type').val() == null )
       {
          Materialize.toast('Renseigner le moyen paiement.', 4000);  
          error = 1;
       }
       if($('#paypart1').val()!='')//parial payment
       {
           if(!$.isNumeric($('#paypart1').val()))
           {
             Materialize.toast('Format non valide, paiement partiel.', 4000);  
             error = 1;
           }
           
           if(Number($('#paypart1').val())!= Number(amount))
           {
              
              if( $('#payement_type1').val() == null )
              {
                   Materialize.toast('Renseigner le moyen paiement 2.', 4000);  
                   error = 1;
              }
           }
           
          
       }
       if(error == 0)
       {
          
          if(print==0) $("#payTransac").prop('disabled', true);
          else         $("#printTransac").prop('disabled', true);
          
          var employee        = $('#employee').val();
   
          var payement_type   = $('#payement_type').val();
          var payement_type1  = $('#payement_type1').val();
          
          if($('#list_remise').val() != 0)   var amount   = $('.ttcpricef').text();
          else                               var amount   = $('.ttcprice').text();
          
          if($('#paypart1').val()!='')
          {
             var amount1 = $('#paypart1').val();
             if($('#paypart2').val()!='')         var amount2 = $('#paypart2').val();
             else                                 var amount2 ='';
          }

          
           jQuery.post('../ajax/ajax.php', {
                   action: 'pay_transac',
                   prestataire:employee,
                   mont:amount,
		              }).done(function(response) 
                       {
           
                           var transId = response;
    
                           $('.cart-container').find('.body').find('ul').eq(0).find('li').each(function(){
                                   
                            var Pid = $(this).find('.name').attr('id');
                            
                          
                            jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_product',
                            prod:Pid,
                            trans:transId
		                      }).done(function(response) {});
                          });
                          
                          
                          if($('#list_remise').val() != 0)
                         {
                             var remise = $('#list_remise').val();
                          
                            jQuery.post('../ajax/ajax.php', {
                            action: 'insert_transac_remise',
                            reduc:remise,
                            trans:transId
		                      }).done(function(response) {});
                          }
                          
                          
                          if($('#paypart1').val()!='')
                          {
                              AddPayment(transId,amount1,payement_type);
                              AddPayment(transId,amount2,payement_type1);
                          }
                          else AddPayment(transId,amount,payement_type);
                      
                                             
                          Materialize.toast('Transaction regl&eacute;e, num&eacute;ro '+transId, 5000);    
                  });
                      
              
                  $("#payTransac").prop('disabled', false);
                  $("#printTransac").prop('disabled', false); 
                  setInterval(function(){ Reload(); }, 2000);
          
      
      
       }
          
   }
   

}






















function AddPayment(trans,amount,payid){
    
   
   jQuery.post('../ajax/ajax.php', {
                action: 'insert_transac_payment',
                mont:amount,
                trans:trans,
                paymenttype:payid
                }).done(function(response) 
                {
                });
    
}



function ConfirmDeleteTransac(id){
     
    var id_t = id;
    var $toastContent = $('<span>Suppression de la transaction N&deg;'+id_t+'?</span>').add($('<div>&nbsp;&nbsp;<button class="btn dark-blue" Onclick="RemoveToast()">Non</button>&nbsp;<button class="btn dark-blue" Onclick="DeleteTransaction('+id_t+')">Oui</button></div>'));
   
    Materialize.toast($toastContent, 30000,'rounded');
        
    
}



function RemoveToast(){
    
 Materialize.Toast.removeAll();
    
}






function DeleteTransaction(id){
    
  jQuery.post('../ajax/ajax.php', {
                action: 'remove_transac',
                trans:id,

                }).done(function(response) 
                {
                    $('.Wait').html(response); 
                     Materialize.Toast.removeAll();
                });
    
}





function UnsetUser(){
    
    jQuery.post('../ajax/ajax.php', {
         action: 'unset_client',
         }).done(function(response) 
         { 
         });
         
         
  
  $('#clnom').removeClass('red').addClass('transparent');
  
  $('.cart-container').find('.body').find('ul').eq(0).empty();
  $('.cart-container').find('.checkout').find('span').text(0);
  $('#cart-text').html('panier vide');
  $('#pay').addClass('disabled'); 
  
  
  
}




function EditTransac(id){
    
   $('.Wait').html(''); 
   $('.cartclt').text('')
 
   $('#idTransaction').val(id);
   
   jQuery.post('../ajax/ajax.php', {
       action: 'get_client_transac',
         trans:id
          }).done(function(response) 
          { 
            if(response !='')
            {
              // rez = JSON.parse(response);
              $('.cartclt').text(response); 
            }
            else $('.cartclt').text(''); 
          });
          
          
     jQuery.post('../ajax/ajax.php', {
       action: 'get_products_transac',
         trans:id
          }).done(function(response) 
          {  
               var str = "";
               var ProductsArray = {};
                  ProductsArray = JSON.parse(response);
                  
                  $('.Cart').modal('open'); 
                                   
                  var Paylist =  $('.bodyPay').find('ul').eq(0);
                                   
                  var totalht  = 0;
                  var totalttc = 0;
                                   
                  Paylist.empty();
                  
                  for (var item in ProductsArray) 
                  {      
                         var pid = ProductsArray[item].id;
                         var plabel = ProductsArray[item].label;
                         var pprix = ProductsArray[item].prix;
                         var pvat = ProductsArray[item].vat;
                         
                         totalht+= Number(pprix)-Number((pprix*Number(pvat))/100); 
                         totalttc+=Number(pprix);  
                         Paylist.append('<li class="productcart"><img src="../images/icon/product-preview.png"></span><span class="namecart" id="'+pid+'">'+plabel+'</span><span class="pricecart" vat="'+pvat+'">'+pprix+'</span><span class="eurocart">&euro;</span></li>');               
                 }
                 
                 $('.htprice').text(totalht.toFixed(2)); 
                 $('.ttcprice').text(totalttc.toFixed(2));
                   
          });
          
          
          jQuery.post('../ajax/ajax.php', {
           action: 'get_remise_transac',
           trans:id
               }).done(function(response) 
                { 
                    if(response !='')
                    {
                         $('#list_remise').val(response);
                         $('#list_remise').material_select(); 
                         
                         jQuery.post('../ajax/ajax.php', {
                         action: 'get_amount_transac',
                         trans:id
                        }).done(function(response) 
                        { 
                          if(response !='')
                          {
                             $('#rowtotalttcf').css('display','block');
                             $('.ttcpricef').text(response); 
                          } 
                        });
                    }
                
              });
              
              
              //Prestataire
              jQuery.post('../ajax/ajax.php', {
              action: 'get_prestataire_transac',
              trans:id
              }).done(function(response) 
                { 
                 if(response !='')
                 {       
                         $('#employee').val(response);
                         $('#employee').material_select(); 
                   }
            });
            
          
             //Payments
              jQuery.post('../ajax/ajax.php', {
              action: 'get_payments_transac',
              trans:id
              }).done(function(response) 
                { 
                    var TransArray = {};
                    TransArray = JSON.parse(response);
                            
                     if(TransArray[0].id != "")
                     {
                         $('#payement_type').val(TransArray[0].id);
                         $('#payement_type').material_select(); 
                         
                         $('#paypart1').val(TransArray[0].mont);
                         
                         $('#paypart1').focus();
                     }
                     
                     if(TransArray[1].id != "")
                     {
                         $('#payement_type1').val(TransArray[1].id);
                         $('#payement_type1').material_select(); 
                         
                         $('#paypart2').val(TransArray[1].mont);
                         
                         $('#payement2').removeClass('hidden');
                     }
                    
                
            });
          
          
          /*
          
         
          $('#paypart2').val('');
          
          $('#payement2').addClass('hidden');
          
          */
          
          
          
   

      $('.Cart').modal('open'); 

}






function UserSelected(){
    
    var input_selector = 'input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], textarea';
                                   $(document).ready(function() {
                                   $(input_selector).each(function(index, element) {
                                       if($(element).val().length > 0) {
                                         $(this).siblings('label, i').addClass('active');
                                           }
                                       });
                                   });
}






jQuery(document).ready(function($){
    
    var pp2;
    
    $('#paypart1').keypress(function(event){
        
    if(!isNumber(event, this)) event.preventDefault();
    
  });  
  
  
      

  
  
  $('#paypart1').keyup(function(){
      
     
     
      if($(this).val().length==0) 
      {
         $('#payement2').addClass('hidden');
         
      } 
      else 
      {
        
         $('#payement2').removeClass('hidden');
         
        
         
         if($('.ttcpricef').text() == '')
         {
            var ttprice =  Number($('.ttcprice').text());
            pp2 = ttprice- $('#paypart1').val(); 
            
            if( $('#paypart1').val()< ttprice)  $('#paypart2').val(pp2.toFixed(2)); 
            else  
            {
                $('#paypart2').val(''); 
                $('#payement2').addClass('hidden');
            }                              
         }
         else
         {
             var ttpricef = Number($('.ttcpricef').text())
             pp2 = ttpricef - $('#paypart1').val(); 
             if($('#paypart1').val()<ttpricef)   $('#paypart2').val(pp2.toFixed(2)); 
             else   
             {
                $('#paypart2').val(''); 
                 $('#payement2').addClass('hidden');
             }            
         }
         
      } 
        

    });
    



$('#paypart1').focusout(function(){
        
        if($('#paypart1').val()== '')
        {
            $('#payement2').addClass('hidden');
        }

    });
    
    
    
    

    
    
 
 
});







/*
** Function check if is number 
*/

function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 44 || $(element).val().indexOf(',') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

        else return true;
}    



/*
** Function check if is number 
*/

function isNumberOnly(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (charCode < 48 || charCode > 57)  return false;
        else                                 return true;
}    



function Reload(){
   
   UnsetUser();
   location.href = "./";
}



function CloseModal(){
    
    $('.Wait').modal('close');
}







