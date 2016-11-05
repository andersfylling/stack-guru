<?php

namespace StackGuru\CoreLogic\Utils;

use \Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;

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
     * @param bool $keysMustBeString
     * @return array $options Nothing gets changed. But gives a nicer syntax. Potentially more to come.
     *
     * @see \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @see \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     */
    public static function verify (
        array $options = [],
        array $requirements = [],
        boolean $keysMustBeString = null
    ) : array
    {
        /*
         * make sure each key is a string.
         */
        if ($keysMustBeString !== null && $keysMustBeString === true) {
            foreach ($options as $key => $value) {
                if (!is_string($key)) {
                    throw new NoSuchOptionException("Keys in array cannot be of any type but strings");
                }
            }
        }

        /*
         * Loop through each requirement and make sure it exists as a key within the $options array
         */
        foreach ($requirements as $key) {
            if (!key_exists($key, $options)) { // || !$options[$key] instanceof $value) {

                /*
                 * Key didn't exist so a MissingOptionsException is thrown
                 */
                throw new MissingOptionsException("Key must be specified: {$key}");
            }
        }

        return $options;
    }
}
