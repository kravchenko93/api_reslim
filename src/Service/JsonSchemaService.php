<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\JsonSchemaValidationException;
use App\Exception\SystemException;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;

class JsonSchemaService
{
    /**
     * @param string $schemaName
     *
     * @return array
     */
    public function getSchema(string $schemaName): array
    {
        $file = __DIR__ . "/../Resources/schema/{$schemaName}.json";
        if (!file_exists($file)) {
            throw new SystemException('JSON schema does not exist', ['schemaName' => $schemaName]);
        }

        return json_decode(file_get_contents($file), true);
    }

    /**
     * @param string $schemaName
     *
     * @return string
     */
    public function getSchemaString(string $schemaName): string
    {
        $file = __DIR__ . "/../Resources/schema/{$schemaName}.json";
        if (!file_exists($file)) {
            throw new SystemException('JSON schema does not exist', ['schemaName' => $schemaName]);
        }

        return file_get_contents($file);
    }

    /**
     * @param string $schemaName
     * @param array $data
     *
     * @throws JsonSchemaValidationException
     */
    public function validateData(string $schemaName, array $data): void
    {
        $schemaAsArray = $this->getSchema($schemaName);
        $schemaAsObject = json_decode(json_encode($schemaAsArray), false);
        $schema = new Schema($schemaAsObject);

        $dataAsObject = json_decode(json_encode($data));

        $validator = new Validator();
        $result = $validator->schemaValidation($dataAsObject, $schema);

        if (!$result->isValid()) {
            throw new JsonSchemaValidationException($result->getErrors());
        }
    }

    /**
     * Возвращает ошибки валидации JSON схемы.
     *
     * Обрабатывает ошибки только двух уровней вложенности.
     * Для обработки большой вложенности нужно делать рекурсию, пока не стал усложнять,
     * т.к. пока схемы есть только двух уровней сложенности.
     *
     * @param JsonSchemaValidationException $exception
     *
     * @return array
     */
    public function buildErrorDetails(JsonSchemaValidationException $exception): array
    {
        $validationErrors = $exception->getValidationErrors();

        if (count($validationErrors) === 0) {
            return [];
        }

        $firstValidationError = $this->getNestedError($validationErrors[0]);

        return [
            'dataPointer' => $firstValidationError->dataPointer(),
            'keyword' => $firstValidationError->keyword(),
            'keywordArgs' => $firstValidationError->keywordArgs(),
        ];
    }

    /**
     * @param ValidationError $validationError
     *
     * @return ValidationError
     */
    private function getNestedError(ValidationError $validationError): ValidationError
    {
        if (count($validationError->subErrors()) === 0) {
            return $validationError;
        }

        return $this->getNestedError($validationError->subErrors()[0]);
    }
}
