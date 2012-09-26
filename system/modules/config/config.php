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


$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_registrations';

$be_mods = array(
    'seminare' => array(
        'registrations' => array(
            'tables' => array('tl_calendar_registrations'),
            'icon' => 'system/modules/wp_event_ticketsystem/html/reg_user_list.png',
            'print_coupon' => array('EventRegistration', 'buildCouponPdf'),
            'send_coupon' => array('EventRegistration', 'sendCouponMail')
        )
    )
);

$GLOBALS['BE_MOD']['content']['calendar']['exportCsv'] = array('EventRegistration', 'exportRegsToCsv');
$GLOBALS['BE_MOD']['content']['calendar']['print_coupon'] = array('EventRegistration', 'buildCouponPdf');
$GLOBALS['BE_MOD']['content']['calendar']['send_coupon'] = array('EventRegistration', 'sendCouponMail');


$GLOBALS['BE_MOD']['content']['calendar']['stylesheet'] = 'system/modules/wp_event_registrations/html/style.css';
$GLOBALS['BE_MOD']['seminare']['registrations']['stylesheet'] = 'system/modules/wp_event_registrations/html/style.css';

array_insert($GLOBALS['BE_MOD'], 0, $be_mods);


// Modules
$GLOBALS['FE_MOD']['isotope']['iso_eventproducts'] = 'ModuleIsotopeEventproducts';


// HOOKs:

$GLOBALS['ISO_HOOKS']['postCheckout'][] = array('EventRegistration', 'postCheckoutHook');
$GLOBALS['ISO_HOOKS']['preCheckout'][] = array('EventRegistration', 'preCheckoutHook');
$GLOBALS['ISO_HOOKS']['generateProduct'][] = array('EventRegistration', 'generateProductHook');
$GLOBALS['ISO_HOOKS']['compileCart'][] = array('EventRegistration', 'compileCartHook');



?>