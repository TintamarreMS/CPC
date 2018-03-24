jQuery(document).ready(function($){
	
    var cartWrapper = $('.cart-container');

	var productId = '';
    var productName = '';
    var productPrice = '';
    var productVat = '';
    var productCat = '';
 
    

	if( cartWrapper.length > 0 ) {
		//store jQuery objects
		var cartBody = cartWrapper.find('.body')
		var cartList = cartBody.find('ul').eq(0);
		var cartTotal = cartWrapper.find('.checkout').find('span');
		var addToCartBtn = $('.add-to-cart');

		var undoTimeoutId;

		//add product to cart
		addToCartBtn.on('click', function(event){
			event.preventDefault();
			addToCart($(this));

		});

	

		//delete an item from the cart
		cartList.on('click', '.delete', function(event){
			event.preventDefault();
			removeProduct($(event.target).parents('.product'));
		});

		

	}




	function addToCart(trigger) {
		//update cart product list
		addProduct(trigger);
		//update total price
		updateCartTotal(trigger.data('price'), true);

	}

	function addProduct(trigger) {

        productId    = trigger.data('id');
        productName  = trigger.data('name');
        productPrice = trigger.data('price');
        productVat   = trigger.data('vat');
        productCat   = trigger.data('cat');
        
		cartList.append('<li class="product"><img src="../images/icon/product-preview.png"><span class="name" id="'+productId+'">'+productName+'</span><span class="price" vat="'+productVat+'">'+productPrice+'</span><span class="euro">&euro;</span><i class="material-icons delete">delete_forever</i></li>');
	}




	function removeProduct(product) 
    {
		clearInterval(undoTimeoutId);
		cartList.find('.deleted').remove();
		
		var topPosition = product.offset().top - cartBody.children('ul').offset().top ,
			productTotPrice = Number(product.find('.price').text());
		
		    product.css('top', topPosition+'px').addClass('deleted');
  
	    	updateCartTotal(productTotPrice, false);
	
           cartList.find('.deleted').remove();
       
	}





	function updateCartTotal(price, bool) 
    {
		bool ? cartTotal.text( (Number(cartTotal.text()) + Number(price)).toFixed(2) )  : cartTotal.text( (Number(cartTotal.text()) - Number(price)).toFixed(2) );
        
        if(Number(cartTotal.text()) == 0)
        {
            $('#cart-text').html('panier vide');
            $('#pay').addClass('disabled');  
        }
        else
        {
            $('#cart-text').html('');
            $('#pay').removeClass('disabled');
        }
	}





});




















