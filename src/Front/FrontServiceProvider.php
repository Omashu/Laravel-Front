<?php

namespace Omashu\Front;

use Illuminate\Support\ServiceProvider;

class FrontServiceProvider extends ServiceProvider {
	public function register()
	{
		$this->app->singleton('front', function($app) {
			return new Front();
		});
	}
}
