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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<div class="reset<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<div class="article_body">	
		<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=reset.request'); ?>" method="post" class="form-validate">

			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<p><?php echo JText::_($fieldset->label); ?></p>		
			<fieldset>
				<dl>
					<dd><input aria-invalid="true" required="required" aria-required="true" name="jform[email]" id="jform_email" class="validate-username required invalid" size="30" type="text" value="Адрес электронной почты" onfocus="if(this.value=='Адрес электронной почты') this.value='';" onblur="if(this.value=='') this.value='Адрес электронной почты';"></dd>
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
