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

namespace dirp\addon;
use \dirp\router as router;

/**
 *
 *	DIRP is a php5 file listing and utilities framework.
 *
 *	manager
 *	addon manager thingy
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class manager
{
	/**
	 *@var array
	 */
	private static $_addons;
	
	/**
	 * load_addons
	 * attempts to load addons, by name.
	 *
	 * @param array $names;
	 */
	public static function load_addons(array $names)
	{
		foreach($names as $addon)
		{
			$addon_instance = static::_addon_loader($addon);

			// addons must always implement an 'about' method,
			// and return an array of info.
			if(method_exists($addon_instance, 'about'))
			{
				if(!$about = call_user_func(array($addon_instance, 'about')))
				{
					continue;
				}

				static::$_addons[$addon] = array(
					'instance' => $addon_instance,
					'about' => $about
				);

				// prepares event listeners for this event:
				if(isset($about['listen']))
				{
					event::register_listener($addon_instance, $about['listen']);
				}

				// register routes for this addon with the router:
				if(isset($about['routes']))
				{
					router::register_handler($addon_instance, $about['routes']);
				}
			}
		}
	}

	/**
	 * get_active_addons
	 * getter for the $_addons private property
	 *
	 * @return array
	 */
	public static function get_active_addons()
	{
		return static::$_addons;
	}
 
	/**
	 * _addon_loader
	 * autoloader helper for addon classes
	 *
	 *@param string $name
	 *@return dirp\addon\base
	 */
	private static function _addon_loader($name)
	{
		$name = "dirp\addon\\$name\\$name";
		return new $name;
	}
}