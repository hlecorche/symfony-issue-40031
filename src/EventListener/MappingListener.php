<?php

declare(strict_types=1);

/*
 * This file is part of the MyBundle package.
 *
 * (c) E-commit <contact@e-commit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Issue\MyBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Issue\MyBundle\Entity\EntityInVendor;
use Issue\MyBundle\Entity\UserInterfaceInVendor;

class MappingListener
{
    protected $entityInVendorMappingTodo = true;

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->isMappedSuperclass) {
            return;
        }

        $className = $metadata->getName();

        if ($this->entityInVendorMappingTodo && is_subclass_of($className, UserInterfaceInVendor::class)) {
            //User class if loaded before EntityInVendor class
            //$this->entityInVendorMappingTodo = false; //New fix
            $entityInVendorMetadata = $eventArgs->getEntityManager()->getMetadataFactory()->getMetadataFor(EntityInVendor::class);
            $this->mappEntityInVendor($entityInVendorMetadata, $metadata);

        } elseif ($this->entityInVendorMappingTodo && EntityInVendor::class === $className) {
            //EntityInVendor class if loaded before User class
            //$this->entityInVendorMappingTodo = false; //New fix
            $userMetadata = $eventArgs->getEntityManager()->getMetadataFactory()->getMetadataFor(UserInterfaceInVendor::class);
            $this->mappEntityInVendor($metadata, $userMetadata);
        }
    }

    protected function mappEntityInVendor(ClassMetadataInfo $entityInVendorMetadata, ClassMetadataInfo $userMetadata): void
    {
        /*
         * Without this override, the Doctrine Schema is invalid : ""Column name `id` referenced for relation from App\Entity\Post towards App\Entity\User does not exist.
         * Because :
         *      - The @JoinColumn referencedColumnName default value is "id"
         *      - And the User primary column column name is "user_id", not "id".
         */

        /*
         * This method is inspired by https://stackoverflow.com/questions/37368495/relationships-between-entities-using-resolve-target-entity-in-symfony2-when-the
         *
         * but with auto detection of user primary column column name (but less "clean"...)
         */

        $this->entityInVendorMappingTodo = false;

        $entityInVendorMetadata->setAssociationOverride(
            'user',
            [
                'targetEntity' => $userMetadata->getName(),
                'fieldName' => 'user',
                'id' => true,
                'joinColumns' => [[
                    'name' => 'user_id',
                    'referencedColumnName' => $userMetadata->getSingleIdentifierColumnName(),
                    'onDelete' => 'CASCADE',
                ]],
            ]
        );
    }
}
