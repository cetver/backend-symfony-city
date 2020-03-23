<?php

declare(strict_types=1);

namespace App\EntitySubscriber;

/**
 * The "GenerateIdFromNameInterface" interface.
 */
interface GenerateIdFromNameInterface
{
    public function setId($id);

    public function getName();
}
