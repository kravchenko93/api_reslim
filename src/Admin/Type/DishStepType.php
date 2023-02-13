<?php
declare(strict_types=1);

namespace App\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class DishStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', IntegerType::class, ['required' => true])
            ->add('text', TextareaType::class, ['required' => true])
            ->add('imageFile', VichImageType::class, ['allow_delete' => false, 'required' => false])
            ->add('dish',null, ['attr' => ['style' => 'display:none;'], 'label_attr' => ['style' => 'display:none;']])
            ->setByReference(false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\DishStep'
        ));
    }

}