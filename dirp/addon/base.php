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

/**
 *
 *	DIRP is a php5 file listing and utilities framework.
 *
 *	base
 *	addon base class
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class base
{
	/**
	 * @var \dirp\view
	 */
	protected static $_master;

	/**
	 * path to this addon's folder
	 * @var string
	 */
	private $_root;
	
	/**
	 * view
	 * scoped \dirp\view::factory implementation
	 *
	 * @param string $path
	 * @param array $params
	 * @return \dirp\view
	 */
	public function view($path, array $params = null)
	{
		return \dirp\view::factory($this->get_root() .DS . 'views' . DS . $path);
	}

	/**
	 * master
	 * returns the master view instance
	 *
	 * @return \dirp\view
	 */
	public function master()
	{
		return static::$_master;
	}

	/**
	 * get_root
	 * returns a relative path to this addon's root folder.
	 * no trailing slash
	 *
	 * @param bool $reload
	 * @return string
	 */
	public function get_root($reload = false)
	{
		if($reload === null || !$this->_root)
		{
			$sect = explode('\\', get_class($this));
			$this->_root = implode(DS, array_slice($sect, 1, count($sect) - 2));
		}

		return $this->_root;
	}

	/**
	 * set_master_view
	 * sets the master view object for all addons.
	 *
	 * @param \dirp\view $view
	 */
	public static function set_master_view(\dirp\view $view)
	{
		static::$_master = $view;
	}
}