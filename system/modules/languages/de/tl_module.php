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
 * @copyright  Leo Feyer 2005-2011
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Newsletter
 * @license    LGPL
 * @filesource
 */


/**
 * Fields
 */



/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_module']['evr_register_mail_text']   = array('Ihre Anmeldung auf %s', "##link##\n##domain##\n##firstname##\n##lastname##\n##street##\n##plz_city##\n##phone##\n##email##\n##message##\n");
/*$GLOBALS['TL_LANG']['tl_module']['evr_register_mail_text']   = array('Sehr geehrte/r Frau/ Herr %s', "##lastname##,\n\n\nvielen Dank für Ihre Anmeldung zum Kurs ##link## über unsere Webseite http://iq-wissen.de.\n\nBitte klicken Sie auf den folgenden Link, um Ihre Anmeldung noch einmal zu bestätigen.\n\n##link##\n\nBei Fragen stehen wir Ihnen gern zur Verfügung.\n\nMit freundlichen Grüßen\n\nIhr IQ-Team");*/

$GLOBALS['TL_LANG']['tl_module']['legend_evt_register'] = 'Registrierung';

$GLOBALS['TL_LANG']['tl_module']['evr_register_mail_cc'] = array('Kopie der Bestätigung', 'Eine Kopie der Bestätigungs-Email wird an diese Adresse versendet');
$GLOBALS['TL_LANG']['tl_module']['evr_register_mail'] = array('Sehr geehrte/r Frau/ Herr %s', "##lastname##,\n\n\nvielen Dank für Ihre Anmeldung zum Kurs ##link## über unsere Webseite http://iq-wissen.de.\n\nBitte klicken Sie auf den folgenden Link, um Ihre Anmeldung noch einmal zu bestätigen.\n\n##link##\n\nBei Fragen stehen wir Ihnen gern zur Verfügung.\n\nMit freundlichen Grüßen\n\nIhr IQ-Team");
/*$GLOBALS['TL_LANG']['tl_module']['evr_register_mail'] = array('Bestätigungs-Email', 'Diese Marker werden ersetzt: ##link## ##domain## ##event## ##date## ##time## ##firstname## ##lastname## ##street## ##plz_city## ##phone## ##email## ##message##');*/

?>