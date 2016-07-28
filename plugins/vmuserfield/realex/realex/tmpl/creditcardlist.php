<?php
/**
 *
 * REALEX  payment plugin
 *
 * @author Jeremy Magne
 * @version $Id: REALEX.php 7217 2013-09-18 13:42:54Z alatak $
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

$storedCreditCards = $viewData['storedCreditCards'];
?>

	<?php
$i = 1;
foreach ($storedCreditCards as $storedCreditCard) {

	$checked = JHTML::_('grid.id', $i, $storedCreditCard->id, null, 'card_id');
	?>
<?php echo $checked ?>
<?php echo vmText::_('VMUSERFIELD_REALEX_DELETE_CARD') ?>
<br />
	<?php
	$display_fields = array('realex_saved_pmt_type', 'realex_saved_pmt_digits', 'realex_saved_pmt_name');

	echo $storedCreditCard->realex_saved_pmt_type. ' '.$storedCreditCard->realex_saved_pmt_digits. ' ('.$storedCreditCard->realex_saved_pmt_name .')';
?>
	<br />
<?php

}
?>



