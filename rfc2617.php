<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Niels Fröhling <niels@frohling.biz>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

define('RFC2617_AUTHENTICATED', 1);	// the given user authenticates just fine
define('RFC2617_NOTATTEMPTED', 0);	// no authentification-information send
define('RFC2617_NOTCONFIGURED', -1);	// the given user haven't configured REST-access
define('RFC2617_NOTAUTHENTICATED', -2);	// the given user fails to authenticate
define('RFC2617_NOTVALID', -3);		// the authentification-information is utterly wrong

class rfc2617 {

	/* we got 'digest', 'digestfail', 'basic', 'basicfail' */
	var $authtype = "digest";

	/* variables used to authenticate */
	var $realm  = "Typo3";
	var $nonce  = "";
	var $opaque = "";
	var $table  = "";

	/* result of the authentification */
	var $infos;
	var $report;

	function authenticate() {
		/* determine the type of authentication required */
		if (TYPO3_MODE == 'BE') {
			/* by default the BE woud be 'superchallenged', this translates
			 * to about 'digest' (which is more secure, but we don't mind)
			 */
			$this->authtype = 'digest';
			$this->realm = 'typo3be@' . $_SERVER['SERVER_NAME'];
			$this->table = 'be_users';
		}
		else if (TYPO3_MODE == 'FE') {
			/* by default the FE would be 'normal', this is about 'basic' */
			$this->authtype = ($_SERVER['REQUEST_METHOD'] == 'GET' ? 'basicfail' : 'basic');
			$this->realm = 'typo3fe@' . $_SERVER['SERVER_NAME'];
			$this->table = 'fe_users';
		}
		else
			die();

		/* prepare for the right type of authentification */
		if (($this->authtype == 'digest') ||
		    ($this->authtype == 'digestfail')) {
		    	/* some initial seed to be send the first time */
			$this->nonce  = md5(uniqid('') . getmypid());
			$this->opaque = md5($this->realm);

			/* authenticate! */
			$report = $this->digest($_SERVER['PHP_AUTH_DIGEST']);

			/* well, hmm, the realm is wrong or we got some user/pass,
			 * which means the browser tries something, but not the right
			 * thing, give him the correct hint
			 */
			if (($this->infos['realm'] && ($this->infos['realm'] != $this->realm)) ||
			    ($_SERVER['PHP_AUTH_USER'] || $_SERVER['PHP_AUTH_PW'])) {
			       header(
			       'WWW-Authenticate:' .
			       	' Digest realm="'.$this->realm.'"' .
			       	' qop="auth"' .
			       	' nonce="'.$this->nonce.'"' .
			       	' opaque="'.$this->opaque.'"'
			       ); die();
			}

			/* if we hit the requirement, we have to reply in some sensefull
			 * and _machine-readable_ way, don't forget that we have a REST-port
			 * here which favors maschine-logins
			 */
			if ($this->authtype == 'digest') {
				/* well we have to have it, report */
				if ($report == RFC2617_NOTATTEMPTED) {
			        	header(
			        	'WWW-Authenticate:' .
			        		' Digest realm="'.$this->realm.'"' .
			        		' qop="auth"' .
			        		' nonce="'.$this->nonce.'"' .
			        		' opaque="'.$this->opaque.'"'
			        	); die();
				}
				/* well you tried but that one can't, report */
				if ($report == RFC2617_NOTCONFIGURED) {
			        	header(
			        	'HTTP/1.1 403 Forbidden'
			        	); die();
				}
				/* well you tried but that one was wrong, report */
				if ($report == RFC2617_NOTAUTHENTICATED) {
			        	header(
			        	'HTTP/1.1 401 Unauthorized'
			        	);
			        	header(
			        	'WWW-Authenticate:' .
			        		' Digest realm="'.$this->realm.'"' .
			        		' qop="auth"' .
			        		' nonce="'.$this->nonce.'"' .
			        		' opaque="'.$this->opaque.'"'
			        	); die();
				}
				/* well you tried but that one reallly wrong, report */
				if ($report == RFC2617_NOTVALID) {
			        	header(
			        	'HTTP/1.1 400 Bad Request'
			        	); die();
				}
			}
		}
		else if (($this->authtype == 'basic') ||
			 ($this->authtype == 'basicfail')) {

			$report = $this->basic($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

			/* well, hmm, the realm is wrong or we got some digest,
			 * which means the browser tries something, but not the right
			 * thing, give him the correct hint
			 */
			if (($this->infos['realm'] && ($this->infos['realm'] != $this->realm)) ||
			    ($_SERVER['PHP_AUTH_DIGEST'])) {
			      header(
			      'WWW-Authenticate:' .
			      	' Basic realm="'.$this->realm.'"'
			      ); die();
			}

			/* if we hit the requirement, we have to reply in some sensefull
			 * and _machine-readable_ way, don't forget that we have a REST-port
			 * here which favors maschine-logins
			 */
			if ($this->authtype == 'basic') {
				/* well we have to have it, report */
				if ($report == RFC2617_NOTATTEMPTED) {
			        	header(
			        	'WWW-Authenticate:' .
			        		' Basic realm="'.$this->realm.'"'
			        	); die();
				}
				/* well you tried but that one can't, report */
				if ($report == RFC2617_NOTCONFIGURED) {
			        	header(
			        	'HTTP/1.1 403 Forbidden'
			        	); die();
				}
				/* well you tried but that one was wrong, report */
				if ($report == RFC2617_NOTAUTHENTICATED) {
			        	header(
			        	'HTTP/1.1 401 Unauthorized'
			        	);
			        	header(
			        	'WWW-Authenticate:' .
			        		' Basic realm="'.$this->realm.'"'
			        	); die();
				}
				/* well you tried but that one reallly wrong, report */
				if ($report == RFC2617_NOTVALID) {
			        	header(
			        	'HTTP/1.1 400 Bad Request'
			        	); die();
				}
			}
		}
		else
			die();

		return ($this->report = $report);
	}

	/* ------------------------------------------------------------------ */
	function digestfind($user) {
		/* no user, no search */
		if (trim($user) == null)
			return null;

		/* look up the user by the username */
		$found = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'tx_rest_restkey',
					$this->table,
					'username = \'' . $user . '\''
				);

		if ($found) {
			$key = $GLOBALS['TYPO3_DB']->sql_fetch_row($found);
			$GLOBALS['TYPO3_DB']->sql_free_result($found);

			return $key[0];
		}

		return null;
	}

