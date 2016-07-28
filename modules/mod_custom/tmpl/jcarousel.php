<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_custom
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$document =& JFactory::getDocument();
$js = '/modules/mod_custom/assets/jcarousellite_1.0.1.pack.js';
//$document->addScript($js);
?>
<script type="text/javascript" src="/modules/mod_custom/assets/jcarousellite_1.0.1.pack.js"></script>
<script type="text/javascript">
	jQuery(function(){
		
		jQuery(".gallery").jCarouselLite({
				btnNext: ".next",
				btnPrev: ".prev",
				auto: 3000,
				speed: 500,
				visible: 1
		});
		
	});
</script>


<div class="custom" >
	<button class="prev"> </button>
	<button class="next"> </button>
	<div class="gallery"><?php echo $module->content;?></div>
</div>
