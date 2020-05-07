<?php

namespace GaylordP\UserBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use GaylordP\UserBundle\Annotation\CreatedAt;
use GaylordP\UserBundle\Annotation\CreatedBy;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Security\Core\Security;

class CreatableListener
{
    private $annotationReader;
    private $accessor;
    private $security;

    public function __construct(Reader $annotationReader, PropertyAccessorInterface $accessor, Security $security)
    {
        $this->annotationReader = $annotationReader;
        $this->accessor = $accessor;
        $this->security = $security;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $reflection = new \ReflectionClass($entity);

        foreach ($reflection->getProperties() as $property) {
            $this->isCreatedAtInProperty($entity, $property);
            $this->isCreatedByInProperty($entity, $property);
        }
    }

    private function isCreatedAtInProperty(object $entity, \ReflectionProperty $property): void
    {
        $annotation = $this->annotationReader->getPropertyAnnotation($property, CreatedAt::class);

        if (
            null !== $annotation
                &&
            null === $this->accessor->getValue($entity, $property->getName())
        ) {
            $this->accessor->setValue($entity, $property->getName(), new \DateTime());
        }
    }

    private function isCreatedByInProperty(object $entity, \ReflectionProperty $property): void
    {
        $annotation = $this->annotationReader->getPropertyAnnotation($property, CreatedBy::class);

        if (
            null !== $annotation
                &&
            null === $this->accessor->getValue($entity, $property->getName())
        ) {
            if (null !== $this->security->getUser()) {
                $this->accessor->setValue($entity, $property->getName(), $this->security->getUser());
            } elseif ($entity instanceof UserInterface) {
                $this->accessor->setValue($entity, $property->getName(), $entity);
            }
        }
    }
}
