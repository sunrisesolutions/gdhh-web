<?php

namespace App;

use App\Admin\BaseAdmin;
use App\Admin\BaseCRUDAdminController;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel {
	use MicroKernelTrait;
	
	const CONFIG_EXTS = '.{php,xml,yaml,yml}';
	
	public function build(ContainerBuilder $container) {
		parent::build($container);
		$container->registerForAutoconfiguration(BaseCRUDAdminController::class)
		          ->addTag('controller.service_arguments');
	}
	
	public function getCacheDir() {
		return $this->getProjectDir() . '/var/cache/' . $this->environment;
	}
	
	public function getLogDir() {
		return $this->getProjectDir() . '/var/log';
	}
	
	public function registerBundles() {
		$contents = require $this->getProjectDir() . '/config/bundles.php';
		foreach($contents as $class => $envs) {
			if(isset($envs['all']) || isset($envs[ $this->environment ])) {
				yield new $class();
			}
		}
	}
	
	protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader) {
		$container->setParameter('container.autowiring.strict_mode', true);
		$container->setParameter('container.dumper.inline_class_loader', true);
		$confDir = $this->getProjectDir() . '/config';
		
		$loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
		$loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
		$loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
		$loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
		
		/////// <<<  MY CUSTOM ADMIN SERVICE AUTOCONFIG ///////
		
		$definitions = [];
		foreach(get_declared_classes() as $class) {
			if(is_subclass_of($class, BaseAdmin::class)) {
				$className = explode('\\', str_replace('Admin', '', $class));
				
				$def = new Definition();
				$def->setClass($class);
				$def->addTag('sonata.admin', [
					'manager_type'              => 'orm',
					'label'                     => strtolower(end($className)),
					'label_translator_strategy' => 'sonata.admin.label.strategy.underscore'
				]);
				$code = $class;
				if(empty($entity = $class::ENTITY)) {
					$entity = str_replace('Admin\\', 'Entity\\', $code);
					$entity = str_replace('Admin', '', $entity);
				}
				
				if(empty($controller = $class::CONTROLLER)) {
					$controller = $class . 'Controller';
					if( ! class_exists($controller)) {
						$controller = BaseCRUDAdminController::class;
					}
				}
				
				$def->setArguments([ $code, $entity, $controller ]);
				$definitions[ $code ] = $def;
			}
		}
		$container->addDefinitions($definitions);
		/////// >>>  MY CUSTOM ADMIN SERVICE AUTOCONFIG ///////
	}
	
	protected function configureRoutes(RouteCollectionBuilder $routes) {
		$confDir = $this->getProjectDir() . '/config';
		
		$routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
		$routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
		$routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
	}
}
