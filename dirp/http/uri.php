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
 *	uri
 *	inspired and heavily based on, (or downright copied from)
 *  SlimPHP's Uri class.
 *
 *	http://www.slimframework.com/
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class uri
{	
	/**
	 * @var array
	 */
	private $_server;

	/**
	 * @var string
	 */
	private $_scheme;

	/**
	 * @var string
	 */
	private $_base_uri;

	/**
	 * @var string
	 */
	private $_uri;

	/**
	 * @var string
	 */
	private $_query_string;

	/**
	 * __construct
	 *
	 * @param array $server $_SERVER-style array
	 */
	public function __construct(array $server = null)
	{
		$this->_server = $server or $_SERVER;
	}

	/**
	 * get_base
	 * returns the base uri without a trailing slash
	 *
	 * @param bool $reload
	 * @return string
	 */
	public function get_base($reload = false)
	{
		if($reload || $this->_base_uri === null)
		{
            $req = isset($this->_server['REQUEST_URI']) ?
            	   $this->_server['REQUEST_URI'] : $this->_server['PHP_SELF'];
           	$sname = $this->_server['SCRIPT_NAME'];
           	$base = strpos($req, $sname) === 0 ? $sname : dirname($sname);
           	$this->_base_uri = rtrim($base, '/');
		}

		return $this->_base_uri;
	}

	/**
	 * get_uri
	 * returns the uri with a leading slash
	 *
	 * @param bool $reload
	 * @return string
	 */
	public function get_uri($reload = false)
	{
		if($reload || $this->_uri === null)
		{
            $uri = '';
            if(!empty($this->_server['PATH_INFO']))
            {
            	$uri = $this->_server['PATH_INFO'];
            }
            else
            {
            	if(isset($this->_server['REQUEST_URI']))
            	{
            		$uri = parse_url($this->_server['REQUEST_URI'], PHP_URL_PATH);
            	}
            	elseif(isset($this->_server['PHP_SELF']))
            	{
            		$uri = $this->_server['PHP_SELF'];
            	}
            	else
            	{
            		throw new Runtime_Exception('Unable to determine request URI');
            	}
            }

            if($this->get_base() !== '' && strpos($uri, $this->get_base()) === 0)
            {
            	$uri = substr($uri, strlen($this->get_base()));
            }

            $this->_uri = '/' . ltrim($uri, '/');
		}

		return $this->_uri;
	}

	/**
	 * get_scheme
	 * returns the scheme for this uri (http/https)
	 *
	 * @param bool $reload
	 * @return string
	 */
	public function get_scheme($reload = false)
	{
		if($reload || $this->_scheme === null)
		{
			$this->_scheme = (empty($this->_server['https']) || $this->_server['https'] == 'off') 
							 ? 'http' : 'https';
		}
		return $this->_scheme;
	}

	/**
	 * get_query
	 * returns the query string portion for this url
	 *
	 * @param bool $reload
	 * @return string
	 */
	public function get_query($reload = false)
	{
		if($reload || $this->_query_string === null)
		{
			$this->_query_string = $this->_server['QUERY_STRING'];
		}

		return $this->_query_string;
	}
	
	/**
	 * factory
	 * 
	 * @param array $server
	 * @return \dirp\http\uri
	 */
	public static function factory(array $server = null)
	{
		$server = $server or $_SERVER;
		return new uri($server);
	}
}