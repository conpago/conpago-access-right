<?php
	use Saigon\Conpago\AccessRight\AccessRightChecker;

	/**
	 * Created by PhpStorm.
	 * User: bgolek
	 * Date: 2014-11-10
	 * Time: 08:38
	 */
	class AccessRightCheckerTest extends PHPUnit_Framework_TestCase
	{
		function test()
		{
			$sessionManager = $this->getMock('Saigon\Conpago\Auth\Contract\ISessionManager');
			$rolesConfig = $this->getMock();
			$accessRightChecker = new AccessRightChecker($sessionManager, $rolesConfig);
		}
	}
 