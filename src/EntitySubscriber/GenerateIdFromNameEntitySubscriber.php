<?php

declare(strict_types=1);

namespace App\EntitySubscriber;

use App\Transformer\StringTransformerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * The "GenerateIdFromNameEntitySubscriber" class.
 */
class GenerateIdFromNameEntitySubscriber implements EventSubscriber
{
    /**
     * @var StringTransformerInterface
     */
    private StringTransformerInterface $transformer;

    public function __construct(StringTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof GenerateIdFromNameInterface) {
            $id = $this->transformer->transform($entity->getName());
            $entity->setId($id);
        }
    }
}
