<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');

$instant_users_group_id = 2;
$show_tests = 0;
$current_user = &JFactory::getUser();

//echo'<pre>';print_r($current_user->groups,0);echo'</pre>';
foreach($current_user->groups as $gr){
	if($gr != $instant_users_group_id)	{
		$show_tests = 1;
	}	else	{
		$show_tests = 0;
	}
}
?>
<style>
	div#system-message-container{display:none !important;}
</style>

<script type="text/javascript">
	jQuery(document).ready(function(){
		if(jQuery('#system-message-container dd.message').html() != null)	{
			var Html_ = jQuery('#system-message-container dd.message').html();
			jQuery('#info_box').html(Html_);
		}
	});	
</script>

<div class="profile<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
	<div class="article_body">
		<?php echo $this->loadTemplate('core'); ?>

		<?php echo $this->loadTemplate('params'); ?>

		<?php echo $this->loadTemplate('custom'); ?>

		<?php if (JFactory::getUser()->id == $this->data->id) : ?>
			<a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>" class="edit_profile"><?php echo JText::_('COM_USERS_Edit_Profile'); ?></a>
		<?php endif; ?>
		<?if($show_tests == 1)	{?>
			<a href="<? echo JRoute::_('index.php?option=com_ariquizlite&amp;view=quizzes&amp;task=quiz_list&amp;Itemid=115')?>">Список тестов</a><br />
		<?	}	?>
		
		<a href="<? echo JRoute::_('index.php?option=com_users&view=profile&layout=edit&subscribe=1&Itemid=168')?>">Рассылка акций</a>
	</div>
</div>
