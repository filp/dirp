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
 *	response
 *	abstraction class for an HTTP response
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class response
{
	/**
	 * http status code translations
	 * @var array
	 */
	private static $_http_status = array
	(
		// 1xx Informational
		100 => 'Continue',
		101 => 'Switching Protocols',

		// 2xx Successful
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',

		// 3xx Redirection
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',

		// 4xx Client Error
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',

		// 5xx Server Error
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);

	/**
	 * @var int
	 */
	private $_status;

	/**
	 * @var string
	 */
	private $_body;

	/**
	 * @var array
	 */
	private $_headers;

	/**
	 * @var int
	 */
	private $_length;

	/**
	 * header
	 * overloaded getter/setter for http response headers
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return bool
	 */
	public function header($name, $value = null)
	{
		if($value === null)
		{
			return isset($this->_headers[$name]) ? $this->_headers[$name]:null;
		}

		return (bool) $this->_headers[$name] = $value;
	}

	/**
	 * status
	 * overloaded getter/setter for this response's status
	 *
	 * @param int status
	 * @return int
	 */
	public function status($status = null)
	{
		if($status)
		{
			$this->_status = $status;
		}

		return $this->_status;
	}

	/**
	 * write
	 * writes a string to the response's body
	 *
	 * @param string $body
	 * @return int
	 */
	public function write($body)
	{
		$this->_body .= $body;
		$this->_length += strlen($body);

		return $this->_length;
	}

	/**
	 * clean
	 * cleans this response's content
	 */
	public function clean()
	{
		$this->_body = null;
		$this->_length = 0;
		$this->_headers = null;
		$this->_headers = null;
	}

	/**
	 * redirect
	 * redirects the client derpaherp
	 *
	 * @param string $uri
	 * @param int $status
	 */
	public function redirect($uri, $status = 302)
	{
		$this->header('location', $uri);
		$this->send(302);
	}

	/**
	 * send
	 * sends this response and terminates script
	 * execution.
	 *
	 * @param int $status
	 */
	public function send($status = null)
	{
		$status = $status or $this->_status or 200;
		$this->header('content-length', $this->_length);

		if($this->_headers)
		{
			foreach($this->_headers as $k => $v)
			{
				header("$k:$v");
			}
		}

		// send the status header
		$code = isset(static::$_http_status[$status]) ? static::$_http_status[$status] : '';
		header("HTTP/1.1 $status $code");
		flush();

		echo $this->_body;
		exit();
	}
}