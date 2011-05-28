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
 *	event
 *	addon event class..i'm sorry about these descriptions, honestly.
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class event extends \dirp\helper\params
{
	/**
	 * @var array
	 */
	private static $_listeners;

	/**
	 * this event's name/type
	 * @var string
	 */
	private $_name;

	/**
	 * __construct
	 *
	 * @param string $name
	 * @param array $params
	 */
	public function __construct($name, array $params = null)
	{
		$this->_name = $name;
		parent::__construct($params);
	}

	/**
	 * get_name
	 * return this event's name
	 *
	 * @return string
	 */
	public function get_name()
	{
		return $this->_name;	
	}

	/**
	 * last
	 * force the event iterator to stop, effectivelly making
	 * this the last event responder.
	 * 
	 * @throws \dirp\exception\event_break
	 */
	public function last()
	{
		throw new \dirp\exception\event_break;
	}

	/**
	 * fire
	 * fires off a new event, creates a new event 
	 * instance in the process.
	 *
	 * @param string $name
	 * @param array $params
	 * @return \dirp\addon\event
	 */
	public static function fire($name, array $params = null)
	{
		$event = new event($name, $params);
		// no listeners for this event:
		if(!isset(static::$_listeners[$name]))
		{
			return $event;
		}

		// iterate through each listener, in order of subscription,
		// triggering the event method in each.
		foreach(static::$_listeners[$name] as $listener)
		{
			if(!method_exists($listener, 'event_' . $name))
			{
				continue;
			}
			try
			{
				call_user_func(array($listener, 'event_' . $name), $event);
			}
			catch(\dirp\exception\event_break $e)
			{
				break;
			}
		}
		return $event;
	}

	/**
	 * register_listener
	 * subscribes an instance to a list of events
	 *
	 * @param object $listener
	 * @param array $events
	 * @return true
	 */
	public static function register_listener($listener, array $events)
	{
		foreach($events as $event)
		{
			if(!isset(static::$_listeners[$event]))
			{
				static::$_listeners[$event] = array();
			}

			static::$_listeners[$event][] = $listener;
		}

		return true;
	}

	/**
	 * listeners_for
	 * returns an array of listeners for a given event
	 *
	 * @param string $name
	 * @return array
	 */
	public static function listeners_for($event)
	{
		if(!isset(static::$_listeners[$event]))
		{
			return array();
		}

		return static::$_listeners[$event];
	}
}