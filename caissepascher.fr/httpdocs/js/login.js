
jQuery(document).ready(function($){


   var formLogin = $('#login-form');

   
  

	formLogin.find('input[type="submit"]').on('click', function(event){
		event.preventDefault();
        
       var us = $('#username').val(),
           ps = $('#pass').val(); 
           
       if(us=='')
       {
           $('#login-error').html('Saisir l\'identifiant');
           $('#login-error').fadeIn().delay(1000).fadeOut();
       }    
       else if(ps=='')
       {
          $('#pass-error').html('Saisir le mot de passe');
          $('#pass-error').fadeIn().delay(1000).fadeOut();
          
       }
       else{
                
                  
                  jQuery.post('../ajax/ajax.php', {
                   action: 'user_login',
			       user_log: us,
			       user_pwd: ps
		              }).done(function(response) 
                      {
                            var rez = {};
			                try {
				                   rez = JSON.parse(response);
                                  
			                     } catch (e) {}
                                 
		      
        	               if (response === '0') 
                           {   
                                   setTimeout(function() {
					                          window.location = '../dashboard';
					                 }, 1000);   
			               } 
                         else 
                          {
                               
                               var error = '';
                      
                               switch(response)
                                {
                                    case '1':   
                                                error =  'Mot de passe incorrect'; 
                                    
                                                formLogin.find('input[type="password"]').removeClass('has-error').next('span').removeClass('is-visible');
                                                $('#pass-error').html(error);
                                                $('#pass-error').fadeIn().delay(3000).fadeOut(); 
                                    
                                    break;
                                    
                                    case '2':   
                                                error =  'Login incorrect';
                                                
                                                $('#login-error').html(error);
                                                $('#login-error').fadeIn().delay(3000).fadeOut();     
                                               
                                    break;
                                    
                                    
                                }
  
                                  
			              }
		           	
		            });
        
          }    
    });
    
});


