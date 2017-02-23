<?php
declare(strict_types=1);

namespace StackGuru\Core\Service;

use StackGuru\Core\Utils;
use StackGuru\Core\Command\CommandContext;

// Object to hold all services
class Services
{
	private $services;

	public function __construct() 
	{
		$this->services = [];

		echo "loaded services";
	}

	public function loadServicesFolder(string $namespace, string $path, CommandContext $ctx) : void 
	{
        // Fix namespace if not fully qualified
        if (substr($namespace, 0, 1) != '\\')
            $namespace = '\\' . $namespace;
        $namespace = rtrim($namespace, '\\');

        // Check if path exists
        if (!is_dir($path)) {
            throw new \RuntimeException("Folder ".$path." doesn't exist");
        }

        // Find service files
        $files = Utils\Filesystem::dig($path);

        foreach ($files as $file) {
        	require_once($path . '/' . $file);
        	$service = $this->add($namespace, ucfirst(basename($file, '.php')));
        	if ($service->isEnabled($ctx)) {
        		$instance = $service->createInstance();
        		$instance->start($ctx);
        	}
        }
	}

	public function add(string $namespace, string $class): ?ServiceEntry 
	{
		$entry = new ServiceEntry($namespace, $class);
		$this->services[strtolower($class)] = $entry;

		return $entry;
	}

	public function remove(string $name): ?ServiceEntry 
	{
		$entry = $this->getService($name);

		if (null !== $entry) {
			unset($this->services[$name]);
		}

		return $entry;
	}

	public function get(string $name): ?ServiceEntry 
	{
		if (null === $name) {
			return null;
		}

		$name = trim($name);

		if (false === isset($this->services[$name])) {
			return null;
		} 

		return $this->services[$name];
	}

	/**
	 * Always 0 or higher.
	 * 
	 * @return [type] [description]
	 */
	public function size() : int
	{
		return sizeof($this->services);
	}
}