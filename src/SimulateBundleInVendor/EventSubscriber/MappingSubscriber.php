<?php

/*
 * This file is part of the symfony-issue-40031 package.
 *
 * (c) E-commit <contact@e-commit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\SimulateBundleInVendor\EventSubscriber;

use App\SimulateBundleInVendor\Entity\EntityInVendor;
use App\SimulateBundleInVendor\Entity\UserInterfaceInVendor;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class MappingSubscriber implements EventSubscriber
{
    protected $entityInVendorMappingTodo = true;

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->isMappedSuperclass) {
            return;
        }

        $className = $metadata->getName();

        if ($this->entityInVendorMappingTodo && is_subclass_of($className, UserInterfaceInVendor::class)) {
            $this->entityInVendorMappingTodo = false; //avoid infinite loop - Fix https://github.com/symfony/symfony/issues/40031
            //User class if loaded before EntityInVendor class
            $entityInVendorMetadata = $eventArgs->getEntityManager()->getMetadataFactory()->getMetadataFor(EntityInVendor::class);
            $this->mappEntityInVendor($entityInVendorMetadata, $metadata);

        } elseif ($this->entityInVendorMappingTodo && EntityInVendor::class === $className) {
            $this->entityInVendorMappingTodo = false; //avoid infinite loop - Fix https://github.com/symfony/symfony/issues/40031
            //EntityInVendor class if loaded before User class
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
