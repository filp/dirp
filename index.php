<?php
	
/**
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
 *
 *
 *	DIRP is a php5 file listing and utilities framework.
 *
 *	@author Filipe Dobreira <http://dirp.fildob.com>
 *	@version 1.0.0
 */
$configuration = array(

	/*
	 *	addons ARRAY:
	 *	An array containing enabled addons, by name.
	 *
	 *		example: 'addons' => array('index', 'codeview')
	 */
	'addons'	=>	array(
							'dirtools',
							'index',
					),

	/*
	 *	An *absolute* path to the public files folder.
	 *
	 *	WARNING: by default, this directory and all sub-directories
	 *	will become publicly viewable.
	 *	
	 *	WARNING: by default, this directory must reside in the 
	 *	document root, or files therein will not be accessible.
	 *
	 *		example: 'files' => '/home/user/public_html/myfiles/'
	 *		example: 'files' => __DIR__ . '/myfiles'
	 *		( looks for directory 'myfiles' in this directory)
	 */
	'files'		=>	__DIR__ . '/files',

	/*
	 *	A *uri* root pointing to the files location, or a qualified 
	 *	handler. By default, file links will be in the following 
	 *	format:
	 *
	 *	<files.uri>/path/to/file.ext
	 *
	 *		example: 'files.uri' => 'files'
	 *		example: 'files.uri' => 'public/files/dir'
	 *		example: 'files.uri' => 'filehandler.php?file='
	 */
	'files_uri'	=>	'files',

	/*
	 *	A list of patterns used to define file visibility. File and
	 *	folder names are all matched against these patterns, and if
	 *	they match, are excluded from directory listing.
	 *
	 *		example pattern: '/^_/' -- hide all files beginning with a _
	 *		example pattern: '/*.\.php$' -- hide all .php files
	 */
	'private' => array(
						'/^_/',
						'/^\.htaccess$/',
						'/^index\.php/',
						'/^gitkeep$/',
						'/assets/',
						'/dirp/',
						'/files/',
						'/\.gitignore/'
					)
);

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

// blast-off! omg i'm so excited i just wanna hurl
require 'dirp/app.php';
dirp\app::run($configuration);