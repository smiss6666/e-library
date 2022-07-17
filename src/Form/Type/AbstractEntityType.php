<?php
declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractEntityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('id');
        $resolver->setAllowedTypes('id', ['int', 'null']);
        $resolver->setDefault('id', null);
    }
}
