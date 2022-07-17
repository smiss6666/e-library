<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Reading;
use App\Service\Manager\BookManager;
use App\Service\Manager\UserManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderBookType extends AbstractEntityType
{

    protected BookManager $bookManager;
    protected UserManager $userManager;

    public function __construct(
        BookManager $bookManager,
        UserManager $userManager
    ) {
        $this->bookManager = $bookManager;
        $this->userManager = $userManager;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired(['book_id', 'user_id']);
        $resolver->setAllowedTypes('book_id', ['int']);
        $resolver->setAllowedTypes('user_id', ['int']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $readingTypeChoices = ['' => ''] + array_flip(Reading::READING_TYPES);

        $builder
            ->add(
                'book_id',
                HiddenType::class,
                [
                    'data' => $options['book_id'],
                ]
            )
            ->add(
                'user_id',
                HiddenType::class,
                [
                    'data' => $options['user_id'],
                ]
            )
            ->add(
                'quantity',
                NumberType::class,
                [
                    'empty_data'        => 1,
                    'scale'       => 0,
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThanOrEqual(['value' => 1])
                    ]
                ]
            )
            ->add(
                'reading_type',
                ChoiceType::class,
                [
                    'choices'     => $readingTypeChoices,
                    'constraints' => [new NotBlank()],
                ]
            )
            ->add(
                'start_at',
                DateType::class,
                [
                    'widget'      => 'single_text',
                    'constraints' => [new NotBlank()],
                ]
            )
            ->add(
                'end_at',
                DateType::class,
                [
                    'widget'      => 'single_text',
                    'constraints' => [new NotBlank()],
                ]
            );
    }
}
