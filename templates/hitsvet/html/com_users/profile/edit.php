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
//load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load( 'plg_user_profile', JPATH_ADMINISTRATOR );
?>
<script type="text/javascript">
	/*
	jQuery(document).ready(function(){
		jQuery('#jform_password1').keyup(
			function(){
				var el = jQuery(this);
				if(el.val().length > 0)	{
					jQuery('#placeholder_password1').hide();
				}	else	{
					jQuery('#placeholder_password1').show();
				}
			}
		);
		
		jQuery('#jform_password2').keyup(
			function(){
				var el = jQuery(this);
				if(el.val().length > 0)	{
					jQuery('#placeholder_password2').hide();
				}	else	{
					jQuery('#placeholder_password2').show();
				}
			}
		);
	});
	*/
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


<div class="profile-edit<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
	<div class="article_body">
	<a id="GoBack" href="javascript:history.back()" onMouseOver="window.status='Назад';return true">Вернуться назад</a>
	<? if (JRequest::getVar('subscribe') != 1)	{?>
	
<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
<?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($group);?>
	<?php if (count($fields)):?>
	<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
		<legend><h3><?php echo JText::_($fieldset->label); ?></h3></legend>
		<?php endif;?>
<?
	$u_id = '';
	$u_name = '';
	$u_username = '';
	$u_email1 = '';
	$u_email2 = '';
	$u_mobile_phone = '';
	$u_subscribe_email = '';
	$u_birthday = '';
	foreach ($fields as $field)	{
		switch($field->name)	{
			case 'jform[id]':
				$u_id = $field->value;
				break;
			case 'jform[name]':
				$u_name = $field->value;
				break;
			case 'jform[username]':
				$u_username = $field->value;
				break;
			case 'jform[email1]':
				$u_email1 = $field->value;
				break;
			case 'jform[email2]':
				$u_email2 = $field->value;
				break;
			case 'jform[mobile_phone]':
				$u_mobile_phone = $field->value;
				break;
			case 'jform[subscribe_email]':
				$u_subscribe_email = $field->value;
				break;
			case 'jform[birthday]':
				$u_birthday = $field->value;
				break;
		}
		//echo'<pre>';print_r($field->value,0);echo'</pre>';
		//echo'<pre>';print_r($field->name,0);echo'</pre>';
	}

?>		
		<dl>
			<input name="jform[id]" id="jform_id" value="361" type="hidden" />
			<input name="jform[name]" id="jform_name" value="<?=$u_name?>" type="hidden" />
			<input name="jform[username]" id="jform_username" value="<?=$u_username?>" type="hidden" />
			<input name="jform[email1]" id="jform_email1" value="<?=$u_email1?>" type="hidden" />
			<input name="jform[email2]" id="jform_email1" value="<?=$u_email2?>" type="hidden" />
			
			<dt></dt>
			<dd>
				<input name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password" size="30" type="password" onfocus="checkFocus(this)" onblur="checkBlur(this)" />
				<span id="placeholder_password1" class="placeholder">Пароль</span>
			</dd>
			
			<dt></dt>
			<dd>
				<input name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password" size="30" type="password" onfocus="checkFocus(this)" onblur="checkBlur(this)" />
				<span id="placeholder_password2" class="placeholder">Повторите пароль</span>
			</dd>
			

	</fieldset>
	<?php endif;?>
<?php endforeach;?>

		<div>
			<button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
	<?	}	else	{?>
	
	<?
	$fields = $this->form->getFieldset(0);
	
	//echo'<pre>';print_r($fields['jform_id']->getValue('mobile_phone'),0);echo'</pre>';
	//echo'<pre>';print_r($fields,0);echo'</pre>';
	$u_id = '';
	$u_name = '';
	$u_username = '';
	$u_email1 = '';
	$u_email2 = '';
	$u_mobile_phone = '';
	$u_subscribe_email = '';
	$u_birthday = '';
	foreach ($fields as $field)	{
		switch($field->name)	{
			case 'jform[id]':
				$u_id = $field->value;
				break;
			case 'jform[name]':
				$u_name = $field->value;
				break;
			case 'jform[username]':
				$u_username = $field->value;
				break;
			case 'jform[email1]':
				$u_email1 = $field->value;
				break;
			case 'jform[email2]':
				$u_email2 = $field->value;
				break;
			case 'jform[mobile_phone]':
				$u_mobile_phone = $field->value;
				break;
			case 'jform[subscribe_email]':
				$u_subscribe_email = $field->value;
				break;
			case 'jform[birthday]':
				$u_birthday = $field->value;
				break;
		}
		//echo'<pre>';print_r($field->value,0);echo'</pre>';
		//echo'<pre>';print_r($field->name,0);echo'</pre>';
	}
	?>
		<link rel="stylesheet" href="/templates/slavia/html/com_users/profile/cusel/css/cusel.css " type="text/css" media="screen" />

		<script type="text/javascript" src="/templates/slavia/html/com_users/profile/cusel/js/jquery-1.6.1.js"></script>
		<script src="/templates/slavia/html/com_users/profile/cusel/js/cusel.js" type="text/javascript"></script>
		<script type="text/javascript" src="/templates/slavia/html/com_users/profile/cusel/js/jScrollPane.js"></script>
		<script type="text/javascript" src="/templates/slavia/html/com_users/profile/cusel/js/jquery.mousewheel.js"></script>
		
		

			<script type="text/javascript">
				
				
				var user_birthday = null;
				var jform_birthday = null;
				
				jQuery(document).ready(function(){
					jform_birthday = jQuery('#jform_birthday');
					user_birthday = jform_birthday.val().split('-');

					
					var params = {
							changedEl: ".profile-edit select",
							visRows: 5,
							scrollArrows: true
						}
					cuSel(params);
				});
				
				function change_birthday(el,i)
				{
					user_birthday[i] = el.value;
					jform_birthday.val(user_birthday.join('-'));
				}
								
			</script>


	
<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
<?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($group);?>
	<?php if (count($fields)):?>
	<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
		<legend><h3><?php echo JText::_($fieldset->label); ?></h3></legend>
		<?php endif;?>
		<dl>
		

			<input name="jform[id]" id="jform_id" value="<?=$u_id?>" type="hidden" />
			<input name="jform[username]" id="jform_username" value="<?=$u_username?>" type="hidden" />
			<input name="jform[email1]" id="jform_email1" value="<?=$u_email1?>" type="hidden" />
			<input name="jform[email2]" id="jform_email2" value="<?=$u_email2?>" type="hidden" />

			<div class="item padding_r_25">
			<dt>
				<label id="jform_name-lbl" for="jform_name" class="required" title="">Ф.И.О.<span class="star">&nbsp;*</span></label>
			</dt>
			<dd>
				<input required="required" aria-required="true" name="jform[name]" id="jform_name" value="<?=$u_name?>" class="required" size="30" type="text" />
			</dd>
			</div>
			
			<div class="item">
			<dt>
				<label id="jform_birthday-lbl" for="jform_birthday" class="" title="">Дата рождения</label>
				
			</dt>
			<dd>
				<input name="jform[birthday]" id="jform_birthday" value="<?=$u_birthday?>" size="30" type="hidden" />
				<? $user_day = JHtml::_('date', $u_birthday, JText::_('d')); //echo $user_day;?>
				<select name="birthday_day" id="birthday_day" onChange="change_birthday(this,2)">
					<?	for ($i = 1; $i <= 31; $i++) {
							if($i == $user_day)	{
								$user_day_select = ' selected="selected"';
							}	else	{
								$user_day_select = '';
							}
					
							echo "<option value=\"$i\"$user_day_select>$i</option>";
						}					
					?>
				</select>
				<? $user_month = JHtml::_('date', $u_birthday, JText::_('m')); //echo $user_month;?>
				<select name="birthday_month" id="birthday_month" onChange="change_birthday(this,1)">
					<?$m = '01';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '02';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '03';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '04';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '05';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '06';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '07';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '08';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '09';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '10';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '11';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
					<?$m = '12';?>
					<option value="<?=$m?>"<?echo($user_month == $m) ? ' selected="selected"' : ''?>><?=$m?></option>
				</select>
				<? $year = date('Y');	?>
				<? //$year_u = ($u_birthday('Y'));	?>
				<? $user_year = JHtml::_('date', $u_birthday, JText::_('Y'));?>
				
				<select name="birthday_year" id="birthday_year" onChange="change_birthday(this,0)">
					<?	for ($i = ($year-10); $i >= ($year-60); $i--) {
							if($i == $user_year)	{
								$user_year_select = ' selected="selected"';
							}	else	{
								$user_year_select = '';
							}
							echo "<option value=\"$i\"$user_year_select>$i</option>";
						}					
					?>
				</select>
				
			</dd>
			</div>
			
			<div class="item clr padding_r_25">
			<dt>
				<label id="jform_subscribe_email-lbl" for="jform_subscribe_email" class="required" title="">E-mail рассылка<span class="star">&nbsp;*</span></label>									
			</dt>
			<dd>
				<input required="required" aria-required="true" name="jform[subscribe_email]" class="validate-email required" id="jform_subscribe_email" value="<?=$u_subscribe_email?>" size="30" type="email" />
			</dd>
			</div>
			
			<div class="item">
			<dt>
				<label id="jform_mobile_phone-lbl" for="jform_mobile_phone" class="required" title="">SMS рассылка<span class="star">&nbsp;*</span></label>
			</dt>
			<dd>
				<input required="required" aria-required="true" name="jform[mobile_phone]" id="jform_mobile_phone" value="<?=$u_mobile_phone?>" class="required" size="30" type="text" />
			</dd>
			<span class="notice">Поля с символом <span class="star">*</span> - обязательны для заполнения.</span>
			</div>
			
		</dl>
	</fieldset>
	<?php endif;?>
<?php endforeach;?>

		<div>
			<button type="submit" class="validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>

			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
	<?	}	?>
</div>
</div>
