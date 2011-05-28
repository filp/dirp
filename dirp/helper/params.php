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

namespace dirp\helper;

/**
 *
 *	DIRP is a php5 file listing and utilities framework.
 *
 *	params
 *	helper class for accessible-parameter classes. takes
 *  care of the __get issue internally and all that good stuff.
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class params
{
	/**
	 * @var array
	 */
	private $_params;

	/**
	 * __construct
	 *
	 * @param array $params
	 */
	public function __construct(array $params = null)
	{
		// force params to go through __set:
		// TO-DO: can this be done a bit more elegantly?
		if($params !== null)
		{
			foreach($params as $param => $value)
			{
				$this->$param = $value;
			}
		}
	}

	/**
	 * __set
	 *
	 * the __get magic method always returns
	 * by value - this can become an issue 
	 * if we're working with arrays, so those
	 * are converted to ArrayObject instances.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	public function __set($name, $value)
	{
		if(is_array($value))
		{
			$value = new \ArrayObject($value);
		}
		
		return ( $this->_params[$name] = $value );
	}

	/**
	 * __get
	 *
	 * @param string $name
	 * @return mixed|null null if not set
	 */
	public function __get($name)
	{
		return isset($this->_params[$name]) ? $this->_params[$name] : null;
	}

	/**
	 * __isset
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->_params[$name]);
	}

	/**
	 * __unset
	 *
	 * @param string $name
	 */
	public function __unset($name)
	{
		unset($this->_params[$name]);
	}

	/**
	 * get_params
	 * returns this instance's parameters
	 *
	 * @return array
	 */
	public function get_params()
	{
		return $this->_params;
	}

	/**
	 * merge_params
	 * merges an array with this instance's parameters
	 *
	 * @param array $params
	 */
	public function merge_params(array $params)
	{
		$this->_params = array_merge((array) $this->_params, $params);
	}
}