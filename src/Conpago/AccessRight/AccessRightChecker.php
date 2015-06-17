<?php
	/**
	 * Created by PhpStorm.
	 * User: Bartosz Gołek
	 * Date: 09.11.13
	 * Time: 15:30
	 */

	namespace Saigon\Conpago\AccessRight;

	use Saigon\Conpago\AccessRight\Contract\Exceptions\CurrentLoggedUserNotImplementAccessRightRequesterException;
	use Saigon\Conpago\AccessRight\Contract\IAccessRightChecker;
	use Saigon\Conpago\AccessRight\Contract\IAccessRightRequester;
	use Saigon\Conpago\AccessRight\Contract\IRolesConfig;
	use Saigon\Conpago\Auth\Contract\ISessionManager;

	class AccessRightChecker implements IAccessRightChecker
	{
		const USER_IS_NOT_A_CORRECT_ACCESS_RIGHT_REQUESTER = 'User is not a correct AccessRight requester.';
		const All_ACCESS_RIGHTS = '*';
		/**
		 * @var ISessionManager
		 */
		private $sessionManager;
		/**
		 * @var IRolesConfig
		 */
		private $rolesConfig;

		/**
		 * @param ISessionManager $sessionManager
		 * @param IRolesConfig $rolesConfig
		 */
		function __construct(
			ISessionManager $sessionManager,
			IRolesConfig $rolesConfig)
		{
			$this->sessionManager = $sessionManager;
			$this->rolesConfig = $rolesConfig;
		}

		/**
		 * @param string $accessRight
		 *
		 * @return bool
		 * @throws \Exception
		 */
		public function check($accessRight)
		{
			if (!$this->sessionManager->isLoggedIn())
				return false;

			$accessRightRequester = $this->sessionManager->getCurrentUser();
			if (!$accessRightRequester instanceof IAccessRightRequester)
				throw new CurrentLoggedUserNotImplementAccessRightRequesterException(self::USER_IS_NOT_A_CORRECT_ACCESS_RIGHT_REQUESTER);

			$userRoles = $accessRightRequester->getRoles();
			foreach($userRoles as $roleName)
			{
				$roles = $this->rolesConfig->getRoles();

				if ($roles == null || !array_key_exists($roleName, $roles))
					return false;

				$role = $roles[$roleName];

				$roleAccessRights = $role->getAccessRights();
				if (in_array(self::All_ACCESS_RIGHTS, $roleAccessRights) || in_array($accessRight, $role->getAccessRights()))
					return true;
			}
			return false;
		}
	}