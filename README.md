Title, description, keywords and custom meta tags for Laravel 4
=======

Installation
------------

Use composer:
-------------

	"repositories": [
		{
			"type": "git",
			"url": "https://github.com/Omashu/Laravel-Front"
		}
	],
	"require": {
		"omashu/front": "1.*"
	},

Configure your app.php
----------------------
	
	providers:
		'Omashu\Front\FrontServiceProvider',

	aliases:
		'Front' => 'Omashu\Front\Facades\FrontFacade',

Use module:
----------

	// adding
	Front::title("Site.ru");
	Front::description("This is my site");
	Front::keywords("site");

	// adding custom tag
	Front::custom("meta", ["name" => "generator", "value" => "Value"]);

	// adding js variables
	Front::js("_token", Session::token());
	Front::js("baseUrl", "http://localhost:8000");

	// show data in your head block
	echo Front::getHtml();