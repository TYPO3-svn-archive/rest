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

define('TYPO3_REST', 1);
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