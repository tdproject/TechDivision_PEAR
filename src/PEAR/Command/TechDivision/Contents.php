<?php

/**
 * PEAR_Command_TechDivision_Contents
 *
 * NOTICE OF LICENSE
 * 
 * PEAR_Command_TechDivision is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * PEAR_Command_TechDivision is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with PEAR_Command_TechDivision. If not, see <http://www.gnu.org/licenses/>.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PEAR_Command_TechDivision 
 * to newer versions in the future. If you wish to customize PEAR_Command_TechDivision 
 * for your needs please refer to http://www.techdivision.com for more information.
 *
 * @category   PEAR_Command
 * @package    PEAR_Command_TechDivision
 * @copyright  Copyright (c) 2009 <tw@techdivision.com> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 */

/**
 * Generates the contentsNode of the package2.xml.
 * 
 * @category   PEAR_Command
 * @package    PEAR_Command_TechDivision
 * @copyright  Copyright (c) 2009 <tw@techdivision.com> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 * @author     Tim Wagner <tw@techdivision.com>
 */
class PEAR_Command_TechDivision_Contents
{
    
	/**
	 * Array of argv items
	 * 
	 * @var string[]
	 */
	private $arguments = null;
	
	/**
	 * Path to source package2.xml file
	 * 
	 * @var string
	 */
	private $templateFile = null;
	
	/**
	 * Filename of modified package2.xml file
	 * 
	 * @var string
	 */
	private $generatedFile = 'package2.xml';
	
	/**
	 * Path to contents source directory
	 * 
	 * @var string
	 */
	private $srcDir = null;
	
	/**
	 * Path to save the new package2.xml
	 * 
	 * @var string
	 */
	private $saveDir = null;
	
	/**
	 * Name of the Module to generate contents for
	 * 
	 * @var string
	 */
	private $moduleName = null;
	
	/**
	 * Imported src directory recursively 
	 * 
	 * @var string[]
	 */
	private $contentsItems = array();
	
	/**
	 * DOM object of the source package2.xml file
	 * 
	 * @var DOMDocument
	 */
	private $templateDOM;
	
	/**
	 * Roles matching
	 * 
	 * @var string[]
	 */
	private $roles = array();
	
	protected $_toIgnore = array(
		".", "..", ".svn", "package.xml", "package2.xml"
	);
	
	/**
	 * @param $moduleName the $moduleName to set
	 */
	public function setModuleName($moduleName) {
		// set the module name
	    $this->moduleName = $moduleName;
	    // split the module name in namespace and module
	    list($namespace, $module) = explode('/', $this->moduleName);
	    // initialize the array with the roles
		$this->roles = array(
		    "{$this->moduleName}"	            => 'php',
    		"{$this->moduleName}/Common" 	    => 'php',
    		"{$this->moduleName}/Controller"	=> 'php',
    		"{$this->moduleName}/Model"			=> 'php',
    		"{$this->moduleName}/Model"			=> 'php',
    		"design"		                    => 'www',
    		"META-INF"                          => 'data',
    		"WEB-INF" 	                        => 'data',
		);
	}

	/**
	 * @return the $moduleName
	 */
	public function getModuleName() {
		return $this->moduleName;
	}

	/**
	 * @param $saveDir the $saveDir to set
	 */
	public function setSaveDir($saveDir) {
		$this->saveDir = $saveDir;
	}

	/**
	 * @param $srcDir the $srcDir to set
	 */
	public function setSrcDir($srcDir) {
		$this->srcDir = $srcDir;
	}

	/**
	 * @param $generatedFile the $generatedFile to set
	 */
	public function setGeneratedFile($generatedFile) {
		$this->generatedFile = $generatedFile;
	}

	/**
	 * @param $templateFile the $templateFile to set
	 */
	public function setTemplateFile($templateFile) {
		$this->templateFile = $templateFile;
	}

	/**
	 * @return the $saveDir
	 */
	public function getSaveDir() {
		return $this->saveDir;
	}

	/**
	 * @return the $srcDir
	 */
	public function getSrcDir() {
		return $this->srcDir;
	}

	/**
	 * @return the $generatedFile
	 */
	public function getGeneratedFile() {
		return $this->generatedFile;
	}

	/**
	 * @return the $templateFile
	 */
	public function getTemplateFile() {
		return $this->templateFile;
	}

	
	/**
	 * Gets the Template DOM Object and init DOM Template by loading 
	 * source package2.xml file
	 * 
	 * @return DOMDocument
	 */
	public function getTemplateDOM() {
		if (!$this->templateDOM) {
			$this->templateDOM = DOMDocument::load ( $this->templateFile );
		}
		return $this->templateDOM;
	}
	
	/**
	 * Gets Magerole by given directory
	 * 
	 * @param string $dir directory
	 * @return string[]
	 */
	public function getRole($dir) {
		// iterate all roles
		foreach ($this->roles as $roleDir => $role) {
			// check if roleDir exists in given directory string
			if (preg_match('/^'.str_replace('/','\/',$roleDir).'/',$dir)) {
				// return role if roleDir exists
				return $role;
			}
		}
		// if role doesn't exist return false
		return 'www';
	}
	
