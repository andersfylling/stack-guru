<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;

const NAMESPACE_SEPARATOR = '\\';


abstract class Reflection
{
    /**
     * Determines whether a class/interface/... resides in a given namespace.
     *
     * @param string $fqcn Fully qualified class name
     * @param string $namespace Expected namespace
     *
     * @return bool
     */
    public static function isInNamespace(string $namespace, string $fqcn): bool
    {
        $fqcn = trim($fqcn, NAMESPACE_SEPARATOR);
        $namespace = trim($namespace, NAMESPACE_SEPARATOR) . NAMESPACE_SEPARATOR;
        return strpos($fqcn, $namespace) === 0;
    }

    /**
     * Assemble a fully qualified class name for the given relative class name
     * components.
     *
     * @param string $namespace Namespace
     * @param string ...$relativeClass Class name relative to the namespace
     *
     * @return string Fully qualified class name
     */
    public static function getFullClass(string $namespace, string ...$relativeClass): string
    {
        return $namespace . NAMESPACE_SEPARATOR . implode(NAMESPACE_SEPARATOR, $relativeClass);
    }

    /**
     * Get the relative class name of a class in a namespace.
     *
     * @param string $fqcn Fully qualified class name
     * @param string $namespace Namespace
     *
     * @return string Relative class name
     */
    public static function getRelativeClass(string $namespace, string $fqcn): string
    {
        if (!self::isInNamespace($namespace, $fqcn))
            throw new \ReflectionException("Command class ".$fqcn." does not reside in ".$this->namespace);

        $relativeClass = substr($fqcn, strlen($namespace));
        return $relativeClass;
    }

    /**
     * Get the short class name of a full class name .
     *
     * @param string $className Full class name (including namespaces).
     *
     * @return string Class name without namespaces
     */
    public static function getShortClassName(string $className): string
    {
        return substr(strrchr($className, '\\'), 1);
    }
}
