<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\BladeDirectives;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class BladeServiceProvider extends ServiceProvider {

	/**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {

		view()->composer('*', function($view){
			$view_data = new \StdClass;
			$view_data->name = $view->getName();
			$view_data->path = $view->getPath();
			view()->share('view', $view_data);
		});

		// Component (overrides laravel component)
		Blade::directive('component', function($expression){
			list($template,$vars) = BladeDirectives::parseExpression($expression);
			if (!\View::exists($template)) {
				$path = explode('.',$template);
				$template .= '.'.end($path); // Test if folder
			}
			return "<?php echo \$__env->make('$template',$vars,\ABetter\Toolkit\BladeDirectives::vars(get_defined_vars()))->render(); ?>";
        });

        // Style
        Blade::directive('style', function($expression){
			list($file,$vars) = BladeDirectives::parseExpression($expression);
			$expression = preg_replace('/\'/',"",$expression);
			return "<?php echo \ABetter\Toolkit\BladeDirectives::style('{$file}',array_merge(get_defined_vars(),$vars)); ?>";
        });
        Blade::directive('endstyle', function(){
			return "";
        });

		// Script
        Blade::directive('script', function($expression){
			list($file,$vars) = BladeDirectives::parseExpression($expression);
			return "<?php echo \ABetter\Toolkit\BladeDirectives::script('{$file}',array_merge(get_defined_vars(),$vars)); ?>";
        });
        Blade::directive('endscript', function(){
			return "";
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
