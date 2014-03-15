<?php

namespace Database\Hydrator;

use Database\Model\Decision;
use Database\Model\SubDecision;

class Budget extends AbstractDecision
{

    const BUDGET_TEMPLATE = 'De %TYPE% %NAME% van %AUTHOR%, versie %VERSION% van %DATE% wordt %APPROVE%%CHANGES%.';

    /**
     * Budget hydration
     *
     * @param array $data
     * @param SubDecision $object
     *
     * @return SubDecision
     *
     * @throws \InvalidArgumentException when $object is not a SubDecision
     */
    public function hydrate(array $data, $object)
    {
        $object = parent::hydrate($data, $object);
        var_dump($data);

        $subdecision = new SubDecision();

        $subdecision->setNumber(1);
        $subdecision->setMember($data['author']);
        $subdecision->setType($data['type']);
        $date = new \DateTime($data['date']);

        $content = self::BUDGET_TEMPLATE;

        if ($data['type'] == SubDecision::TYPE_BUDGET) {
            $content = str_replace('%TYPE%', 'begroting', $content);
        } else {
            $content = str_replace('%TYPE%', 'afrekening', $content);
        }

        $content = str_replace('%NAME%', $data['name'], $content);
        $content = str_replace('%AUTHOR%', $data['author']->getFullName(), $content);
        $content = str_replace('%VERSION%', $data['version'], $content);
        $content = str_replace('%DATE%', $date->format('d F Y'), $content);

        if ($data['approve'] == 'approve') {
            $content = str_replace('%APPROVE%', 'goedgekeurd', $content);

            if ($data['changes'] == 'yes') {
                $content = str_replace('%CHANGES%', ' met genoemde wijzigingen', $content);
            }
        } else {
            $content = str_replace('%APPROVE%', 'afgekeurd', $content);
        }

        $subdecision->setContent($content);

        $subdecision->setDecision($object);

        return $object;
    }
}