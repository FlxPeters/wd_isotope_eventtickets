<?php
/**
 * Created by JetBrains PhpStorm.
 * User: felixpeters
 * Date: 19.09.11
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */

class EventRegistration extends Controller
{

    public function getSlotStatsByEventId($id)
    {
        $this->import('Database');

        // Get regular registrations
        $objRegistrations = $this->Database->prepare("SELECT regNr, status
                        FROM tl_calendar_registrations LEFT JOIN tl_iso_orders ON (tl_calendar_registrations.orderId = tl_iso_orders.uniqid)
                        WHERE tl_calendar_registrations.pid=?")->execute($id);

        // Get manual registrations
        $objManualRegistrations = $this->Database->prepare("SELECT regNr
                                FROM tl_calendar_registrations
                                WHERE tl_calendar_registrations.pid=? AND manual=? AND manual_storno=?")->execute($id, true,false);

        $objEvent = $this->Database->prepare("SELECT evr_slots, evr_deadline
                        FROM tl_calendar_events WHERE id = ?")->execute($id);

        $arrResult['all'] = $objRegistrations->numRows;
        $arrResult['dateOver'] = $objEvent->evr_deadline < time();
        $arrResult['complete'] = 0;
        $arrResult['incomplete'] = 0;

        // Process regular registrations
        while ($objRegistrations->next()) {
            if ($objRegistrations->status == 'on_hold' OR $objRegistrations->status == 'cancelled' OR $objRegistrations->status == '') {
                $arrResult['incomplete'] = $arrResult['incomplete'] + 1;
            } else {
                $arrResult['complete'] = $arrResult['complete'] + 1;
            }
        }
        // Process manual registrations
        while($objManualRegistrations->next()) {
            $arrResult['complete'] = $arrResult['complete'] + 1;
        }

        $arrResult['allSlots'] = $objEvent->evr_slots;
        $arrResult['freeSlots'] = $objEvent->evr_slots - $arrResult['complete'];
        $arrResult['soldOut'] = ($objEvent->evr_slots <= $arrResult['complete']);

        return $arrResult;
    }

    /**
     *
     * Maximale ANnzahl an möglichen Tickets per Dropdown bereitstellen
     * Variable anbieten um ausgebuchte Produkte zu Sperren
     *
     * @param $objTemplate
     * @param $objProduct
     * @return mixed
     */
    public function generateProductHook($objTemplate, $objProduct)
    {
        $this->import('Database');
        $this->import('Isotope');       
        
        $arrAllowedProductTypes = deserialize($this->Isotope->Config->evr_product_types_registration);             
        
        //check if Allowd Product Type and Eventcode is set
        if (in_array($objProduct->type, $arrAllowedProductTypes) && $objProduct->eventcode) {     
	        
	        $objEvent = $this->Database->prepare("
	            SELECT evr_slots, evr_deadline FROM tl_calendar_events WHERE id = ?
	        ")->execute($objProduct->eventcode);	
	
	        $arrSlotStats = $this->getSlotStatsByEventId($objProduct->eventcode);
	
	        //echo '<pre>';print_r($arrSlotStats);
	        //die();
	
	        // Build Dropdown
	        $options = '';
	        for ($i = 1; $i <= $arrSlotStats['freeSlots']; $i++) {
	            $options .= '<option value="' . $i . '">' . $i . '</option>';
	        }
	        // Fill Template
	        $objTemplate->soldOut = $arrSlotStats['soldOut'];
	        $objTemplate->dateOver = $arrSlotStats['dateOver'];
	        $objTemplate->freeSlots = $arrSlotStats['freeSlots'];
	        $objTemplate->slotOptions = $options;
        }

        return $objTemplate;
    }

    public function compileCartHook($objModule, $objTemplate, $arrProductData, $arrSurcharges)
    {
        // TODO: Add Select for Quantity

        //var_dump($objTemplate);
    }

    /**
     * In der Session die Aufsplitung der Bestellung ablegen um Anmeldungen zu generieren
     *
     * @param $objOrder
     * @param $objCart
     */
    public function preCheckoutHook($objOrder, $objCart)
    {
    	$this->Import('Isotope');    
	    // Collect Data
        $arrProducts = $objCart->getProducts();
        $arrRegistrations = array();
        $arrAllowedProductTypes = deserialize($this->Isotope->Config->evr_product_types_registration); 

        // Loop over Products in Cart
        foreach ($arrProducts as $objProduct) {  
            // Only add when Type is Allowed an Eventcode is set
            if (in_array($objProduct->type, $arrAllowedProductTypes) && $objProduct->eventcode) {
                // Build Registrarions ans Store in Session
                // One Entry for Each Ticket and Quantity
                for ($i = 0; $i < $objProduct->quantity_requested; $i++) {
                    $arrRegistrations[] = array(
                        'pid' => $objProduct->eventcode,
                        'productId' => $objProduct->id,
                        'productType' => $objProduct->type
                    );
                }
            }
        }
        // Save in Session
        $this->Session->set('checkoutRegistrations', $arrRegistrations);
    }

    /**
     * Anmeldungen aus der Session holen und generieren
     *
     * @param $objOrder
     * @param $arrIds
     * @param $arrData
     */
    public function postCheckoutHook($objOrder, $arrIds, $arrData)
    {
    	$this->Import('Isotope');
        $this->import('Database');
        
        
        // Get Registrations from Session und clear
        $arrRegistrations = $this->Session->get('checkoutRegistrations');
        $this->Session->remove('checkoutRegistrations');

        // Save for Sendstatus Update
        $regIds = array();
        $arrFiles = array();
        
        // Build registrations
        foreach ($arrRegistrations as $k => $registration) {
            $arrSet = array();
            $arrSet = array(
                'pid' => $registration['pid'],
                'tstamp' => time(),
                'registrationTime' => time(),
                'firstname' => $arrData['billing_firstname'],
                'lastname' => $arrData['billing_lastname'],
                'email' => $arrData['billing_email'],
                'phone' => $arrData['billing_phone'],
                'street' => $arrData['billing_street_1'],
                'plz_city' => $arrData['billing_postal'] . ' ' . $arrData['billing_city'],
                'message' => $arrData['order_id'],
                'orderId' => $objOrder->uniqid,
                'productId' => $registration['productId']
            );
            
            $objReg = $this->Database->prepare("INSERT INTO tl_calendar_registrations %s")->set($arrSet)->execute();
            
            // Add a Registrationnumber based on Registration and number of Tickets
            $regId = $objReg->insertId;
            $regIds[] = $regId;            
            $this->Database->prepare("UPDATE tl_calendar_registrations SET regNr=? WHERE id = ?")->execute($this->generateRegistrationId($regId), $regId);
            
            // Build PDF
            $arrAllowedProductTypesForEmail = deserialize($this->Isotope->Config->evr_product_types_send_ticket);
            if (in_array($registration['productType'], $arrAllowedProductTypesForEmail)) {
	            $arrFiles[] = $this->generateCouponPDF($regId, false, $k, count($arrRegistrations));
            }           
            
        }

        // Just Send if Paid and any Product type is Allowed to send by Email
        if ($objOrder->status == 'complete' AND count($arrFiles) > 0) {
            // Send Ticket Mail
            $blnSend = $this->sendMail($arrData['billing_email'], $arrFiles, array(
                'firstname' => $arrData['billing_firstname'],
                'lastname' => $arrData['billing_lastname'],
            ));

            if ($blnSend) {
                // Set all Tickets as send
                $arrSet = array(
                    'isSend' => true,
                    'sendTime' => time()
                );
                foreach ($regIds as $registration) {
                    $this->Database->prepare("UPDATE tl_calendar_registrations %s WHERE id = ?")->set($arrSet)->execute($registration);
                }
            }
        }
    }


    /**
     * Backend Darstellung Events
     *
     * @param $arrEvents
     * @param $arrCalendars
     * @param $intStart
     * @param $intEnd
     * @param Module $objModule
     * @return $arrEvents
     */

    public function addSeminarInfo($arrEvents, $arrCalendars, $intStart, $intEnd, Module $objModule)
    {
        // ToDo: Aufräumen

        $objJump = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")->execute($objModule->jumpTo);

        foreach ($arrEvents as &$day) {
            foreach ($day as &$time) {
                foreach ($time as &$event) {
                    // Set Place
                    $objPlace = $this->Database->prepare("SELECT * FROM tl_calendar_places WHERE id=?")->execute($event['evr_place']);
                    if ($objPlace->numRows > 0) {
                        $event['place'] = $objPlace->street . ', ' . $objPlace->zip . ' ' . $objPlace->city;
                        $event['place_city'] = $objPlace->zip . ' ' . $objPlace->city;
                        $event['place_street'] = $objPlace->street;
                    }

                    // Set Status
                    $objRegistrations = $this->Database->prepare("SELECT id FROM tl_calendar_registrations WHERE pid=? AND active=1")->execute($event['id']);
                    $event['status'] = $this->checkEventStatus($event, $objRegistrations->numRows);

                    // Build Jump-Link
                    if ($event['status'] != 'over' && $event['status'] != 'full') {
                        $alias = ((strlen($event['alias']) && !$GLOBALS['TL_CONFIG']['disableAlias']) ? $event['alias'] : $event['id']);
                        $event['hrefRegister'] = $this->generateFrontendUrl($objJump->Row(), '/event/' . $alias);
                    }

                }
            }
        }

        return $arrEvents;
    }

    public function buildCouponPdf(DataContainer $dc, $blnOutput = true)
    {
        $this->generateCouponPDF($dc->id, true);
    }

    public function sendCouponMail(DataContainer $dc)
    {
        $filename = $this->generateCouponPDF($dc->id, false);

        $objRegistration = $this->Database->prepare("SELECT *,
                         (SELECT name FROM tl_iso_products WHERE id=productId) as ticket
                         FROM tl_calendar_registrations WHERE id=?")->execute($dc->id);

        $arrData = array(
            'firstname' => $objRegistration->firstname,
            'lastname' => $objRegistration->lastname,
        );

        if ($this->sendMail($objRegistration->email, array($filename), $arrData)) {
            $arrSet = array(
                'isSend' => true,
                'sendTime' => time()
            );
            $this->Database->prepare("UPDATE tl_calendar_registrations %s WHERE id = ?")->set($arrSet)->execute($dc->id);
        }
    }

    private function sendMail($rcp, $arrAttacments, $arrData)
    {
    	$this->import('Isotope');
    
    
        $strText = $this->Isotope->Config->evr_ticketmail_text;
        $strText = str_replace('##firstname##', $arrData['firstname'], $strText);
        $strText = str_replace('##lastname##', $arrData['lastname'], $strText);

        $objEmail = new Email();
        $objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
        $objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
        $objEmail->subject = sprintf($this->Isotope->Config->evr_ticketmail_subject, $this->Environment->host);
        $objEmail->html = $strText;
        try {
            foreach ($arrAttacments as $attachment) {
                $objEmail->attachFile(TL_ROOT . '/system/tmp/' . $attachment);
            }
            $objEmail->sendTo($rcp);
            return true;
        } catch (Exception $e) {
            $this->log($e->getMessage(), 'sendMail()', TL_ERROR);
            return false;
        }


    }

    private function generateCouponPDF($regId, $blnOutput = false, $count = 1, $totalCount = 1)
    {
        $this->import('Database');
        
        $objTemplate = new BackendTemplate('iso_coupon');

        // Get all Data for Coupon
        $objRegistration = $this->Database->prepare("SELECT *,
                 (SELECT name FROM tl_iso_products WHERE id=productId) as ticket
                 FROM tl_calendar_registrations WHERE id=?")->execute($regId);
        $objOrder = new IsotopeOrder();
        $objOrder->findBy('uniqid', $objRegistration->orderId);
        $objProduct = IsotopeFrontend::getProduct($objRegistration->productId);
        $objEvent = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE id=?")->execute($objRegistration->pid);
        $objPlace = $this->Database->prepare("SELECT * FROM tl_calendar_places WHERE id=?")->execute($objEvent->evr_place);
        $objTemplate->eventTitle = $objEvent->title;
        $objTemplate->eventDate = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objEvent->startDate);
        $objTemplate->eventTime = $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $objEvent->startTime);
        $objTemplate->eventEndTime = $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $objEvent->endTime);
        $objTemplate->orderDate = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objOrder->date);
        $objTemplate->orderId = $objOrder->order_id;
        $objTemplate->ticketCount = $count;
        $objTemplate->ticketTotalCount = $totalCount;
        $objTemplate->order = $objOrder;
        $objTemplate->registration = $objRegistration;
        $objTemplate->product = $objProduct;
        $objTemplate->place = $objPlace;
        // TODO: Variabel machen
        // $objTemplate->image = TL_ROOT . '/' . $this->getImage('/tl_files/meister-griller/files/logos/gm_logo.jpg', 150, null);

        //Shopdaten für ticket
        $objShop = $this->Database->prepare("SELECT company, firstname, lastname, street_1, postal, city, phone FROM tl_iso_config WHERE fallback=?")->execute(1);
        $objTemplate->shopdata = $objShop;
        //tickettitle ohne Datum
        $objTemplate->ticketTitleSubDate = preg_replace('/[0-9.-]/', '', $objEvent->title);
        

        // TCPDF configuration
        $l['a_meta_dir'] = 'ltr';
        $l['a_meta_charset'] = $GLOBALS['TL_CONFIG']['characterSet'];
        $l['a_meta_language'] = $GLOBALS['TL_LANGUAGE'];
        $l['w_page'] = 'page';

        // Include library
        require_once(TL_ROOT . '/system/config/tcpdf.php');
        require_once(TL_ROOT . '/plugins/tcpdf/tcpdf.php');

        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings
        $pdf->setLanguageArray($l);

        // Initialize document and add a page
        $pdf->AliasNbPages();

        // Set font
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN);

        // Start new page
        $pdf->AddPage();

        $pdf->writeHTML($objTemplate->parse(), true, 0, true, 0);
        $pdf->lastPage();
        if ($blnOutput) {
            // Close and output PDF document inline
            $pdf->Output(standardize(ampersand('ticket-' . $objRegistration->regNr, false), true) . '.pdf', 'I');
            // Stop script execution
            exit;
        } else {
            //Put File to /tmp
            $filename = standardize(ampersand('ticket-' . $objRegistration->regNr, false), true) . '.pdf';
            $pdf->Output(TL_ROOT . '/system/tmp/' . $filename, 'F');
            return $filename;
        }
    }


    public static function checkEventStatus($arrEvent, $registrations)
    {

        $fewSlots = $GLOBALS['TL_CONFIG']['evr_fewFree'];

        // Check date
        if ($arrEvent['startDate'] < time()) {
            return 'over';
        }
        $status = 'free';
        // Check Full
        if ($registrations < $arrEvent['evr_slots']) {
            //check fewFree -> $registraions >= (max Slots - fewSlots)
            if ($registrations >= ($arrEvent['evr_slots'] - $fewSlots)) {
                $status = 'fewFree';
            }
        } else {
            // Status Red - Full
            $status = 'full';
        }
        return $status;

    }

    public function generateRegistrationId($id)
    {

        $this->import('Database');
        $objRegistration = $this->Database->prepare("SELECT * FROM tl_calendar_registrations WHERE id=?")->execute($id);
        $objEvent = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE id=?")->execute($objRegistration->pid);

        $strNr = $objEvent->alias . '-reg' . $objRegistration->id;

        return $strNr;

    }

    public function regNr_save_callback($varValue, DataContainer $dc)
    {
        if ($varValue == '') {
            $varValue = $this->generateRegistrationId($dc->id);
        }
        return $varValue;
    }


    public function exportRegsToCsv(DataContainer $dc)
    {
        $this->import('Database');
        $objEvent = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE id=?")->execute($dc->id);

        $strName = utf8_romanize($objEvent->title);
        $strName = strtolower(str_replace(' ', '_', $strName));
        $strName = preg_replace('/[^A-Za-z0-9\._-]/', '', $strName);
        $strName = basename($strName);

        $objRegistrtations = $this->Database->prepare("SELECT id  FROM tl_calendar_registrations WHERE pid = ?")->execute($dc->id);

        // Open the "save as …" dialogue
        $strTmp = md5(uniqid(mt_rand(), true));
        $objFile = new File('system/tmp/' . $strTmp);
        $bl_header = true;
        while ($objRegistrtations->next()) {
            $arrResult = $this->getDataForRegistrationById($objRegistrtations->id);
            if ($bl_header) {
                $objFile->append(implode(';', array_keys($arrResult)));
                $bl_header = false;
            }
            $objFile->append(implode(';', $arrResult));
        }
        $objFile->close();

        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="' . $strName . '.csv"');
        header('Content-Length: ' . $objFile->filesize);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');

        $resFile = fopen(TL_ROOT . '/system/tmp/' . $strTmp, 'rb');
        fpassthru($resFile);
        fclose($resFile);

        exit;
    }

    public function getDataForRegistrationById($id)
    {
        $this->import('Database');
        $objRegistrtation = $this->Database->prepare("SELECT pid, regNr, firstname, lastname, email, phone, street, plz_city, message, manual, orderId, productId
        FROM tl_calendar_registrations WHERE id = ?")->execute($id);
        $objEvent = $this->Database->prepare("SELECT title
                                FROM tl_calendar_events WHERE id = ?")->execute($objRegistrtation->pid);
        $objOrder = new IsotopeOrder();
        $objOrder->findBy('uniqid', $objRegistrtation->orderId);


        $arrReturn = array(
            'regNr' => $objRegistrtation->regNr,
            'status' => $objOrder->status,
            'firstname' => $objRegistrtation->firstname,
            'lastname' => $objRegistrtation->lastname,
            'email' => $objRegistrtation->email,
            'phone' => $objRegistrtation->phone,
            'street' => $objRegistrtation->street,
            'plz_city' => $objRegistrtation->plz_city,
            'message' => $objRegistrtation->message,
            'payment' => $objOrder->Payment->name,
            'event' => $objEvent->title
        );

        return $arrReturn;

    }
}