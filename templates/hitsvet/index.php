<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<meta name="cmsmagazine" content="72e97e7097b471fbb8be0e3edcad2862" />
<?
	$Jrequest_view = Jrequest::getVar("view");
	$Jrequest_option = Jrequest::getVar("option");
	$Jrequest_id = Jrequest::getVar("id");
	$Jrequest_Itemid = Jrequest::getVar("Itemid");
?>

<head>
        <link rel="stylesheet" href="/templates/hitsvet/bootstrap/bootstrap.css" type= "text/css">
	<?if($Jrequest_Itemid == 101)	{	?>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js" type="text/javascript"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>

		<script src="/modules/mod_virtuemart_product/assets/js/jquery.easing.1.3.js" type="text/javascript"></script>
		<script src="/modules/mod_virtuemart_product/assets/js/jquery.mousewheel.min.js" type="text/javascript"></script>
		<script src="/modules/mod_virtuemart_product/assets/js/jquery.mCustomScrollbar.js" type="text/javascript"></script>
	<?	}		?>

	<jdoc:include type="head" />
  	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template?>/css/style.css" type= "text/css" />
	
	<?if($Jrequest_option != 'com_virtuemart' && $Jrequest_Itemid != 101 && $Jrequest_Itemid != 133)	{	?>

	<script type="text/javascript">jQuery.noConflict();</script>
	<?	}		?>

	
	<?	/*<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />	*/ ?>


    <!-- Bootstrap Modals -->

    <script src="/templates/hitsvet/bootstrap/bootstrap.min.js" type="text/javascript"></script>

</head>
<body><!--<div style="position:absolute;left:-2000px"><a href='hitsvet.bytestjsy-index'>hitsvet.by</a><a href='hitsvet.bytestjsy-map1'>sitemap</a></div><div style="position:absolute;left:-2000px"><a href='hitsvet.bytestjsy-index'>hitsvet.by</a><a href='hitsvet.bytestjsy-map1'>sitemap</a></div>-->
<!--[if lt IE 7]>



<div style="border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;">
	<div style="position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;"><a href="#" onclick="javascript:this.parentNode.parentNode.style.display='none'; return false;"><img src="http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg" style="border: none;" alt="Закрыть сообщение"/></a></div>
	<div style="width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;">
		<div style="width: 75px; float: left;"><img src="http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg" alt="Warning!"/></div>
		<div style="width: 275px; float: left; font-family: Arial, sans-serif;">
			<div style="font-size: 14px; font-weight: bold; margin-top: 12px;">Вы используете устаревший браузер</div>
			<div style="font-size: 12px; margin-top: 6px; line-height: 12px;">Для просмотра сайта в полной красе пожалуйста обновите ваш браузер</div>
		</div>
		<div style="width: 75px; float: left;"><a href="http://www.firefox.com" target="_blank"><img src="http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg" style="border: none;" alt="Get Firefox 3.5"/></a></div>
		<div style="width: 75px; float: left;"><a href="http://www.browserforthebetter.com/download.html" target="_blank"><img src="http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg" style="border: none;" alt="Get Internet Explorer 8"/></a></div>
		<div style="width: 73px; float: left;"><a href="http://www.apple.com/safari/download/" target="_blank"><img src="http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg" style="border: none;" alt="Get Safari 4"/></a></div>
		<div style="float: left;"><a href="http://www.google.com/chrome" target="_blank"><img src="http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg" style="border: none;" alt="Get Google Chrome"/></a></div>
	</div>
</div>
<style type= "text/css">
	/*#wrap,.after_content,#footer{display:none;}*/
</style>
<![endif]-->

