<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Service\Manager\BookManager;
use App\Service\Manager\UserManager;
use Carbon\Carbon;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReadingProlongType extends AbstractEntityType
{

    protected BookManager $bookManager;
    protected UserManager $userManager;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $today = Carbon::today()->startOfDay()->setHour(8);
        $builder
            ->add(
                'prolong_at',
                DateType::class,
                [
                    'widget'      => 'single_text',
                    'constraints' => [
                        new NotBlank(),
                        new GreaterThanOrEqual(['value' => $today])
                    ],
                ]
            );
    }
}
