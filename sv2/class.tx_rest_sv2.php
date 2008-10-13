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

require_once(PATH_t3lib.'class.t3lib_svbase.php');
require_once(t3lib_extMgm::extPath('rest').'rfc2617.php');

/**
 * Service "FE authentification though HTTP-auth" for the "rest" extension.
 *
 * @author	Niels Fröhling <niels@frohling.biz>
 * @package	TYPO3
 * @subpackage	tx_rest
 */
class tx_rest_sv2 extends tx_sv_authbase {

	/**
	 * Authenticate a user by HTTP-auth
	 *
	 * @return	mixed	user array or false
	 */
	function getUser() {
		if (TYPO3_REST == 1) {
			$authinfo = new rfc2617();
			$authinfo->authenticate();

			/* no user, no search */
			if (!$authinfo->infos['username'])
				return false;

			/* look up the user by the username */
			$found = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						$this->db_user['table'],
						'username = \'' . $authinfo->infos['username'] . '\''
					);

			if ($found) {
				$data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($found);
				$GLOBALS['TYPO3_DB']->sql_free_result($found);
				$data['authinfo'] = $authinfo;

				return $data;
			}
		}

		return false;
	}

	/**
	 * Authenticate a user
	 *
	 * @param	array 	Data of user.
	 * @return	boolean
	 */
	function authUser($user) {
		if (TYPO3_REST == 1)
			return ($user['authinfo']->report == RFC2617_AUTHENTICATED ? 200 : false);
		return 100;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rest/sv2/class.tx_rest_sv2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rest/sv2/class.tx_rest_sv2.php']);
}

?>