<?php

namespace App\Helper;

use Symfony\Component\Form\FormInterface;

class FormHelper
{
    /**
     * Extract form errors as an associative array.
     */
    public static function getErrors(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true) as $error) {
            $field = $error->getOrigin()->getName();
            $errors[$field] = $error->getMessage();
        }

        return $errors;
    }
}
