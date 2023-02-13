<?php
declare(strict_types=1);

namespace App\Dto;

class SettingsResponseDto
{
    /**
     * @var string[][]
     */
    private $userQuestionsFormSchema;

    /**
     * @param object $userQuestionsFormSchema
     */
    public function __construct($userQuestionsFormSchema)
    {
        $this->userQuestionsFormSchema = $userQuestionsFormSchema;
    }

    /**
     * @return string[][]
     */
    public function getUserQuestionsFormSchema() {
        return $this->userQuestionsFormSchema;
    }
}