	/**
	 * Sets Arguments given as Parameter
	 * 
	 * @return string[]
	 */
	public function setArguments($argv) {
		return $this->arguments = $argv;
	}
	
	/**
	 * Get Arguments
	 * 
	 * @return string[]
	 */
	public function getArguments($index = null) {
		if ($index == null) {
			return $this->arguments;
		} else {
			if (isset($this->arguments[$index])) {
				return $this->arguments[$index];
			}
		}
	}

	/**
	 * Imports a directory recursively into our contentsItems array
	 * 
	 * @param string $dir directory to import
	 * @return void
	 */
	public function readRecursivDir($dir) {
		// init handle to directory
		$handle = opendir($dir);
		// cut '../../src/' from directory
		$savedir = str_replace($this->srcDir, '', $dir);
		// iterate over directory content
		while ($dirItem = readdir($handle)) {
			// ignore items on blacklist
			if (!in_array($dirItem, $this->_toIgnore)) {
				// check if $dirItem is a directory
				if (is_dir($dir . $dirItem)) {
					// if $dirItem is a directory read $dirItem directory 
					$this->readRecursivDir($dir . $dirItem . '/'); 
				} else {
					// add identifier to $dirItem to declare it as a file
					$dirItem .= '::file';
					// add it to our contentsItem array
					$this->contentsItems[] = $savedir . trim($dirItem);
				} 
			}
			// if $dirItem is an empty directory
			if (($files = @scandir($dir)) && (count($files) <= 2) && ($dirItem != "..")) {
				// add it to our contentsItem array
				$this->contentsItems[] = $savedir;
			}
		}
		// close handle to directory
		closedir($handle);
	} 
	
	/**
	 * Generate new package2.xml by modifying template XML
	 * 
	 * @param string $moduleName 
	 * @return void
	 */
	public function generate($moduleName) {
		// get contentsNode
		$contentsNode = $this->getTemplateDOM()
			->getElementsByTagName("dir")->item(0);
		// init the DOM array we need to build our contentsNode
	    $dirDOM = array();
	    // init $dirElememt
	    $dirElement = null;
	    // iterate contentsItems
		foreach ($this->contentsItems as $item) {
			// get current items roleArray
			$itemRole = $this->getRole($item);
			// get all dirElements of $item striped by slash
			$dirElements = explode('/',$item);
			// set root element to '/' if its empty
			if ($dirElements[0]=='') $dirElements[0]='/';
			// iterate all dir Elements to build our contentsNode
			for ($DOMid=0; $DOMid <= count($dirElements)-1; $DOMid++) {
				// iterate from 0 to current $DOMid to rebuild a unique key
				// for several Nodes to create
				for ($PathId=0; $PathId <= $DOMid; $PathId++) {
					// build a unique key for current Node to create
					$dirElement .= $dirElements[$PathId];
					// build a unique key for parent Node to append it with
					// the current Node
					if ($PathId == $DOMid-1) {
						$dirParentElement = $dirDOM[$dirElement];
					} 
				}
				// check if we are on root to appent the contentsNode
				if ($DOMid == 0) $dirParentElement = $contentsNode;
				// check if nodeElement wasn't created yet
				if (!isset($dirDOM[$dirElement])) {
					// check if it's declared as file
					if (strpos($dirElements[$DOMid],"::file")!==false) {
						// remove file declaration to get the real filename
						$dirElementName = str_replace('::file','',
							$dirElements[$DOMid]);
						// create a file DOMElement
						$dirDOM[$dirElement] = $this->getTemplateDOM()
							->createElement("file");
						// set name attribute to real filename
						$dirDOM[$dirElement]
							->setAttribute('name', $dirElementName);
						// set role attribute by current contentsItems item
						$dirDOM[$dirElement]->setAttribute('role', $itemRole);		
					} else { // if our Element isn't a file it must be a directory
						// set the real directory name
						$dirElementName = $dirElements[$DOMid];
						// create a dir DOMElement
						$dirDOM[$dirElement] = $this->getTemplateDOM()
							->createElement("dir");
						// set attribute name to real directory name
						$dirDOM[$dirElement]
							->setAttribute('name', $dirElements[$DOMid]);
					}
					// append the current parentElement with created Element
					$dirParentElement->appendChild($dirDOM[$dirElement]);		
				}
				// reset unique key for new Element to create
				$dirElement = null;
			}
		}
	}
	
	/**
	 * Starts the generator
	 * 
	 * @return void
	 */
	public function run() {
		// check if there are arguments
		if ($argv = $this->getArguments()) {
			// get source template package from arguments
			$this->templateFile = $this->getArguments(1);
			// get source code folder
			$this->srcDir = $this->getArguments(2);
			// get saveDir from arguments
			$this->saveDir = $this->getArguments(3);
			// get modulename from arguments
			$this->moduleName = $this->getArguments(4);
			// import src directory recursively
			$this->readRecursivDir($this->srcDir);
			// generate contentsNode and add it to templateDOM	
			$this->generate($this->moduleName);
			// save the modified templateDOM
			$this->getTemplateDOM()->save($this->saveDir.$this->generatedFile);
		}	
	}
}