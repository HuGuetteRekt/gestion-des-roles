<?php

namespace App\Form;

use App\Entity\Ecole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('adresse')
            ->add('email', EmailType::class)
            ->add('telephone')
            ->add('ville')
            ->add('type')
            ->add('agregation', CheckboxType::class, [
                'label' => 'Cochez cette case si l\'école est agrégé : ',
                'required' => false,
            ])
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ecole::class,
        ]);
    }
}
