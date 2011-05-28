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
 *	file
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class file extends \dirp\helper\params
{
	/**
	 * a list of extensions and their icons.
	 * @var array
	 */
	private static $_icons;

	/**
	 * used to map properties to the correct
	 * SplFileInfo getter method on-demand.
	 * @var array
	 */
	private static $_map = array(
		'name' 		 => 'getFilename',
		'size'		 => 'getSize',
		'atime' 	 => 'getATime',
		'ctime' 	 => 'getCTime',
	//	'extension'  => 'getExtension', scumbag SplFileInfo doesn't actually implement this :|
		'mtime' 	 => 'getMTime',
		'owner' 	 => 'getOwner',
		'path' 		 => 'getPath',
		'isdir' 	 => 'isDir',
		'isfile' 	 => 'isFile',
		'readable'   => 'isReadable',
		'writable'   => 'isWritable',
		'fullpath'   => 'getRealPath'
	);

	/**
	 * @var SplFileInfo
	 */
	private $_instance;

	/**
	 * read
	 * returns this file's contents
	 *
	 * @return string|null
	 */
	public function read()
	{
		if(!$this->isfile || !$this->readable)
		{
			return null;
		}

		return file_get_contents($this->fullpath);
	}

	/**
	 * write
	 * writes to this file
	 *
	 * @param string $str
	 * @param bool $append
	 * @return bool
	 */
	public function write($str, $append = false)
	{
		if(!$this->isfile || !$this->writable)
		{
			return false;
		}

		return (bool) file_put_contents($this->fullpath, $str, $append ? FILE_APPEND : 0);
	}

	/**
	 * delete
	 * deletes this file. depends on the 'disallow.delete'
	 * configuration parameter.
	 *
	 * @return bool
	 */
	public function delete()
	{
		// users may chose to disallow any files to be
		// deleted through this method. not by any means
		// safe, but possibly a helper against buggy addons.
		if(\dirp\app::cfg()->disallow_delete)
		{
			return false;
		}

		if($this->isfile)
		{
			return (bool) @unlink($this->fullpath);
		}
		elseif($this->isdir)
		{
			return (bool) @rmdir($this->fullpath);
		}
	}

	/**
	 * get_pretty_size
	 * returns a formatted size property for this file,
	 * by rounding to the highest unit.
	 *
	 * @return string|null
	 */
	public function get_pretty_size()
	{
		static $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		if(!$this->size)
		{
			return null;
		}

		$bytes = max($this->size, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 

		$bytes /= (1 << (10 * $pow)); 
		return round($bytes, 2) . $units[$pow]; 
	}

	/**
	 * __construct
	 *
	 * @param SplFileInfo $file
	 */
	public function __construct(\SplFileInfo $file)
	{
		$this->_instance = $file;
		// apparently SplFileInfo doesn't actually
		// implement getExtension, so this is a quick
		// workaround for now.
		$inf = pathinfo($this->name);
		if(isset($inf['extension']))
		{
			$this->extension = $inf['extension'];
		}

		// check if this is a private file:
		$this->visible = !static::is_private($this->name);

		// losing filesize, somewhere, somehow, for
		// some reason. hackety hack away:
		$this->size = $file->getSize();
	}

	/**
	 * __tostring
	 * returns this file's name
	 *
	 * @return string
	 */
	public function __tostring()
	{
		return $this->name;
	}

	/**
	 * _get_icon
	 * return this extension's icon
	 *
	 * @return string
	 */
	public function _get_icon()
	{
		$ext = $this->isdir ? 'folder' : $this->extension;
		if(isset(static::$_icons[$ext]))
		{
			$icon = static::$_icons[$ext];
		}
		else
		{
			$icon = isset(static::$_icons['misc']) ? static::$_icons['misc'] : null;
		}

		return addon\event::fire('geticon', 
			array(
					'file' => $this, 
					'icon' => $icon, 
					'extension' => $ext
				 )
		)->icon;
	}

	/**
	 * __get
	 * decorates \dirp\helper::__get to fetch instance
	 * data on demand, as opposed to using one big array
	 * in the __construct call, which would mean a large
	 * overhead for every loaded file.
	 *
	 * known parameters are mapped to their correct SplFileInfo
	 * method, while everything else works as expected.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		// custom case for this extension's icon:
		if($name == 'icon' && parent::__get('icon') === null)
		{
			parent::__set('icon', $this->_get_icon());
		}

		// check for a mapped property:
		elseif(parent::__get($name) === null)
		{
			if(isset(static::$_map[$name]))
			{
				parent::__set($name, call_user_func(array($this->_instance, static::$_map[$name])));
			}
		}

		return parent::__get($name);
	}

	/**
	 * to_path
	 * builds a path with no leading or trailing slashes
	 * out of function arguments.
	 *
	 * @param string $path,...
	 * @return string
	 */
	public static function to_path()
	{
		return trim(implode('/', array_filter(func_get_args(), 'is_string')), '/');
	}

	/**
	 * to_relative_path
	 * turns an absolute path into a relative path starting
	 * at the defined files root
	 *
	 * @param string $path
	 * @return string
	 */
	public static function to_relative_path($path)
	{
		return rtrim(substr($path, strlen(app::cfg()->files)+1), DS);
	}

	/**
	 * is_private
	 * checks a string against file privacy settings and
	 * event listeners for isprivate.
	 *
	 * @param string $thing
	 * @return bool
	 */
	public static function is_private($thing)
	{
		if(app::cfg()->private)
		{
			foreach(app::cfg()->private as $pattern)
			{
				if(is_array($thing))
				{
					foreach($thing as $thang)
					{
						if(preg_match($pattern, $thang))
						{
							return true;
						}
					}
				}
				elseif(preg_match($pattern, $thing))
				{
					return true;
				}
			}
		}
		return addon\event::fire('isprivate', array('thing' => $thing, 'private' => false))->private;
	}

	/**
	 * safe_path
	 * check if a RELATIVE path exists, and is within the safely
	 * accessible scope. This path should start at the files root.
	 * 
	 * if the second parameter is omitted, the default
	 * return value for unsafe paths is the safe root path.
	 *
	 * @param string $path
	 * @param bool $bool
	 * @return bool|string
	 */
	public static function safe_path($path, $bool = false)
	{
		$root_path = realpath(app::cfg()->files);
		$path = realpath($root_path . DS . ltrim($path, DS));
		$root_path = explode(DS, $root_path);
		$path = explode(DS, $path);

		foreach($path as $sect)
		{
			if(static::is_private($sect))
			{
				return $bool ? false : implode('/', $root_path);
			}	
		}

		if($root_path !== array_slice($path, 0, count($root_path)))
		{
			return $bool ? false : implode('/', $root_path);
		}

		return $bool ? true : implode('/', $path);
	}

	/**
	 * from_directory
	 * iterates and returns an array of sorted  
	 * \dirp\file objects from a directory.
	 * 
	 * the sorting process is extremely simple,
	 * simply tossing folders at the top, and files
	 * in the bottom, ordered with ksort()
	 *
	 * @param string|\DirectoryIterator $path
	 * @return false|array array of \dirp\file objects
	 */
	public static function from_directory($path)
	{
		if(!($path instanceof \DirectoryIterator))
		{
			try
			{
				$dir = new \DirectoryIterator($path);
			}
			catch(\UnexpectedValueException $e)
			{
				return false;
			}
		}

		$files = array();
		$folders = array();
		foreach($dir as $file)
		{
			// ignore dots, we don't care about those.
			if($file->getFilename() == '.' || $file->getFilename() == '..')
			{
				continue;
			}	

			$file = static::factory($file);
			if($file->isdir)
			{
				$folders[$file->name] = $file;
			}
			else
			{
				$files[$file->name] = $file;
			}
		}

		ksort($files);
		ksort($folders);
		return array_merge($folders, $files);
	}

	/**
	 * factory
	 * 
	 * @param \DirectoryIterator|\SplFileInfo|string $file
	 * @param bool $create create this file if it doesn't exist
	 * @return \dirp\file|bool
	 */
	public static function factory($file, $create = false)
	{
		if(!($file instanceof \SplFileInfo))
		{
			if($create)
			{
				if(!touch($file))
				{
					// can't touch this
					return false;
				}
			}

			try
			{
				$file = new \SplFileInfo($file);
			}
			catch(UnexpectedValueException $e)
			{
				return false;
			}
		}

		return new file($file);
	}

	/**
	 * _init
	 * called by the \dirp\app autoloader automatically,
	 * prepares icons and such.
	 */
	public static function _init()
	{
		// the iconslist events allows addons to register
		// their own custom icons.
		static::$_icons = addon\event::fire('iconslist',
			array(
				'exe'    => 'binary.png',
				'doc'    => 'doc.png',
				'zip'    => 'archive.png',
				'rar'    => 'archive.png',
				'tar'    => 'archive.png',
				'7z'     => 'archive.png',
				'fla'    => 'fla.png',
				'swf'    => 'fla.png',
				'html'   => 'html.png',
				'md'     => 'md.png',
				'psd'    => 'psd.png',
				'ruby'   => 'ruby.png',
				'sig'    => 'sig.png',
				'svg'    => 'vec.png',
				'ai'     => 'vec.png',
				'eps'    => 'vec.png',
				'wmv'    => 'vid.png',
				'mpg'    => 'vid.png',
				'mp4'    => 'vid.png',
				'jpg'    => 'jpg.png',
				'gif'    => 'gif.png',
				'pdf'    => 'pdf.png',
				'txt'    => 'txt.png',
				'misc'   => 'unknown.png',
				'folder' => 'folder.png',
				'php'    => 'php.png',
				'mp3'    => 'music.png',
				'wma'    => 'music.png',
				'v0'     => 'music.png',
				'flac'   => 'music.png'
			)
		)->get_params();
	}
}