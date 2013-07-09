<?php

/**
 * StructuredData Magento Extension
 * @package Wsu_StructuredData
 * @copyright (c) 2013+ Jeremy Bass
 * @author Jeremy Bass <jeremy.bass@wsu.edu>
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>
 **/
class Wsu_StructuredData_Model_Source_Strongid
{
	public function toOptionArray()
	{
		$strongid = array();
		$strongid[] = array('value'=>'gtin8', 'label'=>'GTIN-8 / EAN_UCC-8');
		$strongid[] = array('value'=>'gtin14', 'label'=>'GTIN-14');
		$strongid[] = array('value'=>'ean13', 'label'=>'EAN_UCC-13');
		return $strongid;
	}
}