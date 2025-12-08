<?php 

namespace SkankyDev\Core;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use SkankyDev\Exception\ClassNotFoundException;
use SkankyDev\Exception\UnknownMethodException;
use SkankyDev\Exception\ModelNotFoundException;
use SkankyDev\Utilities\Traits\Singleton;
use SkankyDev\Model\Document\MasterDocument;

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
		$paramKey = 0;

		foreach ($parameters as $parameter) {
			$name = $parameter->getName();
			$type = $parameter->getType();
			$className = $type->getName();

			if (is_subclass_of($className, MasterDocument::class)) {
				$id = false;
				if(isset($value[$name])){
					$id = $value[$name];

				}else if(isset($value[$paramKey])){
					$id = $value[$paramKey];
					$paramKey++;
				}

				if($id){
					$model = $className::find($id);
					if (!$model) {
						throw new ModelNotFoundException("Document {$className} avec ID {$id} introuvable");
					}
					$dependencies[] = $model;
                	continue;
				}
			}

			if (array_key_exists($name, $value)) {
				$dependencies[] = $value[$name];
				continue;
			}

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