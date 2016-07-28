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
JHtml::_('behavior.formvalidation');
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

<div class="reset-complete<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<div class="article_body">
		<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.complete'); ?>" method="post" class="form-validate">

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<p><?php echo JText::_($fieldset->label); ?></p>		<fieldset>
				<dl>
					<dd>
						<input aria-invalid="true" required="required" aria-required="true" name="jform[password1]" id="jform_password1" value="" autocomplete="off" class="validate-password required invalid" size="30" type="password" onfocus="checkFocus(this)" onblur="checkBlur(this)" />
						<span id="placeholder_password1" class="placeholder">Новый пароль</span>
					</dd>
					<dd>
						<input required="required" aria-required="true" name="jform[password2]" id="jform_password2" value="" autocomplete="off" class="validate-password required" size="30" type="password" onfocus="checkFocus(this)" onblur="checkBlur(this)" />
						<span id="placeholder_password2" class="placeholder">Повторите пароль</span>
					</dd>
										
				</dl>
			</fieldset>
			<?php endforeach; ?>

			<div>
				<button type="submit" class="validate"><?php echo JText::_('JSUBMIT'); ?></button>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
