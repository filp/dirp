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

namespace dirp\addon\index;
use \dirp\file;
use \dirp\addon\event;

/**
 *
 *	DIRP is a php5 file listing and utilities framework.
 *
 *	index
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
class index extends \dirp\addon\base
{
	/**
	 * about
	 *
	 *@return array
	 */
	public static function about()
	{
		return array(
			'name' 		  => 'Index',
			'description' => 'Directory listing addon.',
			'author'	  => 'Filipe Dobreira',
			'version' 	  => '1.0.0',

			'listen' 	  => array( 'rendernav' ),
			'routes' 	  => array(
				'/.*' 	  => 'index'
			)
		);
	}

	/**
	 * index
	 *
	 * @param \dirp\http\request $req
	 * @param \dirp\http\response $res
	 */
	public function index(\dirp\http\request $req, \dirp\http\response $res)
	{

		$path = file::safe_path($req->get('dir', '/'));
		$files = file::from_directory($path);
		$relative = htmlentities(file::to_relative_path($path));

		// most likely tried to list a file. a file is 
		// not a directory, silly.
		if($files === false)
		{
			$res->redirect($req->get_base_uri());
		}

		$ev = event::fire('indexlist', 
			array('files' => $files, 'body' => null, 'relative' => $relative, 'path' => $path)
		);

		// prepare the breadcrumb:
		$root = $req->get_base_uri() . '?dir=';
		$parts = explode('/', $relative);
		$crumbs = array();
		foreach($parts as $i => $crumb)
		{
			$crumbs[$crumb] = $root .'/'. implode('/', array_slice($parts, 0, $i+1));
		}

		$this->master()->title = 'viewing: /' . $relative;
		$this->master()->css[] = \dirp\app::asset('css/index/index.css');
		$this->master()->body = $this->view('list')->render(
			array(	
				'files' => $ev->files,
				'relative' => $relative,
				'crumbs' => $crumbs,
				'root' => $req->get_base_uri() . '/',
				'filesroot' => \dirp\app::cfg()->files_uri,
				'bodyoverride' => $ev->body // body override by an event:
			)
		);
		
	}

	/**
	 * event_rendernav
	 * adds the Index link to the navigation.
	 *
	 * @param \dirp\addon\event $nav
	 */
	public function event_rendernav(\dirp\addon\event $nav)
	{
		$nav->links['Index'] = \dirp\app::get_request()->get_base_uri();
	}
}