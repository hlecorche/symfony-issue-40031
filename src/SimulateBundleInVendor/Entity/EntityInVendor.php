<?php

/*
 * This file is part of the symfony-issue-40031 package.
 *
 * (c) E-commit <contact@e-commit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\SimulateBundleInVendor\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="entity_in_vendor")
 */
class EntityInVendor
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\SimulateBundleInVendor\Entity\UserInterfaceInVendor")
     * @var UserInterfaceInVendor
     */
    protected $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     * @param string
     */
    protected $otherPk;

    /**
     * @ORM\Column(type="string")
     * @param string
     */
    protected $value;

    /**
     * @return UserInterfaceInVendor
     */
    public function getUser(): UserInterfaceInVendor
    {
        return $this->user;
    }

    /**
     * @param UserInterfaceInVendor $user
     *
     * @return EntityInVendor
     */
    public function setUser(UserInterfaceInVendor $user): EntityInVendor
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOtherPk()
    {
        return $this->otherPk;
    }

    /**
     * @param mixed $otherPk
     *
     * @return EntityInVendor
     */
    public function setOtherPk($otherPk)
    {
        $this->otherPk = $otherPk;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return EntityInVendor
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
