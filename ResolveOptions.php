<?php

namespace CoreLogicUtils;

use \Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * Class ResolveOptions
 *
 * @author Anders Øen Fylling
 * @version 0.1
 * @access public
 * @package CoreLogicUtils
 */
class ResolveOptions
{
    /**
     * Verify given array to contain set keys, otherwise throw exception.
     *
     * @param array $options Elements to verify/confirm has the $requirements
     * @param array $requirements Elements that must exist within the $options class
     * @return array $options Nothing gets changed. But gives a nicer syntax. Potentially more to come.
     *
     * @throws MissingOptionsException
     * @see \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public static function verify (array $options = [], array $requirements = []) : array
    {
        /*
         * Loop through each requirement and make sure it exists as a key within the $options array
         */
        foreach ($requirements as $key) {
            if (!key_exists($key, $options)) { // || !$options[$key] instanceof $value) {

                /*
                 * Key didn't exist soa  MissingOptionsException is thrown
                 */
                throw new MissingOptionsException("Key file must be specified (and contain a value): {$key}");
            }
        }

        return $options;
    }
}