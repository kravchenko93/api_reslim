<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param object $value
     * @param array|null $groups
     *
     * @throws ValidationException
     */
    public function validateDto($value, ?array $groups = null): void
    {
        $constraintList = $this->validator->validate($value, null, $groups);
        if (count($constraintList)) {
            throw new ValidationException($constraintList);
        }
    }
}
