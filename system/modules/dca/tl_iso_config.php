<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * PHP version 5
 * @copyright  Felix peters - 2011
 * @author     Felix peters - Wichteldesign
 * @package    wd
 * @license    -
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['evr_product_types_registration'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['evr_product_types_registration'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_iso_producttypes.name',
	'eval'                    => array('multiple'=>true, 'size'=>8, 'tl_class'=>'w50h w50', 'includeBlankOption'=> true),
);
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['evr_product_types_send_ticket'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['evr_product_types_send_ticket'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'			  => 'tl_iso_producttypes.name',
	'eval'                    => array('multiple'=>true, 'size'=>8, 'tl_class'=>'w50h w50', 'includeBlankOption'=> true),
);
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['evr_ticketmail_subject'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_iso_config']['evr_ticketmail_subject'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array( 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['evr_ticketmail_text'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_iso_config']['evr_ticketmail_text'],
    'exclude' => true,
    'inputType' => 'textarea',
    'eval' => array('rte' => 'tinyMCE', 'tl_class' => 'clr')
);


// Produkttyp einschränken

$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] .= ';{legend_eventregistration},evr_product_types_registration,evr_product_types_send_ticket,evr_ticketmail_subject,evr_ticketmail_text';