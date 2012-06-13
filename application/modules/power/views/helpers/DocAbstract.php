<?php
/**
 * DocAbstract.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA.
 *
 * BBA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DocAbstract View Helper.
 *
 * @category   BBA
 * @package    Power
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class Power_View_Helper_DocAbstract extends Zend_View_Helper_HtmlElement
{
    /**
     * @var string
     */
    protected $_docDir;

    /**
     * @var string
     */
    protected $_currentFile;

    /**
     * @var int
     */
    protected $_id;

    /**
     * @var array
     */
    protected $_attribs;

    /**
     * @var array
     */
    protected $_files = array();

    /**
     * Gets all the files associated with a database record.
     *
     * @return array
     */
    public function getFileList()
    {
        return $this->_files;
    }

    /**
     * Sets all the files associated with a database record.
     *
     * @return array
     */
    public function setFileList()
    {
        $fileArray = array();
        $sort = array();

        $dir = new DirectoryIterator(realpath(
            APPLICATION_PATH . $this->_docDir
        ));

        foreach ($dir as $file) {

            $filename = $file->getFilename();

            if ($file->isDot() || !ZendSF_Utility_String::startsWith(sprintf("%06d", $this->_id), $filename)) {
                continue;
            }

            $filePieces = $this->getFilePieces($filename);

            $fileArray[] = ZendSF_Utility_Array::mergeMultiArray($filePieces);

            $sort[] = $filePieces['datetime']['timestamp'];
        }

        array_multisort($sort, SORT_DESC, $fileArray);

        $this->_files = $fileArray;

        return $this;
    }

    /**
     * Sets the inner directory to the id of the database record.
     * Not sure if we want to keep this.
     *
     * @param int $id
     * @return Power_View_Helper_DocAbstract
     */
    public function setDocDir(int $id)
    {
        $id = sprintf("%06d", $id);
        $this->_docDir = $this->_docDir . '/' . $id;

        return $this;
    }

    /**
     * Split the filename into it's four elements
     * <id>, <datetime>, <orginal name>, <filename>
     *
     * @param type $filename
     * @return type
     */
    public function getFilePieces($filename)
    {
        $filePieces = explode('_', $filename);

        return array(
            'id'        => $filePieces[0],
            'datetime'  => $this->getDateTime(
                $filePieces[1] . $filePieces[2]
            ),
            'normalise' => $this->normaliseFilename($filePieces),
            'filename'  => $filename
        );
    }

    /**
     * Normalises the file name by returning the orginal file name.
     *
     * @param array $filePieces
     * @return string
     */
    public function normaliseFilename(array $filePieces)
    {
        $file = array_splice($filePieces, 3);

        return implode(' ', $file);
    }

    public function getDateTime($dateTime)
    {
        $date = new Zend_Date(
            $dateTime,
            'yyyyMMddHHmmss'
        );

        return array(
            'timestamp' => $date->getTimestamp(),
            'date'      => $date->toString(Zend_Date::DATE_FULL),
            'time'      => $date->toString(Zend_Date::TIMES)
        );
    }

    public function setCurrentFile()
    {
        $this->setFileList();

        if (count($this->_files) > 0) {
            $this->_currentFile = $this->_files[0];
        }
        
        return $this;
    }

    /**
     * Get the current file status and contruct an HTML link
     * to view it.
     *
     * @return string
     */
    public function getCurrentFile()
    {
        if (!$this->_currentFile) {
            return 'No Documents Have Been Uploaded';
        }

        if (is_file(APPLICATION_PATH . $this->_docDir . '/' . $this->_currentFile['filename'])) {
            $link = $this->makeButton($this->_currentFile);
            return '<span>Stored on:&nbsp;' . $this->_currentFile['date'] . ' ' . $this->_currentFile['time']
                . '<br />' . $this->_currentFile['normalise']
                . $link . '</span>' . self::EOL;
        } else {
            return 'No Documents Have Been Uploaded' . self::EOL;
        }
    }

    /**
     * Contructs a link button to view pdf view.
     *
     * @param array $file
     * @return string
     */
    public function makeButton($file)
    {
        if ($this->_attribs) {
            $attribs = $this->_attribs;
        } else {
            $attribs = array();
        }

        $className = $this->_getNamespace();

        $element = 'a';
        $attribs['href'] = $this->view->url(array(
            'module'        => 'power',
            'controller'    => $this->view->request()->getControllerName(),
            'action'        => 'doc',
            'view'          => $file['filename'],
            'doc'           => lcfirst(end($className))
        ));
        $attribs['target'] = '_blank';
        $attribs['class'] = 'button view_button';

        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
             . 'View'
             . '</' . $element . '>'
             . self::EOL;
    }

    /**
     * Gets the class namespace.
     *
     * @return array
     */
    protected function _getNamespace()
    {
        $ns = explode('_', get_class($this));
        return $ns;
    }
}