<?php

namespace Plugin\KomojuPayment43\Form\Type\Admin;

use Plugin\KomojuPayment43\Entity\Config;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('secret_key', TextType::class, [
                'label' => 'Secret Key',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('merchant_id', TextType::class, [
                'label' => 'Merchant ID',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('webhook_secret', TextType::class, [
                'label' => 'Webhook Secret',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }
}
