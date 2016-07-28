<?php
// *****************************************************************************
// *
// * Product: Realex Payments
// * Version: 2.0.1
// * Release Date: 16 Nov 2012
// * For: Joomla 2.5/VirtueMart 2.0.x
// * Author: E-commerce Solution
// * Email: support@virtuemart-solutions.com
// * Website: http://www.virtuemart-solutions.com
// * Support: http://support.virtuemart-solutions.com
// * Copyright: (C) 2012 E-commerce Solution
// * Licence: Commercial
// * Details: http://www.virtuemart-solutions.com/commercial-licence
// * Filename: jump.php
// *
// *****************************************************************************
// * This software is licensed, and may be used and copied only in accordance  *
// * with the terms of such license, and must include this copyright notice.   *
// *****************************************************************************
?>
<?php

// url sent in get
$url = 'https://hpp.sandbox.realexpayments.com/pay';
?>

<html>
<head>
  <title>Transferring...</title>
  <meta http-equiv="Content-Type"
    content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">

<form
  name="form1"
  action="<?php echo $url; ?>"
  method="POST">

<?php
  // get the posted vars
  $field_array = array_keys($_POST);

  //loop posted fields
  for($i = 0;$i < count($field_array);$i++) {
    $actual_var = $field_array[$i];
    $actual_val = stripslashes($_POST[$actual_var]);

    //hidden form field
    echo ("<input type=\"hidden\" name=\"");
    echo ($actual_var . "\" value=\"");
    echo (trim($actual_val) . "\" />\n");
  }

?>
</form>

<script language="javascript">
  var f=document.forms;
  f = f[0];
  f.submit();
</script>

</body>
</html>
