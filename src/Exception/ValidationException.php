<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\Validator\{
    ConstraintViolationListInterface, ConstraintViolationInterface
};

/**
 * Исключение для ошибок порожденными нарушениями ограничениями владиции Symfony
 */
class ValidationException extends Exception
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $constraintList;

    /**
     * @param ConstraintViolationListInterface $constraintList
     */
    public function __construct(ConstraintViolationListInterface $constraintList)
    {
        parent::__construct();

        $this->constraintList = $constraintList;
    }

    /**
     * Return detail error info about fields
     *
     * @return array
     */
    public function getFieldsDetails(): array
    {
        $fieldsDetails = [];

        /** @var ConstraintViolationInterface $constraint */
        foreach ($this->constraintList as $constraint) {
            $fieldsDetails[$constraint->getPropertyPath()][] = [
                'clientError' => $constraint->getMessage(),
            ];
        }

        return $fieldsDetails;
    }

    /**
     * Return detail error info about fields
     *
     * @return array
     */
    public function getLogFieldsDetails(): array
    {
        $fieldsDetails = [];

        /** @var ConstraintViolationInterface $constraint */
        foreach ($this->constraintList as $constraint) {
            $fieldsDetails[$constraint->getPropertyPath()][] = [
                'message' => $constraint->getMessage(),
                'invalidValue' => $constraint->getInvalidValue(),
                'messageTemplate' => $constraint->getMessageTemplate(),
                'parameters' => $constraint->getParameters(),
                'root' => $constraint->getRoot(),
                'propertyPath' => $constraint->getPropertyPath(),
            ];
        }

        return $fieldsDetails;
    }
}