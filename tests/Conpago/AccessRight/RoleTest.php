<?php
/**
 * Created by PhpStorm.
 * User: bgolek
 * Date: 2014-11-10
 * Time: 08:34
 */

namespace Conpago\AccessRight;

use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    const ROLE_NAME = "name";
    private $roleAccessRights = array("a");

    public function testGetAccessRightsReturnsInitializedArray(): void
    {
        $role = new Role(self::ROLE_NAME, array("a"));
        $this->assertEquals($role->getAccessRights(), array("a"));
    }

    public function testGetGetRoleNameReturnsInitializedName(): void
    {
        $role = new Role(self::ROLE_NAME, $this->roleAccessRights);
        $this->assertEquals($role->getRoleName(), self::ROLE_NAME);
    }
}
