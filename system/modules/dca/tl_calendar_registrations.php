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


/**
 * Table tl_calendar_registrations
 */
$GLOBALS['TL_DCA']['tl_calendar_registrations'] = array
(

    // Config
    'config' => array
    (
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'ptable' => 'tl_calendar_events',
        'doNotCopyRecords' => true,
        'closed' => false
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode' => 2,
            'fields' => array('registrationTime DESC', 'id DESC'),
            'panelLayout' => 'filter;sort,search,limit'
        ),
        'label' => array
        (
            'fields' => array('firstname', 'lastname', 'registrationTime'),
            'format' => ' <b>%s %s</b><span style="color:#b3b3b3; padding-right:3px; padding-bottom: 2px;"> [%s]</span>',
            'label_callback' => array('tl_calendar_registrations', 'label_callback')
        ),
        'global_operations' => array
        (

        ),
        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'copy' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif'
            ),
                       'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif'
            ),
            'print_coupon' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['print_coupon'],
                'href' => 'key=print_coupon',
                'icon' => 'system/modules/isotope/html/document-pdf-text.png',
                'attributes' => 'target="_blank"'
            ),
            'send_coupon' => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['send_coupon'],
                'href' => 'key=send_coupon',
                'icon' => 'system/modules/wp_event_ticketsystem/html/email_go.png'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__' => array(''),
        'default' => 'regNr,registrationTime,firstname,lastname,plz_city,street,email,phone,message,manual,manual_storno'
    ),

    // Subpalettes
    'subpalettes' => array
    (
        '' => ''
    ),

    // Fields
    'fields' => array
    (
        'regNr' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['regNr'],
            'inputType' => 'text',
            'eval' => array('maxlength' => 255, 'tl_class' => 'w50', 'unique' => true),
            'save_callback' => array
            (
                array('EventRegistration', 'regNr_save_callback')
            )
        ),
        'registrationTime' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['registrationTime'],
            'inputType' => 'text',
            'default' => time(),
            'filter' => true,
            'sorting' => true,
            'flag' => 6,
            'eval' => array('rgxp' => 'datim', 'readonly' => true, 'tl_class' => 'w50')
        ),
        'firstname' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['firstname'],
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50')
        ),
        'lastname' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['lastname'],
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50')
        ),
        'street' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['street'],
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50')
        ),
        'plz_city' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['plz_city'],
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50')
        ),
        'email' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['email'],
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'rgxp' => 'email', 'maxlength' => 255, 'tl_class' => 'w50')
        ),
        'phone' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['phone'],
            'inputType' => 'text',
            'eval' => array('mandatory' => false, 'maxlength' => 255, 'tl_class' => 'w50')
        ),
        'message' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['message'],
            'inputType' => 'textarea',
            'eval' => array()
        ),
        'manual' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['manual'],
            'inputType' => 'checkbox',
            'filter' => true,
            'default' => true,
            'eval' => array('tl_class' => 'w50')
        ),
        'manual_storno' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['manual_storno'],
            'inputType' => 'checkbox',
            'filter' => true,
            'default' => true,
            'eval' => array('tl_class' => 'w50')
        ),
        'isSend' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['isSend'],
            'inputType' => 'checkbox',
            'eval' => array()
        ),
        'sendTime' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_registrations']['sendTime'],
            'inputType' => 'text',
            'default' => time(),
            'flag' => 6,
            'eval' => array('rgxp' => 'datim', 'readonly' => true, 'tl_class' => 'w50')
        ),
    )
);


// Diable wenn kein Event ausgewählt ist
if ($this->Input->get('do') != 'calendar') {
    $GLOBALS['TL_DCA']['tl_calendar_registrations']['config']['closed'] = true;
    unset($GLOBALS['TL_DCA']['tl_calendar_registrations']['list']['operations']['copy']);
}

class tl_calendar_registrations extends Backend
{
    public function label_callback($row, $label)
    {
        // Get Event
        $objEvent = $this->Database->prepare("SELECT title, startTime, addTime, (SELECT title FROM tl_calendar_places WHERE id = evr_place) as place FROM tl_calendar_events WHERE id=?")->limit(1)->execute($row['pid']);
        // Get Product
        $objProduct = IsotopeFrontend::getProduct($row['productId']);
        // Get Order Status
        $objOrder = new IsotopeOrder();
        if ($objOrder->findBy('uniqid', $row['orderId'])) {
            $link = 'contao/main.php?do=iso_orders&act=edit&id=' . $objOrder->id;
            $style = '; padding: 2px 4px; margin-right: 5px; color: #fff; border-radius: 4px;';
            if ($objOrder->status != 'complete') {
                $color = '#C55';
            } else {
                $color = '#8AB858';
            }
        }

        if ($objEvent->numRows > 0) {

            // Order Status
            $strStatus = '<div style="padding:5px 0;">';
            if ($row['manual']) {
                $strStatus .= '<span style="background: #004065; padding: 2px 4px; margin-right: 5px; color: #fff; border-radius: 4px;">Manuell</span>';
            } else {
                $strStatus .= '<a href="' . $link . '" style="background: ' . $color . $style . '">' . $objOrder->status . '</a>';
            }
            // Orer Time
            $strOrdertime = '<li><b>Bestellt am: </b>' . $this->parseDate($GLOBALS['TL_CONFIG'][datimFormat], $row['registrationTime']) . '</li>';
            // Send Status
            if ($row['isSend']) {
                $strSend = '<li><b>Versendet am: </b>' . $this->parseDate($GLOBALS['TL_CONFIG'][datimFormat], $row['sendTime']) . '</li>';
            }
            // Event
            $strEvent = '<li><b>Event: </b>' . $objEvent->title . '</li><li><b>Ticket: </b>' . $objProduct->name . ' - ' . $this->parseDate($GLOBALS['TL_CONFIG'][($objEvent->addTime ? 'datimFormat' : 'dateFormat')], $objEvent->startTime) . ')</li>';

            $strReturn = '<div>' . $strStatus . '<b>' . $row['firstname'] . ' ' . $row['lastname'] . '</b></div>';
            $strReturn .= '<ul>' . $strEvent;
            $strReturn .= $strOrdertime;
            $strReturn .= $strSend;
            $strReturn .= '</ul>';
        }

        return $strReturn;

    }

}


?>