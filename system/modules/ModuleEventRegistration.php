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
 * Class ModuleEventRegistrationList
 *
 * @copyright  Felix peters - 2011
 * @author     Felix peters - Wichteldesign
 * @package    Controller
 */
class ModuleEventRegistration extends Module
{

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### EVENT REGISTRATION ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'typolight/main.php?do=modules&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected $strTemplate = 'mod_eventRegistration';

    /**
     * Generate module
     */
    protected function compile()
    {

        // Catch token
        if ($this->Input->get('token')) {
            $this->activateRegistration();
            return;
        }

        // Render Registration
        $event = $this->Input->get('event');

        try {
            // Check Event is valid
            $objEvent = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE id=? OR alias=?")->limit(1)->execute($event, $event);
            if ($objEvent->numRows > 0) {


                // Check Event-Status
                $objRegistrations = $this->Database->prepare("SELECT id FROM tl_calendar_registrations WHERE pid=? and active=1")->execute($objEvent->id);
                $status = EventRegistration::checkEventStatus($objEvent->Row(), $objRegistrations->numRows);
                if ($status == 'over' || $status == 'full') {
                    throw new Exception("Event::Status: " . $status);
                }

                $formData = $this->buildForm();

                // Build Form
                if ($formData) {
                    $this->generateRegistration($formData, $objEvent);
                }

                $this->Template->event = $objEvent->Row();


            } else {
                throw new Exception("Invalid Event");
            }
        } catch (Exception $e) {
            // echo $e->getMessage();
            $this->Template->error = $GLOBALS['TL_LANG']['MSC']['evr_error'];
        }


    }

    protected function buildForm()
    {
        $submit_form = false;
        $dca = array
        (
            'firstname' => array
            (
                'label' => 'Vorname',
                'inputType' => 'text',
                'eval' => array('mandatory' => true)
            ),
            'lastname' => array
            (
                'label' => 'Nachname',
                'inputType' => 'text',
                'eval' => array('mandatory' => true)
            ),
            'street' => array
            (
                'label' => 'Straße / Nr.',
                'inputType' => 'text',
                'eval' => array('mandatory' => true)
            ),
            'plz_city' => array
            (
                'label' => 'PLZ / Stadt',
                'inputType' => 'text',
                'eval' => array('mandatory' => true)
            ),
            'email' => array
            (
                'label' => 'E-Mail',
                'inputType' => 'text',
                'eval' => array('mandatory' => true, 'rgxp' => 'email')
            ),
            'phone' => array
            (
                'label' => 'Telefon',
                'inputType' => 'text',
                'eval' => array('mandatory' => true)
            ),
            'message' => array
            (
                'label' => 'Bemerkung',
                'inputType' => 'textarea',
                'eval' => array()
            ),

            'datenschutz' => array
            (
                'label' => '',
                'inputType' => 'checkbox',
                'options' => array('datenschutz' => 'Die {{link::datenschutz}} habe ich zur Kenntnis genommen und bin mit deren Inhalt und Geltung einverstanden.'),
                'eval' => array('mandatory' => true)
            ),
            'persoenlicheDaten' => array
            (
                'label' => '',
                'inputType' => 'checkbox',
                'options' => array('persDaten' => 'Ich bin damit einverstanden, dass meine persönlichen Angaben elektronisch gespeichert, verarbeitet und zu Informations- und Werbezwecken verwendet werden.'),
                'eval' => array('mandatory' => true)
            ),

            'submit' => array
            (
                'label' => 'Anmelden',
                'inputType' => 'submit'
            )
        );

        $frm = new Formular('formEventRegistration');
        $frm->setDCA($dca);

        $frm->setConfig('generateFormat', '<div><div class="label">%label</div> %field %error </div>');
        $frm->setConfig('attributes', array('tableless' => true));

        // Check Form
        if ($frm->isSubmitted() && $frm->validate()) {
            $submit_form = $frm->getData();
        }
        $this->Template->form = $frm->parse();

        return $submit_form;
    }

    protected function generateRegistration($data, $objEvent)
    {
        $strToken = md5(uniqid(mt_rand(), true));

        // Put in Database
        $arrData = array(
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'street' => $data['street'],
            'plz_city' => $data['plz_city'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'],
            'registrationTime' => time(),
            'tstamp' => time(),
            'pid' => $objEvent->id,
            'token' => $strToken
        );

        $objInsert = $this->Database->prepare("INSERT INTO tl_calendar_registrations %s")->set($arrData)->execute();

        // Generate Emails

        $objEmail = new Email();

        $strText = $this->evr_register_mail;

        // Replace Tags
        foreach ($arrData as $field => $value) {
            $strText = str_replace('##' . $field . '##', $value, $strText);
        }
        $strText = str_replace('##domain##', $this->Environment->host, $strText);
        $strText = str_replace('##event##', $objEvent->title, $strText);
        $strText = str_replace('##date##', $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objEvent->startDate), $strText);
        $strText = str_replace('##time##', $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $objEvent->startTime), $strText);
        $strText = str_replace('##link##', $this->Environment->base . $this->Environment->request . (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos($this->Environment->request, '?') !== false)
            ? '&' : '?') . 'token=' . $strToken, $strText);

        $objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
        $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
        $objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['evr_register_mail_subject'], $this->Environment->host);
        $objEmail->text = sprintf("Sehr geehrte/r Frau/ Herr %s,\n\n\nvielen Dank für Ihre Anmeldung zum Kurs %s über unsere Webseite http://iq-wissen.de.\n\nBitte klicken Sie auf den folgenden Link, um Ihre Anmeldung noch einmal zu bestätigen.\n\n%s\n\nBei Fragen stehen wir Ihnen gern zur Verfügung.\n\nMit freundlichen Grüßen\n\nIhr IQ-Team", $arrData['lastname'], $objEvent->title, $this->Environment->base . $this->Environment->request . (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos($this->Environment->request, '?') !== false) ? '&' : '?') . 'token=' . $strToken);
        /*$objEmail->text = $strText;*/
        $objEmail->sendTo($data['email']);
        if ($this->evr_register_mail_cc) {
            $objEmail->sendTo($this->evr_register_mail_cc);
        }

        // Redirect
        $objJump = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")->limit(1)->execute($this->jumpTo);
        $this->redirect($this->generateFrontendUrl($objJump->Row()));

    }

    protected function activateRegistration()
    {

        // Check Registration
        $objRegistration = $this->Database->prepare("SELECT * FROM tl_calendar_registrations WHERE token = ?")->execute($this->Input->get('token'));
        if ($objRegistration->numRows > 0) {

            // Activate
            $this->Database->prepare("UPDATE tl_calendar_registrations SET active=1, token='' WHERE token=?")
                ->execute($this->Input->get('token'));
            // Log it
            $this->log($objRegistration->email . ' has registered', 'ModuleEventRegistration activateRegistration()', TL_EVENT_REGISTRATION);

            // Tell it to the user - just use the error Box, maybe to change
            $this->Template->error = $GLOBALS['TL_LANG']['MSC']['evr_activate'];


        } else {
            $this->Template->error = $GLOBALS['TL_LANG']['ERR']['evr_invalidToken'];
        }

    }

}

?>