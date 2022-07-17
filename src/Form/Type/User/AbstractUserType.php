<?php
declare(strict_types=1);

namespace App\Form\Type\User;

use App\Form\Type\AbstractEntityType;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

abstract class AbstractUserType extends AbstractEntityType
{
    protected UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('id');
        $resolver->setAllowedTypes('id', ['int', 'null']);
        $resolver->setDefault('id', null);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isEdit = !empty($options['id']) && $options['id'];

        $builder
            ->add('first_name', TextType::class, ['constraints' => new NotBlank()])
            ->add('last_name', TextType::class, ['constraints' => new NotBlank()])
            ->add(
                'username',
                TextType::class,
                [
                    'constraints' => [new NotBlank(), new Callback([$this, 'validateFormFieldUsername'])],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => [new NotBlank(), new Email(), new Callback([$this, 'validateFormFieldEmail'])],
                ]
            )
            ->add(
                'password',
                TextType::class,
                [
                    'constraints' => !$isEdit ? [new NotBlank()] : [],
                ]
            );
    }

    protected function isTheSameUser($value, ExecutionContextInterface $context): bool
    {
        /** @var FormInterface $form */
        $form    = $context->getRoot();
        $id      = $form->getConfig()->getOption('id');
        $subject = $id ? $this->userRepository->find($id) : null;
        $another = $this->userRepository->loadUserByUsername($value);

        return !(
            $subject !== null &&
            $another !== null &&
            $subject->getId() !== $another->getId()
        );
    }

    public function validateFormFieldUsername($value, ExecutionContextInterface $context): void
    {
        if ($value && !$this->isTheSameUser($value, $context)) {
            $context
                ->buildViolation('USERNAME_TAKEN')
                ->setParameter('%USERNAME%', $value)
                ->atPath('username')->addViolation();
        }
    }

    public function validateFormFieldEmail($value, ExecutionContextInterface $context): void
    {
        if ($value && !$this->isTheSameUser($value, $context)) {
            $context
                ->buildViolation('EMAIL_TAKEN')
                ->setParameter('%EMAIL%', $value)
                ->atPath('email')->addViolation();
        }
    }
}
