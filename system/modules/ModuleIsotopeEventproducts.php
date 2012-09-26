<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 *
 * PHP version 5
 * @copyright  Intelligent Spark 2011
 * @author     Fred Bliss <http://www.intelligentspark.com>
 */

class ModuleIsotopeEventproducts extends ModuleIsotopeProductList
{

    /**
     * Nicht lauffähig, nur ein Test
     *
     */

    protected function findProducts()
    {
        //Todo: Buggy, tut noch nicht vollständig - vorerst nicht gebraucht

        $objEvent = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE (id=? OR alias=?)")
            ->limit(1)
            ->execute((is_numeric($this->Input->get('events')) ? $this->Input->get('events') : 0), $this->Input->get('events'));


        $objProductData = $this->Database->prepare(IsotopeProduct::getSelectStatement() . "
       		WHERE p1.eventcode=?")
            ->execute($objEvent->id);

        return IsotopeFrontend::getProducts($objProductData);

    }
}

