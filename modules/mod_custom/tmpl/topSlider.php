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
$js = '/modules/mod_custom/tmpl/topSlider.js';
$document->addScript($js);
?>



<div class="custom<?php echo $moduleclass_sfx ?>" <?php if ($params->get('backgroundimage')): ?> style="background-image:url(<?php echo $params->get('backgroundimage');?>)"<?php endif;?> >
	<div id="Slider">	
		<div id="Slider_wrap" class="customWr clearfix">
			<?php echo $module->content;?>
		</div>
	</div>
</div>
<button id="prevBtnTop">&nbsp;</button>
<button id="nextBtnTop">&nbsp;</button>

