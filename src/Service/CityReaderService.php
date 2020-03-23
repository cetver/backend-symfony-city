<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ParseCityDTO;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * The "CityReaderService" class.
 */
class CityReaderService
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return \Generator|ParseCityDTO[]
     */
    public function cities(ReadLinesService $readLinesService)
    {
        foreach ($readLinesService->lines() as $line) {
            yield $this->serializer->deserialize($line, ParseCityDTO::class, JsonEncoder::FORMAT);
        }
    }
}
