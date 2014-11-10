<?php
	use Saigon\Conpago\AccessRight\Role;

	/**
	 * Created by PhpStorm.
	 * User: bgolek
	 * Date: 2014-11-10
	 * Time: 08:34
	 */
	class RoleTest extends PHPUnit_Framework_TestCase
	{
		const ROLE_NAME = "name";
		private $roleAccessRights = array("a");

		function testGetAccessRightsReturnsInitializedArray()
		{
			$role = new Role(self::ROLE_NAME, array("a"));
			$this->assertEquals($role->getAccessRights(), array("a"));
		}

		function testGetGetRoleNameReturnsInitializedName()
		{
			$role = new Role(self::ROLE_NAME, $this->roleAccessRights);
			$this->assertEquals($role->getRoleName(), self::ROLE_NAME);
		}
	}
 