	function digestinfo($digestraw) {
		if (!$digestraw)
			return array();

		/* split up the incoming digest-line */
		$infos = array();
		$pairs = explode(',', $digestraw);
		foreach ($pairs as $pair) {
			$pair = explode('=', $pair);
			$infos[trim($pair[0])] = rtrim(ltrim($pair[1], '" '), '" ');
		}

		/* collect some fixed definitions */
		$infos['method'] = $_SERVER['REQUEST_METHOD'];
		$infos['key'] = $this->digestfind($infos['username']);

		return ($this->infos = $infos);
	}

	function digesthash2069($digestinfo) {
		/* RFC2069 */
		$A1 = $digestinfo['user'] . ':' . $this->realm . ':' . $digestinfo['key'];
		$A2 = $digestinfo['method'] . ':' . $digestinfo['uri'];

		$HA1 = md5($A1);
		$HA2 = md5($A2);

		$HAR = md5($HA1 . ':' . $digestinfo['nonce'] . ':' . $HA2);

		return $HAR;
	}

	function digesthash2617($digestinfo) {
		/* RFC2617 */
		$A1 = $digestinfo['username'] . ':' . $this->realm . ':' . $digestinfo['key'];

		if ($digestinfo['qop'] == 'auth')
			$A2 = $digestinfo['method'] . ':' . $digestinfo['uri'];
		else if ($digestinfo['qop'] == 'auth-int')
			$A2 = $digestinfo['method'] . ':' . $digestinfo['uri'] . ':' . '???';
		else
			$A2 = '';

		$HA1 = md5($A1);
		$HA2 = md5($A2);

		$HAR = md5($HA1 . ':' . $digestinfo['nonce'] . ':' . $digestinfo['nc'] . ':' . $digestinfo['cnonce'] . ':' . $digestinfo['qop'] . ':' . $HA2);

		return $HAR;
	}

	function digest($digestraw) {
		/* now he didn't even try it */
		if (!$digestraw)
			return RFC2617_NOTATTEMPTED;

		/* try to make some informative responses to the state of digest-verification */
		$digestinfo = $this->digestinfo($digestraw);
		if (!$digestinfo['username'] || !$digestinfo['response'])
			return RFC2617_NOTVALID;
		if ($digestinfo['key'] === '')
			return RFC2617_NOTCONFIGURED;
		if ($digestinfo['response'] != $this->digesthash2617($digestinfo))
			return RFC2617_NOTAUTHENTICATED;

		return RFC2617_AUTHENTICATED;
	}

	/* ------------------------------------------------------------------ */
	function basicfind($user) {
		/* no user, no search */
		if (trim($user) == '')
			return null;

		/* look up the user by the username */
		$found = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'tx_rest_restkey',
					$this->table,
					'username = \'' . $user . '\''
				);

		if ($found) {
			$key = $GLOBALS['TYPO3_DB']->sql_fetch_row($found);
			$GLOBALS['TYPO3_DB']->sql_free_result($found);

			return $key[0];
		}

		return null;
	}

	function basicinfo($u, $p) {
		if (!$u)
			return array();

		/* split up the incoming basic-line */
		$infos = array();
		$infos['username'] = $u;
		$infos['password'] = $p;

		/* collect some fixed definitions */
		$infos['method'] = $_SERVER['REQUEST_METHOD'];
		$infos['key'] = $this->basicfind($infos['username']);

		return ($this->infos = $infos);
	}

	function basic($u, $p) {
		/* now he didn't even try it */
		if (!$u)
			return RFC2617_NOTATTEMPTED;

		/* try to make some informative responses to the state of basic-verification */
		$basicinfo = $this->basicinfo($u, $p);
		if (!$basicinfo['username'])
			return RFC2617_NOTVALID;
		if ($basicinfo['key'] === '')
			return RFC2617_NOTCONFIGURED;
		if ($basicinfo['password'] != $basicinfo['key'])
			return RFC2617_NOTAUTHENTICATED;

		return RFC2617_AUTHENTICATED;
	}
};
?>