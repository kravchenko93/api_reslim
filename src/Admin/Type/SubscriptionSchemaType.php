<?php
declare(strict_types=1);

namespace App\Admin\Type;

use App\Dto\SubscriptionInfoDto;
use App\Enum\JsonSchemaNameEnum;
use App\Exception\SystemException;
use App\Enum\SubscriptionType;
use App\Service\JsonSchemaService;
use Cyve\JsonSchemaFormBundle\Form\Helper\FormHelper;
use Cyve\JsonSchemaFormBundle\Form\Type\SchemaType as CyveSchemaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionSchemaType extends CyveSchemaType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('data_class', SubscriptionInfoDto::class)
        ;
    }

    /**
     * @var JsonSchemaService $jsonSchemaService
     */
    private $jsonSchemaService;

    public function __construct(
        JsonSchemaService $jsonSchemaService
    ) {
        $this->jsonSchemaService = $jsonSchemaService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /**
             * @var SubscriptionInfoDto $subscriptionInfoDto
             */
            $subscriptionInfoDto = $event->getData();
            if (!$subscriptionInfoDto instanceof SubscriptionInfoDto) {
                throw new SystemException('SubscriptionSchemaType used with no SubscriptionInfoDto');
            }
            $type = $subscriptionInfoDto->getType();

            if (SubscriptionType::TRIAL === $type) {
                $rootSchema = json_decode($this->jsonSchemaService->getSchemaString(JsonSchemaNameEnum::SUBSCRIPTIONS_TRIAL_FORM));
            } else {
                $rootSchema = json_decode($this->jsonSchemaService->getSchemaString(JsonSchemaNameEnum::SUBSCRIPTIONS_FORM));
            }

            foreach ($rootSchema->properties as $name => $schema) {
                if (!$formType = FormHelper::resolveFormType($schema)) {
                    continue;
                }

                $formOptions = FormHelper::resolveFormOptions($schema) + ['required' => in_array($name, $rootSchema->required ?? []), 'mapped' => false];
                $form->add($name, $formType, $formOptions);
            }
        });

//        parent::buildForm($builder, $options);
    }
}