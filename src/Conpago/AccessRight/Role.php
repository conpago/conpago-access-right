<?php
    /**
     * Created by PhpStorm.
     * User: Bartosz Gołek
     * Date: 29.11.13
     * Time: 00:48
     */

    namespace Conpago\AccessRight;

    use Conpago\AccessRight\Contract\IRole;

class Role implements IRole
{

    /** @var string */
    private $name;

    /** @var string[] */
    private $accessRights;

    /**
     * @param string $name
     * @param string[] $accessRights
     */
    public function __construct(string $name, array $accessRights)
    {
        $this->name = $name;
        $this->accessRights = $accessRights;
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getAccessRights(): array
    {
        return $this->accessRights;
    }
}
