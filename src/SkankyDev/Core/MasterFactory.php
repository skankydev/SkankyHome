<?php 

namespace SkankyDev\Core;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use SkankyDev\Exception\ClassNotFoundException;
use SkankyDev\Exception\UnknownMethodException;
use SkankyDev\Utilities\Traits\Singleton;

class MasterFactory {

	use Singleton;


	public function __construct(){

	}

	public function call(object $object,string $method, array $parameters = [] ){

		if (!method_exists($object, $method)) {
			throw new UnknownMethodException(
				'Unknown method : ' . $method . ' in Class : ' . get_class($object),
				101
			);
		}

		$reflector = new ReflectionMethod($object, $method);

		$dependencies = $this->resolveDependencies(
			$reflector->getParameters(),
			$parameters
		);

		return $reflector->invokeArgs($object, $dependencies);
	}


	public function make(string $className, array $parameters = []){

		$reflector = new ReflectionClass($className);

		if (!$reflector->isInstantiable()) {
			throw new ClassNotFoundException("La classe {$className} n'est pas instanciable");
		}

		if (in_array(Singleton::class, $reflector->getTraitNames())) {
			return $className::getInstance();
		}
		
		$constructor = $reflector->getConstructor();

		if ($constructor === null) {
			return new $className();
		}

		$dependencies = $this->resolveDependencies(
			$constructor->getParameters(),
			$parameters
		);
		
		return $reflector->newInstanceArgs($dependencies);
	}

	protected function resolveDependencies(array $parameters, array $value = []): array {
		$dependencies = [];

		foreach ($parameters as $parameter) {
			$name = $parameter->getName();

			if (array_key_exists($name, $value)) {
				$dependencies[] = $value[$name];
				continue;
			}

			$type = $parameter->getType();
			// Si pas de type ou type primitif
			if ($type === null || $type->isBuiltin()) {
				// Utiliser la valeur par défaut si disponible
				if ($parameter->isDefaultValueAvailable()) {
					$dependencies[] = $parameter->getDefaultValue();
				} else {
					throw new ClassNotFoundException("Impossible de résoudre le paramètre {$name}");
				}
				continue;
			}

			// Résoudre la dépendance de classe
			$className = $type->getName();
			$dependencies[] = $this->make($className);
		}

		return $dependencies;
	}
}