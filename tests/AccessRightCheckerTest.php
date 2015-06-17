<?php
	use Conpago\AccessRight\AccessRightChecker;
	use Conpago\AccessRight\Contract\IAccessRightRequester;

	/**
	 * Created by PhpStorm.
	 * User: bgolek
	 * Date: 2014-11-10
	 * Time: 08:38
	 */
	class AccessRightCheckerTest extends PHPUnit_Framework_TestCase
	{
		const ACCESS_RIGHT_NAME = 'accessRightName';

		function testCheckWillReturnFalseIfNotLogged()
		{
			$sessionManager = $this->getMock('Conpago\Auth\Contract\ISessionManager');
			$sessionManager->expects($this->any())->method('isLoggedIn')->willReturn(false);

			$rolesConfig = $this->getMock('Conpago\AccessRight\Contract\IRolesConfig');


			$accessRightChecker = new AccessRightChecker($sessionManager, $rolesConfig);

			$this->assertFalse($accessRightChecker->check(self::ACCESS_RIGHT_NAME));
		}

		/**
		 * @expectedException \Conpago\AccessRight\Contract\Exceptions\CurrentLoggedUserNotImplementAccessRightRequesterException
		 */
		function testCheckThrowsExceptionIfLoggedUserNotImplementRequester()
		{
			$sessionManager = $this->getMock('Conpago\Auth\Contract\ISessionManager');
			$sessionManager->expects($this->any())->method('isLoggedIn')->willReturn(true);
			$sessionManager->expects($this->any())->method('getCurrentUser')->willReturn(new TestBadFakeUser());
			$rolesConfig = $this->getMock('Conpago\AccessRight\Contract\IRolesConfig');

			$accessRightChecker = new AccessRightChecker($sessionManager, $rolesConfig);

			$accessRightChecker->check(self::ACCESS_RIGHT_NAME);
		}

		function testCheckWillReturnTrueWhenUserHasRoleWithAccessRight()
		{
			$sessionManager = $this->getMock('Conpago\Auth\Contract\ISessionManager');
			$sessionManager->expects($this->any())->method('isLoggedIn')->willReturn(true);
			$sessionManager->expects($this->any())->method('getCurrentUser')->willReturn(new TestCorrectFakeUser());
			$rolesConfig = $this->getMock('Conpago\AccessRight\Contract\IRolesConfig');

			$role = $this->getMock('Conpago\AccessRight\Contract\IRole');
			$role->expects($this->any())->method('getAccessRights')->willReturn(array(self::ACCESS_RIGHT_NAME));

			$rolesConfig->expects($this->any())->method('getRoles')->willReturn(array("role1" => $role));

			$accessRightChecker = new AccessRightChecker($sessionManager, $rolesConfig);

			$this->assertTrue($accessRightChecker->check(self::ACCESS_RIGHT_NAME));
		}

		function testCheckWillReturnTrueWhenUserHasRoleWithAllAccessRights()
		{
			$sessionManager = $this->getMock('Conpago\Auth\Contract\ISessionManager');
			$sessionManager->expects($this->any())->method('isLoggedIn')->willReturn(true);
			$sessionManager->expects($this->any())->method('getCurrentUser')->willReturn(new TestCorrectFakeUser());
			$rolesConfig = $this->getMock('Conpago\AccessRight\Contract\IRolesConfig');

			$role = $this->getMock('Conpago\AccessRight\Contract\IRole');
			$role->expects($this->any())->method('getAccessRights')->willReturn(array(AccessRightChecker::All_ACCESS_RIGHTS));

			$rolesConfig->expects($this->any())->method('getRoles')->willReturn(array("role1" => $role));

			$accessRightChecker = new AccessRightChecker($sessionManager, $rolesConfig);

			$this->assertTrue($accessRightChecker->check(self::ACCESS_RIGHT_NAME));
		}

		function testCheckWillReturnFalseWhenUserHasRoleWithoutAccessRight()
		{
			$sessionManager = $this->getMock('Conpago\Auth\Contract\ISessionManager');
			$sessionManager->expects($this->any())->method('isLoggedIn')->willReturn(true);
			$sessionManager->expects($this->any())->method('getCurrentUser')->willReturn(new TestCorrectFakeUser());
			$rolesConfig = $this->getMock('Conpago\AccessRight\Contract\IRolesConfig');

			$role = $this->getMock('Conpago\AccessRight\Contract\IRole');
			$role->expects($this->any())->method('getAccessRights')->willReturn(array());

			$rolesConfig->expects($this->any())->method('getRoles')->willReturn(array("role1" => $role));

			$accessRightChecker = new AccessRightChecker($sessionManager, $rolesConfig);

			$this->assertFalse($accessRightChecker->check(self::ACCESS_RIGHT_NAME));
		}

		function testCheckWillReturnFalseWhenUserHasNoRole()
		{
			$sessionManager = $this->getMock('Conpago\Auth\Contract\ISessionManager');
			$sessionManager->expects($this->any())->method('isLoggedIn')->willReturn(true);
			$sessionManager->expects($this->any())->method('getCurrentUser')->willReturn(new TestCorrectFakeUser());
			$rolesConfig = $this->getMock('Conpago\AccessRight\Contract\IRolesConfig');

			$rolesConfig->expects($this->any())->method('getRoles')->willReturn(array());

			$accessRightChecker = new AccessRightChecker($sessionManager, $rolesConfig);

			$this->assertFalse($accessRightChecker->check(self::ACCESS_RIGHT_NAME));
		}
	}

	class TestBadFakeUser
	{

	}

	class TestCorrectFakeUser implements IAccessRightRequester
	{
		/**
		 * @return string[]
		 */
		function getRoles()
		{
			return array("role1");
		}
	}
 
