<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\DTO\ParseCityDTO;
use App\DTO\ParseUserDTO;
use App\Transformer\StringTransformerInterface;
use function iter\toArray;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;

/**
 * The "CityDTONormalizer" class.
 */
class ParseCityDTONormalizer implements ContextAwareDenormalizerInterface
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
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return ParseCityDTO::class === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $name = $data['name'];
        $id = $this->transformer->transform($name);
        $users = toArray($this->users($data['users']));

        return new ParseCityDTO($id, $name, $data['country'], $users);
    }

    private function users(array $users)
    {
        foreach ($users as $user) {
            $name = $user['name'];
            $id = $this->transformer->transform($name);
            yield new ParseUserDTO($id, $name, $user['phone']);
        }
    }
}