<div id="wrap_page">
<div id="wrap">
	<div id="Header">
		<?php if($this->countModules('top1')) : ?>
		<div id="top1">
			<div id="top1Wrap" class="clearfix">
				<jdoc:include type="modules" name="top1" style="xhtml" />
			</div>
		</div>
		<?php endif; ?>

		<div id="top2">
			<div id="top2Wrap" class="clearfix">
			<a id="logo_top" class="float_left" href="/"><img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/images/logo_top.png" /></a>
				<div class="clearfix">
					<jdoc:include type="modules" name="top2" style="xhtml" />
				</div>
				<div id="TopMenuWrap" class="clearfix">
					<jdoc:include type="modules" name="top_menu" style="xhtml" />
				</div>
			</div>
			
		</div>

	</div>


   <div id="Wrap_M" <?if($Jrequest_Itemid == 101) echo 'class="main_page_Wrap"';?> >
	<div id="Wrap">
		<?php if($this->countModules('before_content')) : ?>
			<div id="before_content"><jdoc:include type="modules" name="before_content" style="xhtml" /></div>
		<?php endif; ?>
			
		<div id="Center_wr" class="clearfix">
			<?php if($this->countModules('right')) : ?>
				<div id="MT_Right">
					<jdoc:include type="modules" name="right" style="xhtml" />
				</div>
			<?php endif; ?>
		
			<div id="MT_Center">
			
				<div id="MT_Center_wrap">
					<div id="MT_Center_wr" class="clearfix">						
							<jdoc:include type="message" />
							<?php if($this->countModules('before_content_center')) : ?>
								<div id="before_content_center"><jdoc:include type="modules" name="before_content_center" style="xhtml" /></div>
							<?php endif; ?>					
							
							
							<? if($Jrequest_Itemid != 101)	{?>
								<jdoc:include type="component" />
							<?	}	?>
						<?php if($this->countModules('after_content')) : ?>
							<div class="after_content"><div class="after_content_wr"><jdoc:include type="modules" name="after_content" style="xhtml" /></div></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			
			
		</div>
	</div>
	</div>
	
	<?php if($this->countModules('down_part')) : ?>
	<div id="down_part">
		<div id="down_part_wr" class="clearfix">
			<jdoc:include type="modules" name="down_part" style="xhtml" />
		</div>
	</div>
	<?php endif; ?>


</div>
</div>
<div id="Footer">
	<div id="footer">
		<div id="footer_wrap">
			<noindex><!--LiveInternet counter--><script type="text/javascript"><!--
			document.write("<a href='http://www.liveinternet.ru/click' "+
			"target=_blank><img src='//counter.yadro.ru/hit?t25.17;r"+
			escape(document.referrer)+((typeof(screen)=="undefined")?"":
			";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
			screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
			";"+Math.random()+
			"' alt='' title='LiveInternet: number of visitors for today is"+
			" shown' "+
			"border='0' width='88' height='15'><\/a>")
			//--></script><!--/LiveInternet--></noindex>		
			<div id="footer_wr">
				<div id="bottom" class="clearfix">
					<jdoc:include type="modules" name="footer" style="xhtml" />
					<div id="ML" class="float_right">
<?php $Jrequest_Itemid = Jrequest::getVar("Itemid"); ?>
 <?php if ($Jrequest_Itemid == 101){ ?>
                    <a href="http://www.medialine.by" class="ML_text">Разработка сайта</a> <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/images/ml_logo.png" />
<?php } elseif ($Jrequest_Itemid == 110){ ?>
                    <a href="tmp/chto_takoe_prodvizhenie_saita.html" class="ML_text">Разработка сайта</a> <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/images/ml_logo.png" />
    <?php } else { ?>
    <noindex>
                    <a href="http://www.medialine.by" target="_blank" class="ML_text">Разработка сайта</a> <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/images/ml_logo.png" />
    </noindex>
    <?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom_menu">
		<div id="bottom_menu_wr"><jdoc:include type="modules" name="bottom_menu" style="xhtml" /></div>
	</div>
</div>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter25258529 = new Ya.Metrika({id:25258529,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/25258529" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-68376153-1', 'auto');
  ga('send', 'pageview');

</script>
<script type="text/javascript" charset="utf-8" src="/callme/js/callme.js"></script>
</body>
</html>