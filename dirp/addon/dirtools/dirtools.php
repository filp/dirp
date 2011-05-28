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

namespace dirp\addon\dirtools;
use \dirp\file;
use \dirp\addon\event;
use \dirp\app;

/**
 *
 *	DIRP is a php5 file listing and utilities framework.
 *
 *	dirtools
 *	toolset for directory-based operations
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class dirtools extends \dirp\addon\base
{
	/**
	 * about
	 *
	 *@return array
	 */
	public static function about()
	{
		return array(
			'name' 		  => 'DirTools',
			'description' => 'Directory-based toolset.',
			'author'	  => 'Filipe Dobreira',
			'version' 	  => '1.0.0',

			'listen' 	  => array( 'indexlist' )
		);
	}
	
	/**
	 * 	event_indexlist
	 * 
	 * @param \dirp\addon\event $index
	 */
	public function event_indexlist(\dirp\addon\event $index)
	{
		if(isset($index->files['config.php']))
		{
			$config = require $index->files['config.php']->path . DS . $index->files['config.php']->name;
			if(is_array($config))
			{
				$config = new \dirp\helper\params($config);

				// fire off an event with this data, and give
				// addons a chance to override other behavior:
				event::fire('dirconfig', array('config' => $config, 'indexlist' => $index));

				// passworded directory:
				
			}
		}
	}

	/**
	 * _init
	 * add dirtools config.php files to the privacy list.
	 */
	public static function _init()
	{
		if(!app::cfg()->private)
		{
			app::cfg()->private = array();
		}

		app::cfg()->private[] = '/config/';
	}
}