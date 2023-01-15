<?php

//HTML코드
$html = '
<!DOCTYPE>
<HTML>
    <HEAD>        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </HEAD>
    <BODY>        
    <h1>Command Injection</h1>
	<h2>Ping a device</h2>
	<form name="ping" action="#" method="post">
		<p>
			Enter an IP address:
			<input type="text" name="ip" size="30">
			<input type="submit" name="Submit" value="Submit">
		</p>
	</form>
    </BODY>
</HTML> ';

//PHP코드
if( isset( $_REQUEST[ 'ip' ] ) ) {
    $cmd = shell_exec( 'ping ' . $_REQUEST[ 'ip' ] );
    $cmd = iconv("EUC-KR", "UTF-8", $cmd); //한글깨짐 보정코드

	$html .= "<pre>{$cmd}</pre>";
}

echo $html;

?>
