if(typeof Virtuemart === "undefined")
	{
		var Virtuemart = {
			setproducttype : function (form, id) {
				form.view = null;
				var $ = jQuery, datas = form.serialize();
				var prices = form.parent(".productdetails").find(".product-price");
				if (0 == prices.length) {
					prices = $("#productPrice" + id);
				}
				datas = datas.replace("&view=cart", "");
				prices.fadeTo("fast", 0.75);
				$.getJSON(window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=productdetails&task=recalculate&virtuemart_product_id='+id+'&format=json' + window.vmLang, encodeURIComponent(datas),
					function (datas, textStatus) {
						prices.fadeTo("fast", 1);
						// refresh price
						for (var key in datas) {
							var value = datas[key];
							if (value!=0) prices.find("span.Price"+key).show().html(value);
							else prices.find(".Price"+key).html(0).hide();
						}
					});
				return false; // prevent reload
			},
			productUpdate : function(mod) {

				var $ = jQuery ;
				$.ajaxSetup({ cache: false })
				$.getJSON(window.vmSiteurl+"index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json"+window.vmLang,
					function(datas, textStatus) {
						if (datas.totalProduct >0) {
							mod.find(".vm_cart_products").html("");
							$.each(datas.products, function(key, val) {
								$("#hiddencontainer .container").clone().appendTo(".vmCartModule .vm_cart_products");
								$.each(val, function(key, val) {
									if ($("#hiddencontainer .container ."+key)) mod.find(".vm_cart_products ."+key+":last").html(val) ;
								});
							});
							mod.find(".total").html(datas.billTotal);
							mod.find(".show_cart").html(datas.cart_show);
						}
						mod.find(".total_products").html(datas.totalProductTxt);
					}
				);
			},
			sendtocart : function (form){

				if (Virtuemart.addtocart_popup ==1) {
					
					
					//console.log(form.children('.addtocart-bar').find('.addtocart-button').html());
					Virtuemart.cartEffect(form) ;
				} else {
					form.append('<input type="hidden" name="task" value="add" />');
					form.submit();
				}
			},
			cartEffect : function(form) {

                var $ = jQuery ;
                $.ajaxSetup({ cache: false });
                var datas = form.serialize();

                if(usefancy){
                    $.fancybox.showActivity();
                }

                $.getJSON(vmSiteurl+'index.php?option=com_virtuemart&nosef=1&view=cart&task=addJS&format=json'+vmLang,encodeURIComponent(datas),
                function(datas, textStatus) {
                    if(datas.stat ==1){

                        var txt = datas.msg;
                    } else if(datas.stat ==2){
                        var txt = datas.msg +"<H4>"+form.find(".pname").val()+"</H4>";
                    } else {
                        var txt = "<H4>"+vmCartError+"</H4>"+datas.msg;
                    }
					
					var btm_add_cart = form.children('.addtocart-bar').find('.addtocart-button').find('.addtocart-button');
					var width_1 = btm_add_cart.width() + 44;
					btm_add_cart.val('В корзине');
					btm_add_cart.width(width_1);
					btm_add_cart.addClass('product_in_cart');
					
                    if(usefancy){
                        /*
						$.fancybox({
                                "titlePosition" : 	"inside",
                                "transitionIn"	:	"fade",
                                "transitionOut"	:	"fade",
                                "changeFade"    :   "fast",
                                "type"			:	"html",
                                "autoCenter"    :   true,
                                "closeBtn"      :   false,
                                "closeClick"    :   false,
                                "content"       :   txt
                            }
                        );
						*/
						
						$.fancybox.hideActivity();
						
                    } else {
                        $.facebox.settings.closeImage = closeImage;
                        $.facebox.settings.loadingImage = loadingImage;
                        //$.facebox.settings.faceboxHtml = faceboxHtml;
                        $.facebox({ text: txt }, 'my-groovy-style');
                    }

                    if ($(".vmCartModule")[0]) {
                        Virtuemart.productUpdate($(".vmCartModule"));
                    }
                });

                $.ajaxSetup({ cache: true });
			},
			product : function(carts) {
				carts.each(function(){
					var cart = jQuery(this),
					step=cart.find('input[name="quantity"]'),
					addtocart = cart.find('input.addtocart-button'),
					plus   = cart.find('.quantity-plus'),
					minus  = cart.find('.quantity-minus'),
					select = cart.find('select:not(.no-vm-bind)'),
					radio = cart.find('input:radio:not(.no-vm-bind)'),
					virtuemart_product_id = cart.find('input[name="virtuemart_product_id[]"]').val(),
					quantity = cart.find('.quantity-input');

                    var Ste = parseInt(step.val());
                    //Fallback for layouts lower than 2.0.18b
                    if(isNaN(Ste)){
                        Ste = 1;
                    }
					addtocart.click(function(e) { 
						Virtuemart.sendtocart(cart);
						return false;
					});
					plus.click(function() {
						var Qtt = parseInt(quantity.val());
						if (!isNaN(Qtt)) {
							quantity.val(Qtt + Ste);
						Virtuemart.setproducttype(cart,virtuemart_product_id);
						}
						
					});
					minus.click(function() {
						var Qtt = parseInt(quantity.val());
						if (!isNaN(Qtt) && Qtt>Ste) {
							quantity.val(Qtt - Ste);
						} else quantity.val(Ste);
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					select.change(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					radio.change(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					quantity.keyup(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
				});

			}
		};
		
		jQuery.noConflict();
		
		var loading = false;
		jQuery(window).scroll(function(){
			if(parseInt(jQuery('#pages_current').val()) < parseInt(jQuery('#pages_total').val()))	{
				if(((jQuery(window).scrollTop()+jQuery(window).height())+150)>=jQuery(document).height()){
					if(loading == false){
						loading = true;
						jQuery('#loadingbar').show();
						//var instock_val = jQuery('#instock_val').val();
						//var document_base = jQuery('#document_base').val();
						var document_base = jQuery('#FilterBtn').attr('href');
						var next_url = '';
						if(document_base.indexOf('?') +1 ) 	{							
							//next_url = document_base+'&start='+jQuery('#start').val()+'&format=raw'+instock_val;
							next_url = document_base+'&start='+jQuery('#start').val()+'&format=raw';
						}	else	{
							//next_url = document_base+'?start='+jQuery('#start').val()+'&format=raw'+instock_val;
							next_url = document_base+'?start='+jQuery('#start').val()+'&format=raw';
						}
						jQuery.get(next_url, function(loaded){
							jQuery('.pagination_wr').remove();
							jQuery('.c_part_WR').append(loaded);
							//повторная инициализация добавления товара в корзину аяксом
							jQuery('.addtocart-area > form').each(function(){
								var cart = jQuery(this),
								step=cart.find('input[name="quantity"]'),
								addtocart = cart.find('input.addtocart-button'),
								plus   = cart.find('.quantity-plus'),
								minus  = cart.find('.quantity-minus'),
								select = cart.find('select:not(.no-vm-bind)'),
								radio = cart.find('input:radio:not(.no-vm-bind)'),
								virtuemart_product_id = cart.find('input[name="virtuemart_product_id[]"]').val(),
								quantity = cart.find('.quantity-input');

								addtocart.click(function(e) { 
									Virtuemart.sendtocart(cart);
									return false;
								});
							});
							jQuery('#loadingbar').hide();
							loading = false;
						});
					}
				}
			}
		});
 
		
		
		jQuery(document).ready(function($) {

			Virtuemart.product($("form.product"));

			$("form.js-recalculate").each(function(){
				if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
					var id= $(this).find('input[name="virtuemart_product_id[]"]').val();
					Virtuemart.setproducttype($(this),id);

				}
			});
			
			var max_height_item = 0;
			$('.row').each(function(){
				max_height_item = 0;
				$(this).children('.product').children('.spacer').children('.spacer_wr').find('.prod_ttl').children('h3').each(function(){
					if($(this).height() > max_height_item)	{
						max_height_item = $(this).height();
					}
				});
				//console.log(max_height_item);
				$(this).children('.product').children('.spacer').children('.spacer_wr').find('.prod_ttl').children('h3').css('height',max_height_item);
			})
		});
		
		
	}
