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
 *	request
 *	abstraction class for an HTTP request.
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class session
{
	/**
	 * internal copy of $_SESSION or $_SESSION-style array
	 * @var array
	 */
	private $_session;

	/**
	 * default namespace for this instance
	 * @var string
	 */
	private $_namespace;

	/**
	 * get
	 * getter for session data. accepts a default value,
	 * and a session namespace parameter.
	 * 
	 * @param string $name
	 * @param mixed $default
	 * @param string $namespace
	 * @return mixed
	 */
	public function get($name, $default = null, $namespace = null)
	{
		$namespace = $namespace or $this->_namespace;
		if(!isset($_SESSION[$namespace]))
		{
			return $default;
		}

		return isset($_SESSION[$namespace][$name]) ? $_SESSION[$namespace][$name]:$default;
	}

	/**
	 * set
	 * setter for session data. accepts a namespace parameter.
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @param string $namespace
	 * @return mixed
	 */
	public function set($name, $value, $namespace = null)
	{
		$namespace = $namespace or $this->_namespace;
		if(!isset($_SESSION[$namespace]))
		{
			$_SESSION[$namespace] = array();
		}

		return $_SESSION[$namespace][$name] = $value;
	}

	/**
	 * __construct
	 *
	 * @param string $namespace
	 */
	public function __construct($namespace = 'global')
	{
		$this->_namespace = $namespace;
	}

	/**
	 * factory
	 * accepts a default namespace parameter
	 *
	 * @param string $namespace
	 * @return \dirp\session
	 */
	public static function factory($namespace = 'global')
	{
		if(!isset($_SESSION))
		{
			session_start();
		}

		// sure hope argument inconsistency doesn't become a problem
		// later, but at this time it makes sense to me.
		return new session($namespace);
	}
}