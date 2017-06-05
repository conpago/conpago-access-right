<?php
/**
 * Created by PhpStorm.
 * User: Bartosz Gołek
 * Date: 09.11.13
 * Time: 15:30
 */

namespace Conpago\AccessRight;

use Conpago\AccessRight\Contract\Exceptions\CurrentLoggedUserNotImplementAccessRightRequesterException;
use Conpago\AccessRight\Contract\IAccessRightChecker;
use Conpago\AccessRight\Contract\IAccessRightRequester;
use Conpago\AccessRight\Contract\IRole;
use Conpago\AccessRight\Contract\IRolesConfig;
use Conpago\Auth\Contract\ISessionManager;

class AccessRightChecker implements IAccessRightChecker
{
    const USER_IS_NOT_A_CORRECT_ACCESS_RIGHT_REQUESTER = 'User is not a correct AccessRight requester.';
    const ALL_ACCESS_RIGHTS = '*';

    /** @var ISessionManager */
    private $sessionManager;

    /** @var IRolesConfig */
    private $rolesConfig;

    /**
     * @param ISessionManager $sessionManager
     * @param IRolesConfig $rolesConfig
     */
    public function __construct(
        ISessionManager $sessionManager,
        IRolesConfig $rolesConfig
    ) {
    
        $this->sessionManager = $sessionManager;
        $this->rolesConfig = $rolesConfig;
    }

    /**
     * @param string $accessRight
     *
     * @return bool
     * @throws \Exception
     */
    public function check(string $accessRight): bool
    {
        if (!$this->sessionManager->isLoggedIn()) {
            return false;
        }

        $accessRightRequester = $this->sessionManager->getCurrentUser();
        if (!$accessRightRequester instanceof IAccessRightRequester) {
            throw new CurrentLoggedUserNotImplementAccessRightRequesterException(
                self::USER_IS_NOT_A_CORRECT_ACCESS_RIGHT_REQUESTER
            );
        }

        $userRoles = $accessRightRequester->getRoles();
        foreach ($userRoles as $roleName) {
            /** @var IRole[] $roles */
            $roles = $this->rolesConfig->getRoles();

            if ($this->roleExists($roles, $roleName)) {
                return false;
            }

            $role = $roles[$roleName];

            $roleAccessRights = $role->getAccessRights();
            if ($this->hasAccess($accessRight, $roleAccessRights, $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $accessRight
     * @param $roleAccessRights
     * @param IRole $role
     * @return bool
     */
    public function hasAccess($accessRight, $roleAccessRights, IRole $role): bool
    {
        return in_array(self::ALL_ACCESS_RIGHTS, $roleAccessRights) || in_array($accessRight, $role->getAccessRights());
    }

    /**
     * @param $roles
     * @param $roleName
     * @return bool
     */
    public function roleExists($roles, $roleName): bool
    {
        return $roles == null || !array_key_exists($roleName, $roles);
    }
}
