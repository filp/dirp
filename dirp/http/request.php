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

namespace dirp\http;

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
class request
{
	/**
	 * internal GET arguments;
	 * @var array
	 */
	private $_get;

	/**
	 * internal POST arguments;
	 * @var array
	 */
	private $_post;

	/**
	 * internal HTTP request headers;
	 * @var array
	 */
	private $_headers;

	/**
	 * was this an AJAX request?
	 * @var bool
	 */
	private $_is_ajax;

	/**
	 * instance of \dirp\http\url
	 * @var \dirp\http\url
	 */
	private $_uri;

	/**
	 * http request method
	 * @var string
	 */
	private $_method;

	/**
	 * an array of uri matches given by the router
	 * @var array
	 */
	private $_params;

	/**
	 * get
	 * getter for GET data
	 *
	 * @param string $name
	 * @param mixed $default default value if not set.
	 * @return mixed|null null if not set
	 */
	public function get($name, $default = null)
	{
		return isset($this->_get[$name]) ? $this->_get[$name]:$default;
	}

	/**
	 * post
	 * getter for POST data
	 *
	 * @param string $name
	 * @param mixed $default default value if not set.
	 * @return mixed|null null if not set
	 */
	public function post($name, $default = null)
	{
		return isset($this->_post[$name]) ? $this->_post[$name]:$default;
	}

	/**
	 * header
	 * getter for HTTP header data
	 *
	 * @param string $name
	 * @param mixed $default default value if not set.
	 * @return mixed|null null if not set
	 */
	public function header($name, $default = null)
	{
		return isset($this->_headers[$name]) ? $this->_headers[$name]:$default;
	}

	/**
	 * param
	 * getter for uri match parameters, if any.
	 *
	 * @param string|int $index
	 * @param mixed $default
	 * @return mixed|null
	 */
	public function param($index = null, $default = null)
	{
		if(!$this->_params)
		{
			return $default;
		}

		// return all matches if no index is specified
		if($index === null)
		{
			return $this->_params or $default;
		}

		if(isset($this->_params[$index]))
		{
			if(empty($this->_params[$index]))
			{
				return $default;
			}

			return $this->_params[$index];
		}

		return $default;
	}

	/**
	 * is_ajax
	 * is this an ajax request?
	 *
	 * @return bool
	 */
	public function is_ajax()
	{
		return (bool) $this->_is_ajax;
	}

	/**
	 * get_method
	 *
	 * @return string
	 */
	public function get_method()
	{
		return $this->_method;
	}

	/**
	 * get_base_uri
	 *
	 * @see \derp\http\uri::get_base
	 * @return string
	 */
	public function get_base_uri()
	{
		return $this->_uri->get_base();
	}

	/**
	 * get_uri
	 *
	 * @see \derp\http\uri::get_uri
	 * @return string
	 */
	public function get_uri()
	{
		return $this->_uri->get_uri();
	}

	/**
	 * set_params
	 * setter for this request's params. in normal
	 * use, will only be called by the router
	 *
	 * @param array $params
	 * @see \derp\router::dispatch
	 */
	public function set_params(array $params)
	{
		$this->_params = (array) $params;
	}
	
	/**
	 * get_uri_instance
	 *
	 * @see \derp\http\uri
	 * @return \derp\http\uri
	 */
	public function get_uri_instance()
	{
		return $this->_uri;
	}

	/**
	 * __construct
	 *
	 * @param array $get
	 * @param array $post
	 * @param array $server
	 */
	public function __construct(array $get, array $post, array $server)
	{
		$this->_get = $get;
		$this->_post = $post;
		$this->_headers = static::_prepare_headers($server);
		$this->_uri = uri::factory($server);

		if(isset($server['REQUEST_METHOD']))
		{
			$this->_method = $server['REQUEST_METHOD'];
		}
		
		// was this an ajax request?
		if(isset($server['X_HTTP_REQUESTED_WITH']))
		{
			$this->_is_ajax = $server['X_HTTP_REQUESTED_WITH'] == 'XMLHttpRequest';
		}
	}
	
	/**
	 * factory
	 * this class factory accepts optional arrays for
	 * $_GET, $_POST and $_SERVER variables - in this
	 * manner it's very very easy to simulate requests.
	 * 
	 * @param array $get
	 * @param array $post
	 * @param array $server
	 * @return dirp\http\request
	 */
	public static function factory(array $get = null, array $post = null, array $server = null)
	{
		$get = $get or $_GET;
		$post = $post or $_POST;
		$server = $server or $_SERVER;
		return new request($get, $post, $server);
	}

	/**
	 * _prepare_headers
	 * extracts HTTP headers from a $_SERVER-style array
	 * and returns them in a friendlier format.
	 *
	 * @param array $list
	 * @return array
	 */
	private static function _prepare_headers(array $list)
	{
		$headers = array();
		foreach($list as $k => $v)
		{
			if(substr($k, 0, 5) == 'HTTP_')
			{
				$headers[strtolower(substr($k, 5))] = $v;
			}
		}

		return $headers;
	}
}