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

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ctable'][] = 'tl_calendar_registrations';

/*
 Link auf Liste setzen
*/
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['registrations'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['registrations'],
    'href' => 'table=tl_calendar_registrations',
    'icon' => 'system/modules/wp_event_ticketsystem/html/reg_user_list.png',
    'button_callback' => array('my_tl_calendar_events', 'registrationsButton')
);
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['exportRegistrations'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['exportRegistrations'],
    'href' => 'key=exportCsv',
    'icon' => 'system/modules/wp_event_ticketsystem/html/exportCSV.gif'
);

/*
 * Rendering
 */

$GLOBALS['TL_DCA']['tl_calendar_events']['list']['sorting']['child_record_callback'] = array('my_tl_calendar_events', 'listEvents');

/*
* Felder
*/
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['evr_register'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['evr_register'],
    'exclude' => true,
    'default' => '1',
    'inputType' => 'checkbox',
    'filter' => true,
    'eval' => array('submitOnChange' => true)
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['evr_slots'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['evr_slots'],
    'exclude' => true,
    'default' => '16',
    'inputType' => 'text',
    'filter' => true,
    'eval' => array('tl_class' => 'w50', 'rgxp' => 'digit','doNotCopy' => true, 'maxlength' => 10, 'mandatory' => true)
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['evr_deadline'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['evr_deadline'],
    'default' => '',
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array('rgxp' => 'date', 'mandatory' => true, 'doNotCopy' => false, 'datepicker' => true, 'tl_class' => 'w50 wizard')
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['startTime'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['startTime'],
	'default'                 => '10:00',
	'exclude'                 => true,
	'filter'                  => true,
	'sorting'                 => true,
	'flag'                    => 8,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'time', 'mandatory'=>true, 'doNotCopy'=>false, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['endTime'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['endTime'],
	'exclude'                 => true,
	'default'				  => '',
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'time', 'doNotCopy'=>false, 'tl_class'=>'w50'),
	'save_callback' => array
	(
		array('tl_calendar_events', 'setEmptyEndTime')
	)
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['startDate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['startDate'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>false, 'datepicker'=>true, 'tl_class'=>'w50 wizard')
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['endDate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['endDate'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>false, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
	'save_callback' => array
	(
		array('tl_calendar_events', 'setEmptyEndDate')
	)
);


/*
* Paletten
*/

$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['__selector__'][] = 'evr_register';

$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] . ';{legend_evr_register},evr_register';

array_insert($GLOBALS['TL_DCA']['tl_calendar_events']['subpalettes'], 2, array
    (
        'evr_register' => 'evr_slots,evr_deadline'
    )
);


class my_tl_calendar_events extends Backend
{
    public function registrationsButton($row, $href, $label, $title, $icon, $attributes)
    {


        // if ($row['evr_register'] == 1) {
        // Check Registrations

        //KG: deaktiviert
        //$objRegistrations = $this->Database->prepare("SELECT id FROM tl_calendar_registrations WHERE pid=?")->execute($row['id']);

        //if ($objRegistrations->numRows > 0) {
        return '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . $this->generateImage('system/modules/wp_event_ticketsystem/html/reg_user_list.png', 'Anmeldungen') . '</a> ';
        //}
        //else {
        //    return $this->generateImage('system/modules/wd_event_registrations/html/reg_user_list_inaktiv.png', $label);
        //}
        //  }
    }

    public function listEvents($arrRow)
    {
        $time = time();
        $key = ($arrRow['published'] && ($arrRow['start'] == '' || $arrRow['start'] < $time) && ($arrRow['stop'] == '' || $arrRow['stop'] > $time))
            ? 'published' : 'unpublished';
        $span = Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

        if ($span > 0) {
            $date = $this->parseDate($GLOBALS['TL_CONFIG'][($arrRow['addTime'] ? 'datimFormat'
                : 'dateFormat')], $arrRow['startTime']) . ' - ' . $this->parseDate($GLOBALS['TL_CONFIG'][($arrRow['addTime']
                ? 'datimFormat'
                : 'dateFormat')], $arrRow['endTime']);
        }
        elseif ($arrRow['startTime'] == $arrRow['endTime']) {
            $date = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $arrRow['startTime']) . ($arrRow['addTime']
                ? ' (' . $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $arrRow['startTime']) . ')' : '');
        }
        else {
            $date = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $arrRow['startTime']) . ($arrRow['addTime']
                ? ' (' . $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $arrRow['startTime']) . ' - ' . $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $arrRow['endTime']) . ')'
                : '');
        }
       
/*
       echo '<pre>';
       print_r ($arrRow);
       echo '</pre>';
*/

        $objEventreg = new EventRegistration();
        $arrSlotStats = $objEventreg->getSlotStatsByEventId($arrRow['id']);

        //KG: deaktiviert
        //$objRegistrations = $this->Database->prepare("SELECT id FROM tl_calendar_registrations WHERE pid=?")->execute($arrRow['id']);
        
        if ($arrRow['evr_register']) {
            return '
              <div class="cte_type ' . $key . '" '.(($arrRow['evr_deadline']<=time() || ($arrSlotStats['complete']>=$arrSlotStats['allSlots'])) ? 'style="color:gray"' : '') .'><strong>' . $arrRow['title'] . '</strong> - ' . $date . '</div>
              <div class=" block" >' . $arrSlotStats['complete'] . ' von max. ' . $arrSlotStats['allSlots'] . ' Teilnehmern angemeldet</div>';
        } else {
            return '
            <div class="cte_type ' . $key . '"><strong>' . $arrRow['title'] . '</strong> - ' . $date . '</div>
            <div class="limit_height' . (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h52' : '') . ' block">
            ' . (($arrRow['details'] != '') ? $arrRow['details'] : $arrRow['teaser']) . '
            </div>' . "\n";
        }
    }


}

?>