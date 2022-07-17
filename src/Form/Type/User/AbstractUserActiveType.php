<?php
declare(strict_types=1);

namespace App\Form\Type\User;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AbstractUserActiveType extends AbstractUserType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'active',
                ChoiceType::class,
                [
                    'label'   => 'Is Active?',
                    'choices' =>
                        [
                            'Active'   => true,
                            'InActive' => false,
                        ],

                ]
            );
    }

}
