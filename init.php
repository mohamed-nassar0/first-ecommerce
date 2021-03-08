<?php
// connect with db
include 'connect.php';


// Routes
$template = 'include/templates/';
$css   	  = 'layout/css/';
$js 	  = 'layout/js/';	 

// include header 
include $template . 'header.php';

// include functions file 
include 'include/functions/functions.php';

// include nav bar when variable $no_nav is not found in the page
if (!isset($no_nav)) {
	include $template . 'nav.php';
}

