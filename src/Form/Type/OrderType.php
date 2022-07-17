<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Reading;
use App\Service\Manager\BookManager;
use App\Service\Manager\UserManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractEntityType
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bookChoices        = ['' => ''] + $this->bookManager->choices('title');
        $userFilter         = []; //['role' => UserInterface::ROLE_READER];
        $userChoices        = ['' => ''] + $this->userManager->choices(['first_name', 'last_name'], $userFilter);
        $readingTypeChoices = ['' => ''] + array_flip(Reading::READING_TYPES);

        $builder
            ->add(
                'book_id',
                ChoiceType::class,
                [
                    'label'       => 'Book',
                    'choices'     => $bookChoices,
                    'constraints' => [new NotBlank()],
                ]
            )
            ->add(
                'quantity',
                NumberType::class,
                [
                    'empty_data'  => 1,
                    'scale'       => 0,
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThanOrEqual(['value' => 1])
                    ]
                ]
            )
            ->add(
                'user_id',
                ChoiceType::class,
                [
                    'label'       => 'Reader',
                    'choices'     => $userChoices,
                    'constraints' => [new NotBlank()],
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
