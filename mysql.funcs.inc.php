<?php
function safeIntoDB(&$value,$key=NULL) {
	if (strpos($key,'filterquery')===false // don't clean filters - we've cleaned those separately in the sqlparts function
		&& !preg_match("/^'/d/d/d/d-/d/d-/d/d'$/",$value) ) // and don't clean dates
		{
		if ( get_magic_quotes_gpc() && !empty($value) && is_string($value) )
			$value = stripslashes($value);
		if(version_compare(phpversion(),"4.3.0",'<'))
			mysql_escape_string($value);
		else  
       		$value = mysql_real_escape_string($value);
	}
	return true;
}
?>
