<?php
	/**
	* Embedded CSS to Inline CSS Converter Tool
	* @version 0.2
	* @updated 12/04/2009
	* 
	* @author David Lim
	* @email miliak@orst.edu
	* @link http://davidandjennilyn.com/instyle
	* @acknowledgements Simple HTML DOM
	*/ 

	// If a path is passed in, take the web page and make its embedded CSS inline.
	if (isset($_GET['path'])) {
		require_once 'instyle.php';
		$inlinecss = new instyle();
		$email_content = file_get_contents($_GET['path']);
		$message = $inlinecss->convert($email_content);
		echo $message;
	} else {
		echo '<html><body><p>Usage: {host}/instyle/?path={url of web page}</p><p>Example: http://davidandjennilyn.com/instyle_demo/?path=http://davidandjennilyn.com/instyle_demo/test.html</p></body></html>';
	}

/* End of file index.php */
