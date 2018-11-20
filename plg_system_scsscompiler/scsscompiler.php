<?php
/**
 * @package        SCSS Compiler
 * @version        0.2
 *  
 * @author         Carsten Ruppert <webmaster@headmarketing.de>
 * @link           https://www.headmarketing.de
 * @copyright      Copyright © 2018 HEAD. MARKETING GmbH All Rights Reserved
 * @license        http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright    Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport( 'joomla.filesystem.folder' );

use \Joomla\CMS\Plugin;
use \Joomla\CMS\Language;
use \Leafo\ScssPhp;

class plgSystemScsscompiler extends Plugin\CMSPlugin {

	protected $app;                     // Wird im Constructor der Elternklasse \Joomla\CMS\Plugin\CMSPlugin gesetzt, wenn hier vorhanden.
	protected $autoloadLanguage = true; // Automatisches laden der Sprachdatei. Das passiert im Contructor der Elternklasse, wenn hier gesetzt.

	private $input;
	private $output;
	private $verbose;

	public function onBeforeRender() 
	{
		if( ! $this->app->isClient('site'))
		{
			return;
		}

		$this->verbose = (bool) $this->params->get('verbose_compiler', false);

		$this->input = (object) [
			'dir' 	=> JPATH_THEMES . DIRECTORY_SEPARATOR . $this->app->getTemplate() . DIRECTORY_SEPARATOR . $this->params->get('scss_input_directory','scss'), 
			'file' 	=> $this->params->get('scss_input_file','template.scss')
		];
		$this->input->path = $this->input->dir . DIRECTORY_SEPARATOR . $this->input->file;

		if($this->app->isClient('site') && ! JFile::exists($this->input->path))
		{
			if($this->verbose) 
			{
				$this->app->enqueueMessage('plg_system_scsscompiler: ' . Language\Text::sprintf('PLG_SYS_SCSSCOMP_OUT_MISSING_INPUT_FILE', $this->input->path), 'error');
			}
			return;
		}

		$this->output = (object) [
			'dir' 	=> JPATH_THEMES . DIRECTORY_SEPARATOR . $this->app->getTemplate() . DIRECTORY_SEPARATOR . $this->params->get('css_output_directory','css'), 
			'file' 	=> $this->params->get('css_output_file','template.css')
		];
		$this->output->path = $this->output->dir . DIRECTORY_SEPARATOR . $this->output->file;

		if($this->needsRecompile()) 
		{
			$this->compileScss();
		}
	}
	
	private function needsRecompile()
	{
		// Prüfe ob template.css existiert
		if( ! JFile::exists($this->output->path)
			|| (bool) $this->params->get('force_compilation', 0))
		{
			return true;
		}

		// Prüfe ob irgendeines der Source-Files neuer ist als template.css
		$age 	= filemtime($this->output->path);
		$files 	= JFolder::files($this->input->dir, '^.*\.scss$', true, true);
		foreach($files as $file) 
		{
			if(filemtime($file) > $age)
			{
				return true;
			}
		}

		// Es muss nichts getan werden.
		if($this->verbose) 
		{
			$this->app->enqueueMessage('plg_system_scsscompiler: ' . Language\Text::_('PLG_SYS_SCSSCOMP_OUT_NO_COMPILATION_NEEDED'), 'notice');
		}

		return false;
	}

	private function compileScss()
	{
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'scssphp' . DIRECTORY_SEPARATOR . 'scss.inc.php';

		$compiler = new ScssPhp\Compiler();
		$compiler->setImportPaths($this->input->dir . DIRECTORY_SEPARATOR);

		$format = $this->params->get('compiler_formatter', 'Crunched');
		$compiler->setFormatter('Leafo\ScssPhp\Formatter\\' . $format);

		if($this->params->get('compiler_line_comments',0) 
			&& in_array($format, array('Expanded', 'Compact', 'Nested')))
		{
			$compiler->setLineNumberStyle(ScssPhp\Compiler::LINE_COMMENTS);
		}

		try 
		{
			$this->output->code = $compiler->compile('@import "' . $this->input->file . '"');
			$written = file_put_contents($this->output->path, $this->output->code);

			if($written && $this->verbose) 
			{
				$this->app->enqueueMessage('plg_system_scsscompiler: ' . Language\Text::sprintf('PLG_SYS_SCSSCOMP_OUT_COMPILATION_SUCCEEDED', $written), 'notice');
			}

		}
		catch(\Exception $e)
		{   
			if($this->verbose) 
			{
				$this->app->enqueueMessage('plg_system_scsscompiler: ' . Language\Text::sprintf('PLG_SYS_SCSSCOMP_OUT_COMPILATION_FAILED', $this->input->path) . ' <br>' . $e->getMessage(), 'error');
			}
			syslog(LOG_ERR, 'plg_system_scsscompiler: ' . Language\Text::sprintf('PLG_SYS_SCSSCOMP_OUT_COMPILATION_FAILED', $this->input->path));
		}
	}

}
