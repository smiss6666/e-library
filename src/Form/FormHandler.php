<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormHandler
{

    protected FormFactoryInterface $formFactory;
    protected FormErrors           $formErrors;

    public function __construct(FormFactoryInterface $formFactory, FormErrors $formErrors)
    {
        $this->formFactory = $formFactory;
        $this->formErrors  = $formErrors;
    }

    public function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function createFormBuilder($data = null, array $options = []): FormBuilderInterface
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }

    public function handleForm(FormInterface $form, Request $request, $useFormNamePrefix = false): array
    {
        // submit form
        $form->handleRequest($request);

        // pages does not contains correct form data
        // form was not submitted, submit empty form
        if (!$form->isSubmitted()) {
            $form->submit([]);
        }

        // return form's errors
        return $this->formErrors->getArray($form, $useFormNamePrefix);
    }

    public function submitForm(FormInterface $form, array $data, $useFormNamePrefix = false): array
    {
        // submit form
        $form->submit($data);

        // return form's errors
        return $this->formErrors->getArray($form, $useFormNamePrefix);
    }
}
