<?php
/**
 * Created by PhpStorm.
 * User: Bartosz Gołek
 * Date: 2014-11-10
 * Time: 08:38
 */

namespace Conpago\AccessRight;

use Conpago\AccessRight\Contract\IAccessRightRequester;
use Conpago\AccessRight\Contract\IRole;
use Conpago\AccessRight\Contract\IRolesConfig;
use Conpago\Auth\Contract\IAuthModel;
use Conpago\Auth\Contract\ISessionManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class AccessRightCheckerTest extends TestCase
{
    const ACCESS_RIGHT_NAME = 'accessRightName';

    /** @var AccessRightChecker */
    private $sut;

    /** @var  IRolesConfig | MockObject */
    private $rolesConfig;

    /** @var ISessionManager | MockObject */
    private $sessionManager;

    public function setUp(): void
    {
        $this->sessionManager = $this->createMock(ISessionManager::class);
        $this->rolesConfig = $this->createMock(IRolesConfig::class);

        $this->sut = new AccessRightChecker($this->sessionManager, $this->rolesConfig);
    }
    
    /** */
    public function testCheckWillReturnFalseIfNotLogged(): void
    {
        $this->sessionManager->method('isLoggedIn')->willReturn(false);

        $this->assertFalse($this->sut->check(self::ACCESS_RIGHT_NAME));
    }

    /**
     * @expectedException \Conpago\AccessRight\Contract\Exceptions\CurrentLoggedUserNotImplementAccessRightRequesterException
     */
    public function testCheckThrowsExceptionIfLoggedUserNotImplementRequester(): void
    {
        $this->setAsLogged(
            $this->createMock(IAuthModel::class)
        );

        $this->sut->check(self::ACCESS_RIGHT_NAME);
    }

    public function testCheckWillReturnTrueWhenUserHasRoleWithAccessRight(): void
    {
        $this->setAsLogged(
            $this->initializeAccessRightRequester(["role1"])
        );

        $role = $this->createMock(IRole::class);
        $role->method('getAccessRights')->willReturn([self::ACCESS_RIGHT_NAME]);

        $this->rolesConfig->method('getRoles')->willReturn(["role1" => $role]);

        $this->assertTrue($this->sut->check(self::ACCESS_RIGHT_NAME));
    }

    public function testCheckWillReturnTrueWhenUserHasRoleWithAllAccessRights(): void
    {
        $this->setAsLogged(
            $this->initializeAccessRightRequester(["role1"])
        );

        $role = $this->createMock(IRole::class);
        $role->method('getAccessRights')->willReturn([AccessRightChecker::ALL_ACCESS_RIGHTS]);

        $this->rolesConfig->method('getRoles')->willReturn(["role1" => $role]);

        $this->assertTrue($this->sut->check(self::ACCESS_RIGHT_NAME));
    }

    public function testCheckWillReturnFalseWhenUserHasRoleWithoutAccessRight(): void
    {
        $this->setAsLogged(
            $this->initializeAccessRightRequester(["role1"])
        );

        $role = $this->createMock(IRole::class);
        $role->method('getAccessRights')->willReturn([]);

        $this->rolesConfig->method('getRoles')->willReturn(["role1" => $role]);

        $this->assertFalse($this->sut->check(self::ACCESS_RIGHT_NAME));
    }

    public function testCheckWillReturnFalseWhenUserHasNoRole(): void
    {
        $this->setAsLogged(
            $this->initializeAccessRightRequester(["role1"])
        );

        $this->rolesConfig->method('getRoles')->willReturn([]);

        $this->assertFalse($this->sut->check(self::ACCESS_RIGHT_NAME));
    }

    /**
     * @param $authModel
     */
    private function setAsLogged($authModel): void
    {
        $this->sessionManager->method('isLoggedIn')->willReturn(true);
        $this->sessionManager->method('getCurrentUser')->willReturn($authModel);
    }

    /**
     * @param array $roles
     *
     * @return IAccessRightRequester
     */
    private function initializeAccessRightRequester(array $roles)
    {
        $authModel = $this->createMock(IAccessRightRequester::class);
        $authModel->method("getRoles")->willReturn($roles);
        return $authModel;
    }
}