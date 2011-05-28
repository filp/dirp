<?php
/*
 *	Copyright (C) 2011 by Filipe Dobreira
 *
 *	Permission is hereby granted, free of charge, to any person obtaining a copy
 *	of this software and associated documentation files (the "Software"), to deal
 *	in the Software without restriction, including without limitation the rights
 *	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *	copies of the Software, and to permit persons to whom the Software is
 *	furnished to do so, subject to the following conditions:
 *
 *	The above copyright notice and this permission notice shall be included in
 *	all copies or substantial portions of the Software.
 *
 *	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *	THE SOFTWARE.
 */

namespace dirp;

/**
 *
 *	DIRP is a php5 file listing and utilities framework.
 *
 *	app
 *	main dirp application controller derp derp herp
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class app
{
	/**
	 * internal array of configuration params.
	 * @var array
	 */
	private static $_cfg;

	/**
	 * internal instance of \dirp\http\request
	 * @var \dirp\http\request
	 */
	private static $_http_request;

	/**
	 * internal instance of \dirp\http\response
	 * @var \dirp\http\response
	 */
	private static $_http_response;

	/**
	 * master view instance for the app
	 * @var \dirp\view
	 */
	private static $_master;

	/**
	 * run
	 * the main() to this whole darned thing.
	 *
	 * @param array $configuration
	 */ 
	public static function run(array $configuration)
	{
		define('DS', DIRECTORY_SEPARATOR);
		define('ROOT', __DIR__);

		// having short_open_tag enabled is a dirp
		// requirement.
		if(!ini_get('short_open_tag'))
		{
			throw new \Exception("The 'short_open_tag' setting MUST be enabled in your php.ini to use dirp, sorry!");
		}

		// setup the autoloader and the exception handler:
		set_exception_handler(array('\dirp\app', 'exception_handler'));
		static::_autoloader_init();

		static::$_cfg = new helper\params($configuration);

		static::$_http_request = http\request::factory(
			$_GET,
			$_POST,
			$_SERVER
		);
		static::$_http_response = new http\response();
		
		// prepare addons:
		if($addons = static::cfg()->addons)
		{
			addon\manager::load_addons((array) $addons);
		}

		// the master view is the frame/template around
		// the main content area. addons have access to
		// it directly through the \dirp\addon\base::master
		// method.
		static::$_master = view::factory('master/template',
		array(
			'title'  => 'dirp framework',
			'body'   => '',
			'icon'   => 'folder_heart.png',
			'panels' => addon\event::fire('renderpanels', array('panels' => array()))->panels,
			'css'    => array(), // echo'd as <link rel="stylesheet" tags
			'js'     => array(), // echo'd as <script></script> tags
			'head'   => array() // echo'd directly into <head>
		));

		addon\base::set_master_view(static::$_master);

		// dispatch the request and figure out what to do with
		// the controller's response:
		// TO-DO: HEY THE WAY CONTROLLER RETURNS ARE HANDLED IS KINDA FLAKY.
		if($ret = router::dispatch(static::get_request(), static::get_response()))
		{
			if(is_string($ret))
			{
				if(static::get_request()->is_ajax())
				{
					static::get_response()->header('content-type', 'text/plain');
					static::get_response()->write($ret);
					static::get_response()->send();
				}
				else
				{
					static::get_master()->body = $ret;
				}
			}
		}

		//throw new \RuntimeException('hats!');
		static::get_response()->write(static::get_master()->render());
		addon\event::fire('shutdown', array());
		static::get_response()->send();
	}

	/**
	 * asset
	 * returns an absolute path to an asset
	 *
	 * @param string $path
	 * @return string
	 */
	public static function asset($path)
	{
		return static::get_request()->get_base_uri() . '/assets/' . ltrim($path, '/');
	}
	 
	/**
	 * get_master
	 * returns the app's master view
	 *
	 * @return \dirp\view
	 */
	public static function get_master()
	{
		return static::$_master;
	}

	/**
	 * get_request
	 * returns the app's \dirp\http\request instance
	 *
	 * @return \dirp\http\request
	 */
	public static function get_request()
	{
		return static::$_http_request;	
	}

	/**
	 * get_response
	 * returns the app's \dirp\http\response instance
	 *
	 * @return \dirp\http\response
	 */
	public static function get_response()
	{
		return static::$_http_response;
	}

	/**
	 * cfg
	 * returns the app's config object
	 *
	 * @return \dirp\helper\params
	 */
	public static function cfg()
	{
		return static::$_cfg;
	}

	/**
	 * _autoloader_init;
	 * prepares dirp's builtin autoloader.
	 *
	 * @return null
	 */
	private static function _autoloader_init()
	{
		spl_autoload_register(array('\dirp\app', 'autoload'), true, true);
	}

	/**
	 * autoload
	 * autoloader implementation for dirp classes.
	 * uses namespace separators \ as path / separators,
	 * so for the following class:	\dirp\http\request
	 * this file is required: dirp/http/request.php
	 *
	 * @param string $classname;
	 */
	public static function autoload($class)
	{
		require dirname(__DIR__) . DS . str_replace('\\', DS, $class) . '.php';

		// if this class has an _init method, call it.
		if(is_callable($class.'::_init'))
		{
			call_user_func($class.'::_init');
		}
	}

	/**
	 * exception_handler
	 * internal exception handler for pretty printing
	 * errorrrs!
	 *
	 * @param object $exc
	 */
	public static function exception_handler($exc)
	{
		var_dump($exc);
	}
}