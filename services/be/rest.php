<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2008 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * REST-port of TYPO3.
 *
 * @author	Niels Fröhling <niels@frohlig.biz>
 */

define('TYPO3_PROCEED_IF_NO_USER', 1);
define('TYPO3_REST', 1);
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5';	// TODO: 'REST' should be allowed
require_once('init.php');

/* further reading for users and programmers alike:
 * - http://rest.blueoxen.net/cgi-bin/wiki.pl
 * - http://www.oio.de/public/xml/rest-webservices.htm
 *
 * 1) - which is the anonymous user?
 *    - do we have a custom user?
 *
 * 2) - authorize (and do code XXX for the various possible failures)
 *
 * 3) - split up the query like
 *    - <protocol://domain.tld/typo3/services/(fe|be)/(extension|extension configured alias)/resource?parameters>
 *    - <protocol://domain.tld/typo3/services/(extension|extension configured alias)/resource?parameters>
 *    - <protocol://domain.tld/services/(extension|extension configured alias)/resource?parameters>
 *
 * 4) - look if the queried service is enabled
 *    - fine-grain control goes through the standard typo-access
 *
 * 5) - switch the method
 */

$TYPO3_CONF_VARS['EXTCONF']['rest']['services'] = array();

/* google-sitemap, backend-extension providing the service is 'weeaar_googlesitemap' */
$TYPO3_CONF_VARS['EXTCONF']['rest']['services']['sitemap'] = array(
	/* we can't use any defines, bitfield and whatever, because we want to allow
	 * services to be registered even before the REST-extension becomes loaded
	 */
	$methods => array(
		'GET' => true,
		'PUT' => false,
		'POST' => false,
		'DELETE' => false
	}
);

/* news-feeds, backend-extension providing the service is 'tt_news' */
$TYPO3_CONF_VARS['EXTCONF']['rest']['services']['newsfeed'] = array(
	$methods => array(
		'GET' => true,
		'PUT' => false,
		'POST' => false,
		'DELETE' => false
	}
);

/* ics-calendars, backend-extension providing the service is 'cal' */
$TYPO3_CONF_VARS['EXTCONF']['rest']['services']['calendar'] = array(
	$methods => array(
		'GET' => true,
		'PUT' => true,
		'POST' => true,
		'DELETE' => true
	}
);

/* file-io, backend-extension providing the service is 'file' */
$TYPO3_CONF_VARS['EXTCONF']['rest']['services']['file'] = array(
	$methods => array(
		'GET' => true,
		'PUT' => true,
		'POST' => true,
		'DELETE' => true
	}
);

/* dam-io, backend-extension providing the service is 'dam' */
$TYPO3_CONF_VARS['EXTCONF']['rest']['services']['dam'] = array(
	$methods => array(
		'GET' => true,
		'PUT' => true,
		'POST' => true,
		'DELETE' => true
	}
);

class rest_dispatcher() {

	/* ------------------------------------------------------------------ */
	function locate() {

	}

	/* GET */
	function unavailable($frag) {
		/* - do an action on a non-existing resource of an existing resource
		 *   idealy the systom would recognize if the resource existed before
		 *   to indicate the transition from existed->destroyed, realistically
		 *   this probably would require too much capacities
		 */
		header('HTTP/1.1 410 Gone');
		die();
	}

	function nonexistant($frag) {
		/* - do an action on a non-existing resource of a non-existing resource
		 *   most actions can't be clearly defined to recursive resource-creating
		 *   plain-path to url mapping is a counter-example (being very well possible)
		 */
		header('HTTP/1.1 404 Not Found');
		die();
	}

	/* PUT/POST/DELETE */
	function scheduled($frag) {
		/* - any asyncronous action
		 */
		header('HTTP/1.1 202 Accepted');
		die();
	}

	function done($frag) {
		/* - put single entry/multiple entries, replacing the existing entry/entries
		 * - post multiple entries
		 */
		header('HTTP/1.1 200 OK');
		die();
	}

	function relocate($frag) {
		/* - put a single entry, creating a new resource on a non-existing resource
		 */
		header('HTTP/1.1 200 OK');
		header('Location: ' . $base . $frag);
		die();
	}

	function created($frag) {
		/* - post a single entry, creating a new resource appending to an existing resource
		 */
		header('HTTP/1.1 201 Created');
		header('Location: ' . $base . $frag);
		die();
	}

	function unchanged($frag) {
		/* - put a single entry/multiple entries, replacing the existing entry, but no change happened
		 * - post a single entry/multiple entries, adding to the existing entry, but no change happened
		 */
		header('HTTP/1.1 204 No Content');
		die();
	}

	function collision($frag) {
		/* - actions from multiple clients on the same resource like simultanious put or put + delete
		 */
		header('HTTP/1.1 409 Conflict');
		die();
	}

	/* ------------------------------------------------------------------ */
	function get() {
		header('HTTP/1.1 404 Not Found'); die();
	}

	function put() {
		header('HTTP/1.1 405 Method Not Allowed'); die();
	}

	function post() {
		header('HTTP/1.1 405 Method Not Allowed'); die();
	}

	function delete() {
		header('HTTP/1.1 405 Method Not Allowed'); die();
	}

};

if ($_SERVER['REQUEST_METHOD'] == "HEAD")
	echo 'HEAD' . '<br />';
if ($_SERVER['REQUEST_METHOD'] == "GET")
	echo 'GET' . '<br />';
if ($_SERVER['REQUEST_METHOD'] == "PUT")
	echo 'PUT' . '<br />';
if ($_SERVER['REQUEST_METHOD'] == "POST")
	echo 'POST' . '<br />';
if ($_SERVER['REQUEST_METHOD'] == "DELETE")
	echo 'DELETE' . '<br />';

//	$totalWritten = 0;
//        $inFP = @fopen( "php://input", "rb" );
////        $outFP = @fopen( basename( $REQUEST_URI ), "wb" );
//        while( $data = fread( $inFP, 1024 ) )
//        {
////                fwrite( $outFP, $data );
//                $totalWritten += strlen( $data );
//        }
//        fclose($inFP);
////        fclose($outFP);
//
////        if( $totalWritten ) header( "HTTP/1.0 200 Success" );
////        else header( "HTTP/1.0 404 Failed" );
//echo $totalWritten;
//
//echo php_sapi_name();
?>