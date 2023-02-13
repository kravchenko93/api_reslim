<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;
use Opis\JsonSchema\ValidationError;

class JsonSchemaValidationException extends Exception
{
    /**
     * @var ValidationError[]
     */
    private $validationErrors;

    /**
     * @param ValidationError[] $validationErrors
     */
    public function __construct(array $validationErrors)
    {
        parent::__construct();
        $this->validationErrors = $validationErrors;
    }

    /**
     * @return ValidationError[]
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}