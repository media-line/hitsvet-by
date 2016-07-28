<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<style>
	#fancy_outer{height:530px !important;}
	div#system-message-container{display:none !important;}
</style>

<script type="text/javascript">
	jQuery(document).ready(function(){
	
		if(jQuery('#system-message-container dd.message').html() != null)	{
			var Html_ = '<div class="article_body">'+jQuery('#system-message-container dd.message').html()+'</div>';
			jQuery('.item-page').html(Html_);
		}
	
		jQuery('#jform_email2').prop('type','hidden');
	
		var input_email = document.getElementById('jform_email1');

		input_email.oninput = function() {
		  document.getElementById('jform_username').value = this.value;
		  document.getElementById('jform_name').value = this.value;
		  document.getElementById('jform_email2').value = this.value;
		}
	});
	
	function checkFocus(el)
	{
		if(jQuery(el).val() == '')	{
			jQuery(el).next('.placeholder').hide();
		}
	}
	
	function checkBlur(el)
	{
		if(jQuery(el).val() == '')	{
			jQuery(el).next('.placeholder').show();
		}
	}
	

</script>

<?	if(JRequest::getVar('tmpl','') == ''  )	{	?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1><div class="item-page"></div>
<?	}?>

<div class="registrationWrap">
<div class="registration<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading') && JRequest::getVar('tmpl','') == 'component') : ?>
	<h3><?php echo $this->escape($this->params->get('page_heading')); ?></h3>
<?php endif; ?>
<div style="margin-bottom:15px;">Мы с радостью поделимся с вами новостями о поступлении нового товара и начале акций!</div>



	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
	<fieldset>
	<dl>
	<div class="row">
	<dd>
		<input type="hidden" name="jform[username]" id="jform_username" class="validate-username required" size="30" value="" />
		<input type="hidden" name="jform[name]" id="jform_name" value="" class="required" size="30">
	</dd>
	</div>
	
	<div class="row">
	<dd>
		<input type="text" name="jform[email1]" class="validate-email required" id="jform_email1" size="30" value="E-mail" onfocus="if(this.value=='E-mail') this.value='';" onblur="if(this.value=='') this.value='E-mail';" />
	</dd>
	<dt>
		<label id="jform_email1-lbl" for="jform_email1" class="hasTip required">Для проверки регистрации и в целях безопасности нам нужен адрес вашей электронной почты</label>
	</dt>
	</div>

	<div class="row">
	<dd>
		<input type="hidden" name="jform[email2]" class="validate-email required" id="jform_email2" size="30" value="" />
	</dd>
	<dt></dt>
	</div>

	<div class="row">
	<dd>
		<input type="password" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password required" size="30" onfocus="checkFocus(this)" onblur="checkBlur(this)" />
		<span id="placeholder_password1" class="placeholder">Пароль</span>
	</dd>
	<dt>
		<label id="jform_password1-lbl" for="jform_password1" class="hasTip required" >Должен содержать не менее 5 символов и не может совпадать с логином. Не используйте простые пароли, будьте разумны.</label>
	</dt>
	</div>
	
	<div class="row">
	<dd>
		<input type="password" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password required" size="30" onfocus="checkFocus(this)" onblur="checkBlur(this)" />
		<span id="placeholder_password2" class="placeholder">Повторите пароль</span>
	</dd>
	<dt></dt>
	</div>
	</dl>
	</fieldset>
		<div>
			<button type="submit" class="validate"><?php echo 'Отправить'//JText::_('JREGISTER');?></button>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="registration.register" />
			<?php echo JHtml::_('form.token');?>
		</div>
		
	</form>
</div>
</div>
