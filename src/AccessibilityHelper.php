<?php

namespace Tomaj\Form\Renderer;

use Nette\Forms\Control;
use Nette\Forms\Form;
use Nette\Utils\Html;

class AccessibilityHelper
{
    public static function addAccessibilityMetaDataToControl(Form $form, Control $control, array $wrappers)
    {
        // handle aria meta data for errors
        if (!empty($control->getErrors())) {
            $htmlIdPrefix = $control->getHtmlId() . '-aria-describe_';

            $ariaDescribedBy = [];
            foreach ($control->getErrors() as $key => $error) {
                $ariaDescribedBy[] = $htmlIdPrefix . $key;
            }
            $control->getControlPrototype()->addAttributes([
                'aria-invalid' => 'true',
                'aria-describedby' => implode(' ', $ariaDescribedBy),
            ]);

            $errors = $control->getErrors();
            $control->cleanErrors();
            foreach ($errors as $key => $text) {
                $el = Html::el(
                    'span',
                    [
                        'id' => $htmlIdPrefix . $key
                    ]
                );
                $el->setText($text);
                $control->addError($el);
            }
        }

        // handler aria meta data for description
        if (!empty($control->getOption('description'))) {
            $htmlId = $control->getHtmlId() . '-aria-describe_description';

            $ariaDescribedBy = $control->getControlPrototype()->getAttribute('aria-describedby');
            $control->getControlPrototype()->setAttribute(
                'aria-describedby',
                $ariaDescribedBy . ($ariaDescribedBy ? ' ' : '') . $htmlId
            );

            $description = $control->getOption('description');
            if (is_string($description)) {
                $el = Html::el(
                    $wrappers['control']['description'],
                    [
                        'id' => $htmlId
                    ]
                );
                $el->setText($form->getTranslator()->translate($description));
                $control->setOption('description', $el);
            } else {
                $description->setAttribute('id', $htmlId);
            }
        }
    }
}
