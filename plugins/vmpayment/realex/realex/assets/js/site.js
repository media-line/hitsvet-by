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

jQuery().ready(function($) {


	$('input[name=virtuemart_paymentmethod_id]').change(function() {
		var selectedMethod = $('input[name=virtuemart_paymentmethod_id]:checked').val();
		$('.paymentMethodOptions').hide();
		$('#paymentMethodOptions_'+selectedMethod).show();
	});

	$('input[name=virtuemart_paymentmethod_id]').trigger('change');
	
});
