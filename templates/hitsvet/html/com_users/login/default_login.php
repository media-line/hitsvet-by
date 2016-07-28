<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
	<script type="text/javascript">
		/*
		jQuery(document).ready(function(){
			//if(jQuery("div#system-message-container").is("li")){
				var Html_ = '<div class="article_body">'+jQuery('#system-message-container li').text()+'</div>';
				jQuery('.item-page').html(Html_);
			//}
			//jQuery('#system-message-container li').html('<h2>'+jQuery('#system-message-container li').text()+'</h2>');
			
			if(jQuery('#password').val() != '')	{
				jQuery('#placeholder_password').hide();
			}
		});
		*/

	
		function checkFocus(el)
		{
			//if(jQuery(el).val() == '')	{
				jQuery(el).next('.placeholder').hide();
			//}
		}
		
		function checkBlur(el)
		{
			if(jQuery(el).val() == '')	{
				jQuery(el).next('.placeholder').show();
			}
		}
		
	</script>
	
<?	if(JRequest::getVar('tmpl','') == ''  )	{	?>
	<h1><span>Вход в систему</span></h1><?	//<div class="item-page"></div>?>
<?	}?>
<div class="loginWrap">
<div class="login<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) { ?>
	<h3>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h3>
	<?php }	?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif ; ?>

		<?php if($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image')!='')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif ; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post">

		<fieldset>

			<div class="login-fields">
				<input onblur="if(this.value=='') this.value='Логин';" onfocus="if(this.value=='Логин') this.value='';" name="username" id="username" value="Логин" class="validate-username" size="25" type="text">
			</div>
			<div class="login-fields">
				<input name="password" id="password" value="" class="validate-password" size="25" type="password" onfocus="checkFocus(this)" onblur="checkBlur(this)" />
				<span id="placeholder_password" class="placeholder">Пароль</span>
			</div>
			
			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="login-fields">
				<label id="remember-lbl" for="remember"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
				<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes"  alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" />
			</div>
			<?php endif; ?>
			<button type="submit" class="orange_btn"><?php echo JText::_('JLOGIN'); ?></button>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
<div>
	<ul>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
			<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
		</li>
	</ul>
</div>
</div>
