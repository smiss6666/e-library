<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

class FormErrors
{

    public function getArray(FormInterface $form, bool $useFormNamePrefix = false): array
    {
        return $this->getErrors($form, $useFormNamePrefix);
    }

    protected function getErrors($form, $useFormNamePrefix = false): array
    {
        // form errors
        $errors = array();

        if ($form instanceof FormInterface) {

            // get global form erros
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }

            // get child errors
            foreach ($form->all() as $child) {

                /** @var Form $child */
                if ($child->isSubmitted() && $child->isValid()) {
                    continue;
                }

                /** @var string $childErrors */
                if ($childErrors = $this->getErrors($child, $useFormNamePrefix)) {
                    $key          = $useFormNamePrefix ? $form->getName() . '_' . $child->getName() : $child->getName();
                    $errors[$key] = $childErrors;
                }
            }
        }

        // return errors
        return $errors;
    }
}
