/**
 *
 * Realex payment plugin
 *
 * @author Valérie Isaksen
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

jQuery().ready(function ($) {

    /************/
    /* Handlers */
    /************/
    handleIntegrationParameters = function () {
        var integration = $('#paramsintegration').val();

        $('.redirect, .remote').parents('tr').hide();

        if (integration == 'redirect') {
            $('.redirect').parents('tr').show();
        } else if (integration == 'remote') {
            $('.remote').parents('tr').show();
        }
    }

    handleRealvault = function () {
        var realvault = $('#paramsrealvault').val();

        $('.realvault').parents('tr').hide();

        if (realvault == 1) {
            $('.realvault').parents('tr').show();
        }
    }

    handleSettlement = function () {
        var settlement = $('#paramssettlement').val();

        $('.settlement').parents('tr').hide();

        if (settlement == 0) {
            $('.settlement').parents('tr').show();
        }
    }
    handleDcc = function () {
        var dcc = $('#paramsdcc').val();

        $('.dcc').parents('tr').hide();

        if (dcc == 1) {
            $('.dcc').parents('tr').show();
        }
    }
    /**********/
    /* Events */
    /**********/
    $('#paramsintegration').change(function () {
        handleIntegrationParameters();

    });
    $('#paramsrealvault').change(function () {
        handleRealvault();
    });
    $('#paramssettlement').change(function () {
        handleSettlement();
    });
    $('#paramsdcc').change(function () {
        handleDcc();
    });
    /*****************/
    /* Initial calls */
    /*****************/
    handleIntegrationParameters();
    handleRealvault();
    handleSettlement();
    handleDcc();
});
