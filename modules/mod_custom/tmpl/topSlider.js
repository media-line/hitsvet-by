var current_slide = 1;
var prev_slide = 1;
var count_slides = 1;
var SlideBlock = null;


jQuery(document).ready(function(){
	var speed = 500;
	var block_num = 1;
	var block_left = 0;
	var block_width = 0;
	var block_height = 0;
	
	var count_images = jQuery("#Slider img").length; 	
	var counter_images = 0;
	

	var prev_btn = jQuery('#prevBtnTop');
	var next_btn = jQuery('#nextBtnTop');
	var pager_nav = jQuery('.pager_nav');
	
	var animateTextBlockLeft = -750;
	var animateButtonBlockTop = 850;
	
	//var normalTextBlockLeft = parseInt(jQuery('#Slide_1 .text').css('left'));
	//var normalButtonBlockTop = parseInt(jQuery('#Slide_1 .button').css('top'));
	
	
	function init_slider()
	{
		jQuery('.top_slider .txt').each(function(){
			block_left = (jQuery(this).prev('div').width() / 2) - (jQuery(this).width() / 2)
			jQuery(this).css('left',block_left);
			block_width = 0;
			block_height = 0;
			jQuery(this).children('span').each(function(){
				jQuery(this).css('top',jQuery(this).position().top);
				jQuery(this).css('left',jQuery(this).position().left);
				jQuery(this).css('width',jQuery(this).width()+3);
				jQuery(this).css('height',jQuery(this).height());
				jQuery(this).css('text-align','center');
				
				if(jQuery(this).width() > block_width)	{
					block_width = jQuery(this).width();
					//console.log(block_width);
				}
				block_height = block_height + jQuery(this).height();
			});
			jQuery(this).css('width',block_width);
			jQuery(this).css('height',block_height);
			jQuery(this).children('br').remove();
			block_num = 1;
			jQuery(this).children('span').each(function(){
				jQuery(this).css('display','block');
				jQuery(this).css('position','absolute');	
			
				jQuery(this).addClass('txtBlock'+block_num);
				block_num++;
			});
		});
		
		var li_id = 1;
		jQuery('.top_slider li').each(function(){
			jQuery(this).attr('id',('Slide_'+li_id));
			jQuery(this).attr('class','slide');
			li_id++;
		});
		jQuery('.top_slider li').hide();
		jQuery('.top_slider li .img').hide();
		jQuery('.top_slider li#Slide_1').show();
		jQuery('.top_slider li#Slide_1 .img').show();
		
		
	}
	


	
	
	init_slider();
	
	function init_params()
	{
		if(counter_images == count_images)	{
			count_slides = jQuery("#Slider .slide").length;
			//console.log(count_slides);
		}
	}
	
	jQuery("#Slider img").load(function(){
		counter_images++;				
		init_params();
	});
	init_params();
	
	prev_btn.click(function(){
		if(current_slide != 1)	{
			current_slide--;
		}	else	{
			current_slide = count_slides;
		}
		
		prev_slide = current_slide + 1;
		if(prev_slide == (count_slides + 1))	{
			prev_slide = 1;
		}
		//console.log(prev_slide+" | "+current_slide);
		
		slideBlock(current_slide);
		//setActiveSlide(current_slide);				
	});
	
	next_btn.click(function(){
		if(current_slide != count_slides)	{
			current_slide++;
		}	else	{
			current_slide = 1;
		}
		
		prev_slide = current_slide - 1;
		if(prev_slide == 0)	{
			prev_slide = count_slides;
		}
		//console.log(prev_slide+" | "+current_slide);
		slideBlock(current_slide);
		//setActiveSlide(current_slide);
	});
	
	function slideBlock(current_slide)
	{
		hideTextBlock1();
		//hideButtonBlock();
	}
	
	function hideTextBlock1()
	{
		SlideBlock = jQuery('#Slide_'+prev_slide+' .txtBlock1');
		SlideBlock
			.animate({left: parseInt(SlideBlock.css('left')) + animateTextBlockLeft }, speed, function(){ hideTextBlock2();});
	}
	
	function hideTextBlock2()
	{
		SlideBlock = jQuery('#Slide_'+prev_slide+' .txtBlock2');
		SlideBlock
			//.animate({left: animateTextBlockLeft }, speed, function(){ hideGirlBlock();});
			.animate({top: parseInt(SlideBlock.css('top')) + animateButtonBlockTop}, speed, function(){ hideGirlBlock();});
	}
		
	function hideGirlBlock()
	{
		jQuery('#Slide_'+prev_slide+' .img').fadeOut(speed, function(){hideSlideBlock();});
	}
	
	function hideSlideBlock()
	{
		SlideBlock = jQuery('#Slide_'+prev_slide+' .txtBlock1');
		SlideBlock.css('left', SlideBlock.position().left + animateTextBlockLeft);
		
		SlideBlock = jQuery('#Slide_'+prev_slide+' .txtBlock2');
		//SlideBlock.css('left', SlideBlock.position().left + animateTextBlockLeft);
		//console.log(parseInt(SlideBlock.css('top')));
		SlideBlock.css('top', parseInt(SlideBlock.css('top')) - animateButtonBlockTop);
		//jQuery('#Slide_'+prev_slide+' .button').css('top', normalButtonBlockTop);
		jQuery('#Slide_'+prev_slide).hide();
		showSlideBlock()
	}
	
	function showSlideBlock()
	{
		SlideBlock = jQuery('#Slide_'+current_slide+' .txtBlock1');
		SlideBlock.css('left',SlideBlock.position().left + animateTextBlockLeft);
		
		SlideBlock = jQuery('#Slide_'+current_slide+' .txtBlock2');
		//console.log(parseInt(SlideBlock.css('top')));
		//SlideBlock.css('left',SlideBlock.position().left + animateTextBlockLeft);
		SlideBlock.css('top', (parseInt(SlideBlock.css('top')) + animateButtonBlockTop));
		
		jQuery('#Slide_'+current_slide).show();
		showTextBlock1();
	}
	
	
	
	function showTextBlock1()
	{
		SlideBlock = jQuery('#Slide_'+current_slide+' .txtBlock1');
		SlideBlock
			.animate({left: SlideBlock.position().left - animateTextBlockLeft }, speed, function(){ showTextBlock2();});
	}
	
	function showTextBlock2()
	{		
		SlideBlock = jQuery('#Slide_'+current_slide+' .txtBlock2');
		SlideBlock
			//.animate({left: SlideBlock.position().left - animateTextBlockLeft }, speed, function(){ showGirlBlock();});
			.animate({top: SlideBlock.position().top - animateButtonBlockTop}, speed, function(){ showGirlBlock();});
	}

	function showGirlBlock()
	{
		jQuery('#Slide_'+current_slide+' .img').fadeIn(speed);			
	}
	
	
	

/*	
  jQuery('body').on('click', '#radioBtn', function(event){
    //stopEvent();
    playRadio();
    //$('.player-play').addClass('stop-radio');
    //$('.player-play').addClass('player-stop');
    //$('.player-play').removeClass('player-play');
    return false;
  });
*/
	
	
});
