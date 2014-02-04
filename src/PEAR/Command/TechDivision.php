<?php

/**
 * PEAR_Command_TechDivision
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

require_once 'PEAR/Command/Common.php';
require_once 'PEAR/Command/TechDivision/Contents.php';

/**
 * @category   PEAR_Command
 * @package    PEAR_Command_TechDivision
 * @copyright  Copyright (c) 2009 <tw@techdivision.com> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 * @author     Tim Wagner <tw@techdivision.com>
 */
class PEAR_Command_TechDivision extends PEAR_Command_Common
{

    /**
     * The commands implemented in this class.
     * @var array
     */
    public $commands = array(
        'techdivision-contents' => array(
            'summary' => 'Generates contents node in package2.xml',
            'function' => 'doContents',
            'shortcut' => 'tdc',
            'options' => array(
                'templatefile' => array(
                    'shortopt' => 'T',
                    'doc' => 'Path to template package2.xml',
                    'arg' => 'TEMPLATEFILE',
                ),
                'srcdir' => array(
                    'shortopt' => 'S',
                    'doc' => 'Path to source code folder.',
                    'arg' => 'SRCDIR',
                ),
                'destinationdir' => array(
                    'shortopt' => 'D',
                    'doc' => 'Path to destination folder where to save the generated package2.xml',
                    'arg' => 'DESTINATIONDIR',
                ),
                'modulename' => array(
                    'shortopt' => 'M',
                    'doc' => 'Name of the Module.',
                    'arg' => 'MODULENAME',
                )
            ),
            'doc' => '[descfile] Generates the contents node in a template package2.xml file.'
        )
    );

    /**
     * The options passed from the command line when excecuting the command.
     * @var array
     */
    private $options;

    /**
     * Output written after finishing the command execution.
     * @var string
     */
    private $output;
    
    /**
     * Depending on current source folder structure this method generates
     * automatically the contents node in package2.xml
     * 
     * @param $command
     * @param $options
     * @param $params
     * @return void
     */
    public function doContents($command, $options, $params)
    {
    	// get instance of contentsGenerator
    	$gen = new PEAR_Command_TechDivision_Contents();
    	// clear output
    	$this->output = '';
    	// init $result var
    	$result = null;
    	// get options
    	$this->options = $options;

    	// check all options
        if (!isset($this->options['templatefile'])) {
        	$result = new PEAR_Error('No templatefile given. Please use option -T');
        }
        if (!isset($this->options['srcdir'])) {
        	$result = new PEAR_Error('No sourcedir given. Please use option -S');
        }
        if (!isset($this->options['destinationdir'])) {
        	$result = new PEAR_Error('No destinationdir given. Please use option -D');
        }
        if (!isset($this->options['modulename'])) {
			$result = new PEAR_Error('No modulename given. Please use option -M');
        }
        // display error if optioncheck failed
        if ($result instanceof PEAR_Error) {
            return $result;
        }
        // set vars by options
    	$templatefile = $this->options['templatefile'];
        $srcdir = $this->options['srcdir'];
        $destinationdir = $this->options['destinationdir'];
        $modulename = $this->options['modulename'];
		// add slashes to our dirs if there are none
        if (substr($srcdir, -1) != "/") $srcdir .= "/";
        if (substr($destinationdir, -1) != "/") $destinationdir .= "/";
        // check if template file exists
    	if (!file_exists($templatefile)) {
    		$this->raiseError('Could not find templatefile: '.$templatefile); 
    	}
    	// check if sourcecode dir is there
    	if (!is_dir($srcdir)) {
    		$this->raiseError('Could not find codesource dir: '.$srcdir);
    	}
    	// check if destination dir is there
    	if (!is_dir($destinationdir)) {
    		$this->raiseError('Could not find destination save dir: '.$destinationdir);
    	}
    	// set source template for package2.xml
		$gen->setTemplateFile($templatefile);
		// set sourcecode dir
		$gen->setSrcDir($srcdir);
		// set saveDir
		$gen->setSaveDir($destinationdir);
		// set modulename
		$gen->setModuleName($modulename);
		// import src directory recursively
		$gen->readRecursivDir($gen->getSrcDir());
		// generate contentsNode and add it to templateDOM	
		$gen->generate($gen->getModuleName());
		// save the modified templateDOM
		$gen->getTemplateDOM()->save($gen->getSaveDir().$gen->getGeneratedFile());
    	$this->output = 'Generated '.$gen->getGeneratedFile().' successfully.';
    	// delegate the messages to the user interface
        if ($this->output) {
            $this->ui->outputData($this->output, $command);
        }
    }
}