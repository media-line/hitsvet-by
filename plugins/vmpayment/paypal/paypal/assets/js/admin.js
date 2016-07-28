/**
 *
 * Paypal payment plugin
 *
 * @author Jeremy Magne
 * @author Valérie Isaksen
 * @version $Id: paypal.php 7217 2013-09-18 13:42:54Z alatak $
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2013 Virtuemart Team. All rights reserved.
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
    handleCredentials = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        var sandbox = $("input[name='params[sandbox]']:checked").val();
        if (sandbox==1) {
            var sandboxmode = 'sandbox';
        } else {
            var sandboxmode = 'production';
        }


        $('.std,.api,.live,.sandbox,.sandbox_warning, .accelerated_onboarding').parents('tr').hide();
        $('.get_sandbox_credentials').hide();
        $('.get_paypal_credentials').hide();
        // $('.authentication').hide();
        $('.authentication').parents('tr').hide();


        if (paypalproduct == 'std' && sandboxmode == 'production') {
            $('.std.live').parents('tr').show();
            $('.get_paypal_credentials').show();
            $('#paramspaypal_merchant_email').addClass("required");

        } else if (paypalproduct == 'std' && sandboxmode == 'sandbox') {
            $('.std.sandbox').parents('tr').show();
            $('.get_sandbox_credentials').show();
            $('#paramssandbox_merchant_email').addClass("required");

        } else if (paypalproduct == 'api' && sandboxmode == 'production') {
            $('.api.live').parents('tr').show();
            $('.get_paypal_credentials').show();
            $('#paramspaypal_merchant_email').removeClass("required");

        } else if (paypalproduct == 'api' && sandboxmode == 'sandbox') {
            $('.api.sandbox').parents('tr').show();
            $('.get_sandbox_credentials').show();
            $('#paramssandbox_merchant_email').removeClass("required");

        } else if (paypalproduct == 'exp' && sandboxmode == 'production') {
            $('.api.live').parents('tr').show();
            $('.exp.live').parents('tr').show();
            $('.accelerated_onboarding').parents('tr').show();
            $('.get_paypal_credentials').show();
            $('#paramspaypal_merchant_email').removeClass("required");

            //$('.authentication.live.certificate').parents('tr').show();

        } else if (paypalproduct == 'exp' && sandboxmode == 'sandbox') {
            $('.api.sandbox').parents('tr').show();
            $('.exp.sandbox').parents('tr').show();
            $('.accelerated_onboarding').parents('tr').show();
            $('.get_sandbox_credentials').show();
            $('#paramssandbox_merchant_email').removeClass("required");
            // $('.sandbox.authentication').show();

        } else if (paypalproduct == 'hosted' && sandboxmode == 'production') {
            $('.api.live').parents('tr').show();
            $('.hosted.live').parents('tr').show();
            $('.get_paypal_credentials').show();
            $('#paramspaypal_merchant_email').removeClass("required");

        } else if (paypalproduct == 'hosted' && sandboxmode == 'sandbox') {
            $('.api.sandbox').parents('tr').show();
            $('.hosted.sandbox').parents('tr').show();
            $('.get_sandbox_credentials').show();
            $('#paramssandbox_merchant_email').removeClass("required");
        }

        if (sandboxmode == 'sandbox') {
            $('.sandbox_warning').parents('tr').show();
        }
    }

    handlePaymentType = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        var currentval = $('#paramspayment_type').val();
        $('.payment_type').parents('tr').hide();
        if (paypalproduct == 'std') {
            $('.payment_type').parents('tr').show();
        }

        if (paypalproduct == 'exp' || paypalproduct == 'api' || paypalproduct == 'hosted') {
            $('#paramspayment_type option[value=_cart]').attr('disabled', '');
            $('#paramspayment_type option[value=_oe-gift-certificate]').attr('disabled', '');
            $('#paramspayment_type option[value=_donations]').attr('disabled', '');
            $('#paramspayment_type option[value=_xclick-auto-billing]').attr('disabled', '');
            if (currentval == '_cart' || currentval == '_oe-gift-certificate' || currentval == '_donations' || currentval == '_xclick-auto-billing') {
                $('#paramspayment_type').val('_xclick');
            }

        } else {
            $('#paramspayment_type option[value=_cart]').removeAttr('disabled');
            $('#paramspayment_type option[value=_oe-gift-certificate]').removeAttr('disabled');
            $('#paramspayment_type option[value=_donations]').removeAttr('disabled');
            $('#paramspayment_type option[value=_xclick-auto-billing]').removeAttr('disabled');
        }
        $('#paramspayment_type').trigger("liszt:updated");


    }

    handleCreditCard = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        $('.creditcard').parents('tr').hide();
        $('.cvv_required').parents('tr').hide();
        if (paypalproduct == 'api') {
            $('.creditcard').parents('tr').show();
            $('.cvv_required').parents('tr').show();

        }
    }
    handleRefundOnCancel = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        $('.paypal_vm').parents('tr').show();
        if (paypalproduct == 'std') {
            $('.paypal_vm').parents('tr').hide();
        }
    }

    handleCapturePayment = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        var payment_action = $('#paramspayment_action').val();
        $('.capture').parents('tr').hide();
        if (paypalproduct == 'hosted' && payment_action == 'Authorization') {
            $('.capture').parents('tr').show();
        }
    }
    handleTemplate = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        $('.paypaltemplate').parents('tr').hide();

        if (paypalproduct == 'hosted') {
            $('.paypaltemplate').parents('tr').show();
        }
    }

    handleTemplateParams = function () {
        var paypaltemplate = $('#paramstemplate').val();
        var paypalproduct = $('#paramspaypalproduct').val();
        $('.hosted.templateA,.hosted.templateB,.hosted.templateC,.hosted.template_warning').parents('tr').hide();

        if (paypalproduct == 'hosted' && paypaltemplate == 'templateA') {
            $('.hosted.templateA,.hosted.template_warning').parents('tr').show();
        }
        if (paypalproduct == 'hosted' && paypaltemplate == 'templateB') {
            $('.hosted.templateB,.hosted.template_warning').parents('tr').show();
        }
        if (paypalproduct == 'hosted' && paypaltemplate == 'templateC') {
            $('.hosted.templateC,.hosted.template_warning').parents('tr').show();
        }
    }

    handlePaymentAction = function () {
        var paymenttype = $('#paramspayment_type').val();
        //var currentval = $('#paramspayment_action').val();
        if (paymenttype == '_xclick-subscriptions' || paymenttype == '_xclick-payment-plan' || paymenttype == '_xclick-auto-billing') {
            $('#paramspayment_action').val('Sale');
            $('#paramspayment_action').parents('tr').hide();
            $('#paramspayment_action').trigger("liszt:updated");
        } else {
            $('#paramspayment_action').parents('tr').show();
        }
    }

    handleLayout = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        $('.paypallayout').parents('tr').hide();
        $('.stdlayout').parents('tr').hide();
        $('.explayout').parents('tr').hide();
        // $('.hosted.paypallayout').parents('tr').hide();
        if (paypalproduct == 'std' || paypalproduct == 'exp' || paypalproduct == 'hosted') {
            $('.paypallayout').parents('tr').show();
        }
        if (paypalproduct == 'std') {
            $('.stdlayout').parents('tr').show();
        }
        if (paypalproduct == 'exp') {
            $('.explayout').parents('tr').show();
        }
    }
    handleAuthentication = function () {
        var paypalAuthentication = $('#paramsauthentication').val();
        var sandbox = $("input[name='params[sandbox]']:checked").val();
        if (sandbox==1) {
            var sandboxmode = 'sandbox';
        } else {
            var sandboxmode = 'production';
        }

        var paypalproduct = $('#paramspaypalproduct').val();
        $('.authentication').parents('tr').hide();
        if (paypalproduct != 'std') {
            if (sandboxmode == 'sandbox') {
                $('.authentication.sandbox.select').parents('tr').show();
                if (paypalAuthentication == 'certificate') {
                    $('.authentication.sandbox.certificate').parents('tr').show();
                } else {
                    $('.authentication.sandbox.signature').parents('tr').show();

                }
            }
            else if (sandboxmode == 'production') {
                // $('.authentication.live.certificate').parents('tr').show();
                $('.authentication.live.select').parents('tr').show();
                if (paypalAuthentication == 'certificate') {
                    $('.authentication.live.certificate').parents('tr').show();
                } else {
                    $('.authentication.live.signature').parents('tr').show();

                }
            }
        }

    }
    handleExpectedMaxAmount = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        $('.expected_maxamount').parents('tr').hide();

        if (paypalproduct == 'exp') {
            $('.expected_maxamount').parents('tr').show();
        }
    }
    handleWarningAuthorizeStd = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        var payment_action = $('#paramspayment_action').val();
        $('.warning_std_authorize').parents('tr').hide();
        if (paypalproduct == 'std' && payment_action == 'Authorization') {
            $('.warning_std_authorize').parents('tr').show();
        }
    }

    handleWarningHeaderImage = function () {
        var headerimage = $('#paramheaderimg').val();
        $('.warning_headerimg').parents('tr').hide();
        if (headerimage != '-1') {
            $('.warning_headerimg').parents('tr').show();
        }
    }

    handlePaymentTypeDetails = function () {
        var selectedMode = $('#paramspayment_type').val();
        $('.xclick').parents('tr').hide();
        $('.cart').parents('tr').hide();
        $('.subscribe').parents('tr').hide();
        $('.plan').parents('tr').hide();
        $('.billing').parents('tr').hide();
        var paypalproduct = $('#paramspaypalproduct').val();
        if (paypalproduct == 'std') {
            switch (selectedMode) {
                case '_xclick':
                    $('.xclick').parents('tr').show();
                    $('.cart').parents('tr').hide();
                    $('.subscribe').parents('tr').hide();
                    $('.plan').parents('tr').hide();
                    $('.billing').parents('tr').hide();
                    break;
                case '_cart':
                    $('.xclick').parents('tr').hide();
                    $('.cart').parents('tr').show();
                    $('.subscribe').parents('tr').hide();
                    $('.plan').parents('tr').hide();
                    $('.billing').parents('tr').hide();
                    break;
                case '_oe-gift-certificate':
                    $('.cart').parents('tr').hide();
                    $('.subscribe').parents('tr').hide();
                    $('.plan').parents('tr').hide();
                    $('.billing').parents('tr').hide();
                    break;
                case '_xclick-subscriptions':
                    $('.cart').parents('tr').hide();
                    $('.subscribe').parents('tr').show();
                    $('.plan').parents('tr').hide();
                    $('#paramssubcription_trials').trigger('change');
                    $('.billing').parents('tr').hide();
                    handleSubscriptionTrials();
                    break;
                case '_xclick-auto-billing':
                    $('.cart').parents('tr').hide();
                    $('.subscribe').parents('tr').hide();
                    $('.plan').parents('tr').hide();
                    $('.billing').parents('tr').show();
                    handleMaxAmountType();
                    break;
                case '_xclick-payment-plan':
                    $('.cart').parents('tr').hide();
                    $('.subscribe').parents('tr').hide();
                    $('.plan').parents('tr').show();
                    $('.billing').parents('tr').hide();
                    handlePaymentPlanDefer();
                    break;
                case '_donations':
                    $('.cart').parents('tr').hide();
                    $('.subscribe').parents('tr').hide();
                    $('.plan').parents('tr').hide();
                    $('.billing').parents('tr').hide();
                    break;
            }
        }
    }

    handleSubscriptionTrials = function () {
        var nbTrials = $('#paramssubcription_trials').val();
        switch (nbTrials) {
            case '0':
                $('.trial1').parents('tr').hide();
                //$('.trial2').parents('tr').hide();
                break;
            case '1':
                $('.trial1').parents('tr').show();
                //$('.trial2').parents('tr').hide();
                break;
            //case '2':
            //	$('.trial1').parents('tr').show();
            //	$('.trial2').parents('tr').show();
            //	break;
        }
    }

    handlePaymentPlanDefer = function () {
        var doDefer = $('#paramspayment_plan_defer').val();
        var paypalproduct = $('#paramspaypalproduct').val();
        $('.defer').parents('tr').hide();
        if (doDefer == 1) {
            if (paypalproduct == 'std') {
                $('.defer_std').parents('tr').show();
            } else {
                $('.defer_api').parents('tr').show();
            }
        }
    }

    handleMaxAmountType = function () {
        var max_amount_type = $('#paramsbilling_max_amount_type').val();
        switch (max_amount_type) {
            case 'cart':
            case 'cust':
                $('.billing_max_amount').parents('tr').hide();
                break;
            case 'value':
            case 'perc':
                $('.billing_max_amount').parents('tr').show();
                break;
        }
    }

    handlePaymentFeesWarning = function () {
        var paypalproduct = $('#paramspaypalproduct').val();
        var selectedMode = $('#paramspayment_type').val();
        if ((paypalproduct == 'api' || paypalproduct == 'exp') && (selectedMode == '_xclick-subscriptions' || selectedMode == '_xclick-payment-plan')) {
            $('.warning_transaction_cost').parents('tr').show();
        } else {
            $('.warning_transaction_cost').parents('tr').hide();
        }
    }


    /**********/
    /* Events */
    /**********/
    $("input[name='params[sandbox]']").change(function () {
        handleCredentials();
        handleAuthentication();
    });

    $('#paramspaypalproduct').change(function () {
        handleCredentials();
        handleAuthentication();
        handleExpectedMaxAmount();
        handleTemplateParams();
        handleCreditCard();
        handleRefundOnCancel();
        handleLayout();
        handleTemplate();
        handleWarningAuthorizeStd();
        handlePaymentType();
        handlePaymentPlanDefer();
    });
    $('#paramsauthentication').change(function () {
        handleAuthentication();
    });
    $('#paramstemplate').change(function () {
        handleTemplateParams();
    });
    $('#paramspayment_action').change(function () {
        handleWarningAuthorizeStd();
        handleCapturePayment();
    });

    $('#paramspayment_type').change(function () {
        handlePaymentAction();
        handlePaymentTypeDetails();
        handlePaymentFeesWarning();
    });

    $('#paramheaderimg').change(function () {
        handleWarningHeaderImage();
    });

    $('#paramssubcription_trials').change(function () {
        handleSubscriptionTrials();
    });

    $('#paramspayment_plan_defer').change(function () {
        handlePaymentPlanDefer();
    });

    $('#paramsbilling_max_amount_type').change(function () {
        handleMaxAmountType();
    });


    /*****************/
    /* Initial calls */
    /*****************/
    handleCredentials();
    handleAuthentication();
    handleCreditCard();
    handleExpectedMaxAmount();
    handleCapturePayment();
    handleRefundOnCancel();
    handleLayout();
    handleTemplate();
    handleTemplateParams();
    handleWarningAuthorizeStd();
    handlePaymentType();
    handlePaymentAction();
    handlePaymentTypeDetails();
    handleWarningHeaderImage();
    handlePaymentFeesWarning();
    handlePaymentPlanDefer();

});
