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
 *	router
 *	does routing stuff and things :\
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class router
{
	/**
	 * @var array
	 */
	private static $_routes;

	/**
	 * dispatch
	 * dispatches execution to a handler based on
	 * a request uri
	 *
	 * pattern modifications based on those by Joe Topjian,
	 * in the GluePHP micro-framework: http://gluephp.com/
	 *
	 * @param \dirp\http\request $request
	 * @param \dirp\http\response
	 * @throws \dirp\exception\routing if no routes are defined
	 * @throws \dirp\exception\routing if a method is assigned but not existant
	 * @return mixed
	 */
	public static function dispatch(\dirp\http\request $request, \dirp\http\response $response)
	{
		if(!static::$_routes)
		{
			return false; // boy i sure hope this is handled somewhere
		}

    	$routes = static::$_routes;
    	$path = $request->get_uri();

    	foreach($routes as $pair)
    	{
    		$pattern = $pair[0];
    		$handler = $pair[1];

    		$pattern = str_replace('/', '\/', $pattern);
    		$pattern = '^' . $pattern . '\/?$';

    		if(preg_match("/$pattern/i", $path, $matches))
    		{
    			if(!method_exists($handler[0], $handler[1]))
    			{
    				throw new \dirp\exception\routing('that method (' . $handler[1] . ') doesn\'t exist!');
    			}

    			// make matches available through the request
    			$request->set_params($matches);

    			// route passing/halting:
    			try {

    				// check for a 'before' handler for this controller.
    				if(method_exists($handler[0], 'before'))
    				{
    					call_user_func($handler[0], 'before', $request, $response);
    				}

	    			if($ret = call_user_func(array($handler[0], $handler[1]), $request, $response))
	    			{
	    				return $ret;
	    			}

	    			return $handler[0];

    			}
    			catch(\dirp\exception\pass $e)
    			{
    				continue;
    			}
    			catch(\dirp\exception\halt $e)
    			{
    				return $e->getMessage();
    			}
    		}
    	}

    	throw new \dirp\exception\routing('no matches, four oh four!');
	}

	/**
	 * pass
	 * issues a \dirp\exception\pass exception forcing
	 * the router to move to the next pattern
	 *
	 * @throws \dirp\exception\pass
	 */
	public static function pass()
	{
		throw new \dirp\exception\pass;
	}

	/**
	 * halt
	 * issues a \dirp\exception\halt exception, forcing
	 * the router to return to the app controller, with
	 * an optional parameter
	 *
	 * @param mixed $thing
	 * @throws \dirp\exception\halt
	 */
	public static function halt($thing = null)
	{
		throw new \dirp\exception\halt($thing);
	}

	/**
	 * register_handler
	 * registers a new set of routes under a handler
	 *
	 * @param object $instance
	 * @param array $routes
	 * @return bool
	 */
	public static function register_handler($instance, array $routes)
	{
		if(static::$_routes === null)
		{
			static::$_routes = array();
		}

		foreach($routes as $route => $method)
		{
			static::$_routes[] = array($route, array($instance, $method));
		}

		return true;
	}
}