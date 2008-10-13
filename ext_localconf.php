<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

if (TYPO3_REST == 1) {
	$TYPO3_CONF_VARS['SVCONF']['auth']['setup']['BE_alwaysFetchUser'] = '1';
	$TYPO3_CONF_VARS['SVCONF']['auth']['setup']['BE_alwaysAuthUser'] = '1';

	t3lib_extMgm::addService($_EXTKEY,  'auth' /* sv type */,  'tx_rest_sv1' /* sv key */,
			array(

				'title' => 'BE authentification though HTTP-auth',
				'description' => 'REST-specific',

				'subtype' => 'getUserBE,authUserBE',

				'available' => TRUE,
				'priority' => 50,
				'quality' => 50,

				'os' => '',
				'exec' => '',

				'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sv1/class.tx_rest_sv1.php',
				'className' => 'tx_rest_sv1',
			)
		);

	$TYPO3_CONF_VARS['SVCONF']['auth']['setup']['FE_alwaysFetchUser'] = '1';
	$TYPO3_CONF_VARS['SVCONF']['auth']['setup']['FE_alwaysAuthUser'] = '1';

	t3lib_extMgm::addService($_EXTKEY,  'auth' /* sv type */,  'tx_rest_sv2' /* sv key */,
			array(

				'title' => 'FE authentification though HTTP-auth',
				'description' => 'REST-specific',

				'subtype' => 'getUserFE,authUserFE',

				'available' => TRUE,
				'priority' => 50,
				'quality' => 50,

				'os' => '',
				'exec' => '',

				'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sv2/class.tx_rest_sv2.php',
				'className' => 'tx_rest_sv2',
			)
		);

	$TYPO3_CONF_VARS['FE']['dontSetCookie'] = 1;
}
?>