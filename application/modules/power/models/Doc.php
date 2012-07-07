<?php
/**
 * Doc.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA Power.
 *
 * BBA Power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA Power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA Power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Doc Model.
 *
 * @category   BBA_Power
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Doc extends ZendSF_Model_Abstract
{
    public static $mimeMap = array(
        'doc'   => 'application/msword',
        'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'pdf'   => 'application/pdf',
        'png'   => 'image/png',
        'xls'   => 'application/vnd.ms-excel',
        'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    public static $docClient = array(
        'client_docLoa'   => 'Letter of Authority'
    );

    public static $docContract = array(
        'contract_docAnalysis'              => 'Analysis',
        'contract_docTermination'           => 'Termination',
        'contract_docContractSearchable'    => 'Contract (Searchable)',
        'contract_docContractSignedClient'  => 'Contract (Client Signed)',
        'contract_docContractSignedBoth'    => 'Contract (Both Signed)'
    );

    public $docDir = '/../bba-power-docs/';

    public function getDocFile($dir, $file)
    {
        $file = realpath(
            APPLICATION_PATH . $this->docDir . $dir . '/' . $file
        );

        return array(
            file_get_contents($file),
            self::$mimeMap[pathinfo($file, PATHINFO_EXTENSION)]
        );
    }

    public static function addUploadFilter($docs, $form, $id)
    {
        foreach ($docs as $key => $vaule) {
            self::createUploadFilter(
                $form->getElement($key),
                $id
            );
        }
    }

    /**
     * Adds a filter to the doc upload form.
     * Renames file to row <id>_<timestamp>_<original filename>.
     *
     * @param Zend_Form_Element_File $element
     * @param int $id
     * @return Power_Model_Contract
     */
    public static function createUploadFilter(Zend_Form_Element_File $element, $id)
    {
        $ts = Zend_Date::now();

        $log = Zend_Registry::get('log');
        $log->info($element);

        $newDocFileName = join('_', array(
            sprintf("%06d", $id),
            $ts->toString('yyyyMMdd_HHmmss'),
            str_replace(' ', '_', $_FILES[$element->getId()]['name'])
        ));

        $element->addFilter('Rename', $newDocFileName);

        return $element;
    }
}