<?php

namespace Guesl\CardConnect\CoPilot\Models;

class OwnerSiteUser extends Model
{
    /**
     * Required: yes
     * Size: 300
     *
     * @var string
     */
    protected $firstName;

    /**
     * Required: yes
     * Size: 300
     *
     * @var string
     */
    protected $lastName;

    /**
     * Required: yes
     * Size: 300
     *
     * @var string
     */
    protected $email;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return OwnerSiteUser
     */
    public function setFirstName(string $firstName): OwnerSiteUser
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return OwnerSiteUser
     */
    public function setLastName(string $lastName): OwnerSiteUser
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return OwnerSiteUser
     */
    public function setEmail(string $email): OwnerSiteUser
    {
        $this->email = $email;
        return $this;
    }
}
