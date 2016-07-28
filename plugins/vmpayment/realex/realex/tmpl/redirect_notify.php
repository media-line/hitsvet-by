<?php
/**
 *
 * Realex payment plugin
 *
 * @author Valerie Isaksen
 * @version $Id$
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2014 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('_JEXEC') or die();


?>

<?php
if ($viewData["success"]) {
	?>
	<p>
		<?php
		echo JText::sprintf('VMPAYMENT_REALEX_NOTIFY_SUCCESS',  $viewData["shop_name"]);
		?>
	</p>
	<?php
	if ($viewData["order_history_comments"]) {
		?>
		<p>
			<?php
			echo $viewData["order_history_comments"];
			?>
		</p>
	<?php
	}
	?>
	<p>
		<a href="<?php echo $viewData["return_success"] ?>"><?php echo JText::sprintf('VMPAYMENT_REALEX_NOTIFY_RETURN_URL', $viewData["shop_name"]); ?></a>
	</p>
<?php
} else {
	?>
	<p>
		<?php
		echo JText::sprintf('VMPAYMENT_REALEX_NOTIFY_DECLINED', $viewData["order_number"], $viewData["shop_name"]);
		?>
		<br/>
		<?php

		echo $viewData["declined_message"];
		?>
	</p>
	<p>
		<a href="<?php echo $viewData["return_declined"] ?>"><?php echo JText::sprintf('VMPAYMENT_REALEX_NOTIFY_RETURN_URL', $viewData["shop_name"]); ?></a>

	</p>
<?php
}
?>


