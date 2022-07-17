<?php
declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthorType extends AbstractEntityType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, ['constraints' => new NotBlank()])
            ->add('last_name', TextType::class, ['constraints' => new NotBlank()]);
    }

}
