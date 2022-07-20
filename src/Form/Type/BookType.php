<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Service\Manager\AuthorManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookType extends AbstractEntityType
{

    protected AuthorManager $authorManager;

    public function __construct(AuthorManager $authorManager)
    {
        $this->authorManager = $authorManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'constraints' => new NotBlank()
                ]
            )
            ->add(
                'description',
                TextareaType::class
            )
            ->add(
                'authors',
                ChoiceType::class,
                [
                    'multiple' => true,
                    'choices'  => $this->resolveAuthorsChoices(),
                ]
            )
            ->add(
                'quantity',
                NumberType::class,
                [
                    'empty_data'  => 1,
                    'scale'       => 0,
                    'constraints' => [
                        new GreaterThanOrEqual(['value' => 0])
                    ]
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'constraints' => new NotBlank()
                ]
            );
    }

    protected function resolveAuthorsChoices(): array
    {
        $choices = [];
        $authors = $this->authorManager->all();
        foreach ($authors as $author) {
            $choices[$author['first_name'] . ' ' . $author['last_name']] = (int)$author['id'];
        }

        return $choices;
    }
}
