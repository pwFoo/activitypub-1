<?php

/*
 * This file is part of the ActivityPub package.
 *
 * Copyright (c) landrok at github.com/landrok
 *
 * For the full copyright and license information, please see
 * <https://github.com/landrok/activitypub/blob/master/LICENSE>.
 */

namespace ActivityPub\Type\Validator;

use ActivityPub\Type\Util;
use ActivityPub\Type\ValidatorInterface;

/**
 * \ActivityPub\Type\Validator\AnyOfValidator is a dedicated
 * validator for anyOf attribute.
 */
class AnyOfValidator implements ValidatorInterface
{
    /**
     * Validate an ANYOF attribute value
     * 
     * @param mixed  $value
     * @param mixed  $container An object
     * @return bool
     * @todo Choices can contain Indirect references.
     * 		This validation should validate this kind of usage.
     */
    public function validate($value, $container)
    {
        // Validate that container is a Question type
        if (!is_object($container)
            || !property_exists($container, 'type')
            || $container->type !== 'Question'
        ) {
            return false;
        }

        // Can be a JSON string
        if (is_string($value)) {
            $value = Util::decodeJson($value);
        }

        // A collection
        if (!is_array($value)) {
            return false;
        }

        if (!count($value)) {
            return false;
        }

        return $this->validateObjectCollection($value);
    }

    /**
     * Validate a Note type
     * 
     * @param object $value
     * @return bool
     */
    protected function validateObject($item)
    {
        Util::hasProperties($item, ['type', 'name'], true);

        return $item->type == 'Note' && is_scalar($item->name);
    }

    /**
     * Validate a list of object
     * Collection MUST contain objects with following attributes:
     * - a Note type
     * - a name attribute
     * 
     * @param array $collection
     */
    protected function validateObjectCollection(array $collection)
    {
        foreach ($collection as $item) {
            if (is_object($item) && $this->validateObject($item)) {
                continue;
            }

            return false;
        }

        return true;
    }
}