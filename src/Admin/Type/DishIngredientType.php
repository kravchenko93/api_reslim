<?php
declare(strict_types=1);

namespace App\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DishIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', IntegerType::class, ['required' => true])
            ->add('quantity', TextType::class, ['required' => true])
            ->add('dish',null, ['attr' => ['style' => 'display:none;'], 'label_attr' => ['style' => 'display:none;']])
            ->add('ingredient', null, ['required' => true])->setByReference(false);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\DishIngredient'
        ));
    }

}