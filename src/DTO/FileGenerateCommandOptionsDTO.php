<?php

declare(strict_types=1);

namespace App\DTO;

use App\Constraint as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The "FileGenerateCommandOptionsDTO" class.
 */
final class FileGenerateCommandOptionsDTO
{
    /**
     * @Assert\NotBlank
     * @AppAssert\WritableDirName
     *
     * @var mixed
     */
    private $filepath;
    /**
     * @Assert\NotBlank
     * @Assert\Range(
     *     min=1
     * )
     *
     * @var mixed
     */
    private $lines;

    public function __construct($filepath, $lines)
    {
        $this->filepath = $filepath;
        $this->lines = $lines;
    }

    /**
     * @return mixed
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * @return mixed
     */
    public function getLines()
    {
        return (int) $this->lines;
    }
}
