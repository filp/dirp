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
 *	view
 *	view class thingy thingamabob i promise i'll write these
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class view extends \dirp\helper\params
{
	/**
	 * view path
	 * @var string
	 */
	private $_path;

	/**
	 * @var string
	 */
	private static $_base_root;


	/**
	 * __construct
	 *
	 * @param string $path
	 * @param array $params
	 */
	public function __construct($path, array $params = null)
	{
		$this->_path = $path;
		parent::__construct($params);
	}

	/**
	 * __tostring
	 *
	 * @see \dirp\view::render
	 * @return string who woulda thunk it
	 */
	public function __tostring()
	{
		return $this->render();
	}

	/**
	 * render
	 *
	 * @see \dirp\view::_render
	 * @param array $params
	 * @return string
	 */
	public function render(array $params = null)
	{
		if($params)
		{
			parent::merge_params($params);
		}
		return static::_render($this->_path, parent::get_params());
	}

	/**
	 * factory
	 *
	 * @param string $path
	 * @param array $params
	 * @return \dirp\view
	 */
	public static function factory($path, array $params = null)
	{
		return new view($path, $params);
	}

	/**
	 * set_base_root
	 *
	 * @param string $path
	 */
	public static function set_base_root($path)
	{
		static::$_base_root;	
	}

	/**
	 * _render
	 * render method for view instances
	 * TO-DO: implement renderer injection
	 *
	 * @param string $path
	 * @param array $params
	 * @return string
	 */
	private static function _render($path, array $params = null)
	{
		// if for some reason, later on, we want to force a root path
		$root = static::$_base_root or ROOT;
		$path = rtrim(ROOT, '/') . DS . ltrim($path, '/');

		if($params)
		{
			extract($params);
		}

		ob_start();
		require $path . '.php';
		return ob_get_clean();
	}
}