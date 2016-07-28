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
<div class="reset-confirm<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<div class="article_body">
		<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.confirm'); ?>" method="post" class="form-validate">

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<p><?php echo JText::_($fieldset->label); ?></p>		<fieldset>
				<dl>

				<dd><input aria-invalid="true" required="required" aria-required="true" name="jform[username]" id="jform_username" class="required invalid" size="30" type="text" value="E-mail" onfocus="if(this.value=='E-mail') this.value='';" onblur="if(this.value=='') this.value='E-mail';" /></dd>

				<dd><input required="required" aria-required="true" name="jform[token]" id="jform_token" class="required" size="32" type="text"  value="Код подтверждения" onfocus="if(this.value=='Код подтверждения') this.value='';" onblur="if(this.value=='') this.value='Код подтверждения';" /></dd>
				</dl>
			</fieldset>
			<?php endforeach; ?>

			<div>
				<button type="submit" class="orange_btn validate"><?php echo JText::_('JSUBMIT'); ?></button>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
