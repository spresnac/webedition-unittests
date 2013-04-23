<?php

class weShopStatusMails{

	var $FieldsHidden; //an array of statusfield names not to be shown in order
	var $FieldsHiddenCOV; //an array of statusfield names not to be shown in order
	var $FieldsText; //an array with keys equal to name of statusfield, and value = text to be shown
	var $FieldsMails; //an array with keys equal to name of statusfield, and value = 0 for no Mail, 1 for Mail by Hand, 2 for automatic mails
	var $EMailData; // an array with the E-Mail data, see getShopStatusMails
	var $LanguageData; // an array with the Language data, see getShopStatusMails
	var $FieldsDocuments; // an array with dfault values and separate Arrays for each Langauge, see getShopStatusMails
	public static $StatusFields = array('DateOrder', 'DateConfirmation', 'DateCustomA', 'DateCustomB', 'DateCustomC', 'DateShipping', 'DateCustomD', 'DateCustomE', 'DatePayment', 'DateCustomF', 'DateCustomG', 'DateCancellation', 'DateCustomH', 'DateCustomI', 'DateCustomJ', 'DateFinished');
	public static $MailFields = array('MailShipping', 'MailPayment', 'MailOrder', 'MailConfirmation', 'MailCustomA', 'MailCustomB', 'MailCustomC', 'MailCustomD', 'MailCustomE', 'MailCustomF', 'MailCustomG', 'MailCustomH', 'MailCustomI', 'MailCustomJ', 'MailCancellation', 'MailFinished');

	function __construct($FieldsHidden, $FieldsHiddenCOV, $FieldsText, $FieldsMails, $EMailData, $LanguageData, $FieldsDocuments){

		$this->FieldsHidden = $FieldsHidden;
		$this->FieldsHiddenCOV = $FieldsHiddenCOV;
		$this->FieldsText = $FieldsText;
		$this->FieldsMails = $FieldsMails;
		$this->EMailData = $EMailData;
		$this->LanguageData = $LanguageData;
		$this->FieldsDocuments = $FieldsDocuments;
	}

	function initByRequest(&$req){

		return new weShopStatusMails(
				$req['FieldsHidden'],
				$req['FieldsHiddenCOV'],
				$req['FieldsText'],
				$req['FieldsMails'],
				$req['EMailData'],
				$req['LanguageData'],
				$req['FieldsDocuments']
		);
	}

	function getShopStatusMails(){
		global $DB_WE;
		$documentsarray = array(
			'default' => array(
				'DateOrder' => 0,
				'DateConfirmation' => 0,
				'DateCustomA' => 0,
				'DateCustomB' => 0,
				'DateCustomC' => 0,
				'DateShipping' => 0,
				'DateCustomD' => 0,
				'DateCustomE' => 0,
				'DateCancellation' => 0,
				'DateCustomF' => 0,
				'DateCustomG' => 0,
				'DatePayment' => 0,
				'DateCustomH' => 0,
				'DateCustomI' => 0,
				'DateCustomJ' => 0,
				'DateFinished' => 0,
			)
		);
		$frontendL = $GLOBALS["weFrontendLanguages"];
		foreach($frontendL as $lc => &$lcvalue){
			$lccode = explode('_', $lcvalue);
			$lcvalue = $lccode[0];
		}
		unset($lcvalue);
		foreach($frontendL as $langkey){
			$documentsarray[$langkey] = $documentsarray['default'];
		}
		$zw = new weShopStatusMails(
				array(//Fieldshidden
					'DateOrder' => 0,
					'DateConfirmation' => 1,
					'DateCustomA' => 1,
					'DateCustomB' => 1,
					'DateCustomC' => 1,
					'DateShipping' => 0,
					'DateCustomD' => 1,
					'DateCustomE' => 1,
					'DateCancellation' => 1,
					'DateCustomF' => 1,
					'DateCustomG' => 1,
					'DatePayment' => 0,
					'DateCustomH' => 1,
					'DateCustomI' => 1,
					'DateCustomJ' => 1,
					'DateFinished' => 1
				),
				array(//FieldshiddenCOV
					'DateOrder' => 0,
					'DateConfirmation' => 1,
					'DateCustomA' => 1,
					'DateCustomB' => 1,
					'DateCustomC' => 1,
					'DateShipping' => 0,
					'DateCustomD' => 1,
					'DateCustomE' => 1,
					'DateCancellation' => 1,
					'DateCustomF' => 1,
					'DateCustomG' => 1,
					'DatePayment' => 0,
					'DateCustomH' => 1,
					'DateCustomI' => 1,
					'DateCustomJ' => 1,
					'DateFinished' => 1
				),
				array(//FieldsTexts
					'DateOrder' => g_l('modules_shop', '[bestelldatum]'),
					'DateConfirmation' => g_l('modules_shop', '[bestaetigt]'),
					'DateCustomA' => g_l('modules_shop', '[customA]'),
					'DateCustomB' => g_l('modules_shop', '[customB]'),
					'DateCustomC' => g_l('modules_shop', '[customC]'),
					'DateShipping' => g_l('modules_shop', '[bearbeitet]'),
					'DateCustomD' => g_l('modules_shop', '[customD]'),
					'DateCustomE' => g_l('modules_shop', '[customE]'),
					'DatePayment' => g_l('modules_shop', '[bezahlt]'),
					'DateCustomF' => g_l('modules_shop', '[customF]'),
					'DateCustomG' => g_l('modules_shop', '[customG]'),
					'DateCancellation' => g_l('modules_shop', '[storniert]'),
					'DateCustomH' => g_l('modules_shop', '[customH]'),
					'DateCustomI' => g_l('modules_shop', '[customI]'),
					'DateCustomJ' => g_l('modules_shop', '[customJ]'),
					'DateFinished' => g_l('modules_shop', '[beendet]')
				),
				array(//FieldsMails
					'DateOrder' => 1,
					'DateConfirmation' => 1,
					'DateCustomA' => 1,
					'DateCustomB' => 1,
					'DateCustomC' => 1,
					'DateShipping' => 1,
					'DateCustomD' => 1,
					'DateCustomE' => 1,
					'DateCancellation' => 1,
					'DateCustomF' => 1,
					'DateCustomG' => 1,
					'DatePayment' => 1,
					'DateCustomH' => 1,
					'DateCustomI' => 1,
					'DateCustomJ' => 1,
					'DateFinished' => 0
				),
				array(//EMailData
					'address' => '',
					'name' => '',
					'bcc' => '',
					'DocumentSubjectField' => 'Title',
					'DocumentAttachmentFieldA' => '',
					'DocumentAttachmentFieldB' => '',
					'emailField' => '',
					'titleField' => ''
				),
				array(//LanguageData
					'useLanguages' => 1,
					'languageField' => '',
					'languageFieldIsISO' => 0
				),
				$documentsarray
		);

		$DB_WE->query('SELECT * FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="weShopStatusMails"');

		if($DB_WE->next_record()){
			$zw2 = unserialize($DB_WE->f('strFelder'));
			foreach($zw->FieldsHidden as $key => &$value){
				if(isset($zw2->FieldsHidden[$key])){
					$value = $zw2->FieldsHidden[$key];
				}
			}
			unset($value);
			foreach($zw->FieldsHiddenCOV as $key => &$value){
				if(isset($zw2->FieldsHiddenCOV[$key])){
					$value = $zw2->FieldsHiddenCOV[$key];
				}
			}
			unset($value);
			foreach($zw->FieldsText as $key => &$value){
				if(isset($zw2->FieldsText[$key])){
					$value = $zw2->FieldsText[$key];
				}
			}
			foreach($zw->FieldsMails as $key => &$value){
				if(isset($zw2->FieldsMails[$key])){
					$value = $zw2->FieldsMails[$key];
				}
			}
			unset($value);
			foreach($zw->EMailData as $key => &$value){
				if(isset($zw2->EMailData[$key])){
					$value = $zw2->EMailData[$key];
				}
			}
			unset($value);
			foreach($zw->LanguageData as $key => &$value){
				if(isset($zw2->LanguageData[$key])){
					$value = $zw2->LanguageData[$key];
				}
			}
			unset($value);
			foreach($zw->FieldsDocuments as $key => &$value){
				if(isset($zw2->FieldsDocuments[$key])){
					$value = $zw2->FieldsDocuments[$key];
				}
			}
			unset($value);
		}
		return $zw;
	}

	function sendEMail($was, $order, $cdata, $pagelang = ''){
		global $DB_WE;
		$recipientOK = (isset($this->EMailData['emailField']) && $this->EMailData['emailField'] != '' && isset($cdata[$this->EMailData['emailField']]) && we_check_email($cdata[$this->EMailData['emailField']]));
		$docID = 0;
		$UserLang = '';
		if(isset($this->LanguageData['useLanguages']) && $this->LanguageData['useLanguages'] && isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']] != ''){
			if($pagelang != '' && isset($this->FieldsDocuments[$pagelang]) && isset($this->FieldsDocuments[$pagelang]['Date' . $was])){
				$docID = $this->FieldsDocuments[$pagelang]['Date' . $was];
			} else{
				if(isset($this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]) && isset($this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]['Date' . $was])){
					$docID = $this->FieldsDocuments[$cdata[$this->LanguageData['languageField']]]['Date' . $was];
				} else{
					$docID = $this->FieldsDocuments['default']['Date' . $was];
				}
			}
			if(isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']] != ''){
				$UserLang = $cdata[$this->LanguageData['languageField']];
			}
		} else{
			$docID = $this->FieldsDocuments['default']['Date' . $was];
			if(isset($this->LanguageData['languageField']) && $this->LanguageData['languageField'] != '' && isset($cdata[$this->LanguageData['languageField']]) && $cdata[$this->LanguageData['languageField']] != ''){
				$UserLang = $cdata[$this->LanguageData['languageField']];
			}
		}

		$docID = intval($docID);
		if($docID && weFileExists($docID)){
			$_SESSION['WE_SendMail'] = true;
			$_REQUEST['we_orderid'] = $order;
			$_REQUEST['we_userlanguage'] = $UserLang;
			$_REQUEST['we_shopstatus'] = $was;
			//$incpath= id_to_path($docID);
			//ob_start();
			//include($_SERVER['DOCUMENT_ROOT'].$incpath);
			//$codes = ob_get_contents();
			//ob_end_clean();

			$codes = we_getDocumentByID($docID);
			$maildoc = new we_webEditionDocument();
			$maildoc->initByID($docID);

			if(isset($this->EMailData['DocumentAttachmentFieldA']) && $this->EMailData['DocumentAttachmentFieldA'] != ''){
				$attachmentA = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldA']);
				$codes = $codes . $attachmentA;
			}
			unset($_REQUEST['we_orderid']);
			unset($_SESSION['WE_SendMail']);
		} else{
			t_e('Document to send as status mail is empty ID: ' . $docID);
			return false;
		}


		$subject = $maildoc->getElement($this->EMailData['DocumentSubjectField']);

		if($subject == ''){
			$subject = 'no subject given';
		}
		if($recipientOK && $subject != '' && $this->EMailData['address'] != '' && we_check_email($this->EMailData['address'])){
			if(!isset($this->EMailData['name']) || $this->EMailData['name'] === '' || $this->EMailData['name'] === null || $this->EMailData['name'] === $this->EMailData['address']){
				$from = $this->EMailData['address'];
			} else{
				$from['email'] = $this->EMailData['address'];
				$from['name'] = $this->EMailData['name'];
			}
			$phpmail = new we_util_Mailer('', $subject, $from);
			$phpmail->setIsEmbedImages(true);

			$phpmail->addHTMLPart($codes);
			$phpmail->addTextPart(strip_tags(str_replace("&nbsp;", " ", str_replace("<br />", "\n", str_replace("<br>", "\n", $codes)))));
			$phpmail->addTo($cdata[$this->EMailData['emailField']], ( (isset($this->EMailData['titleField']) && $this->EMailData['titleField'] != '' && isset($cdata[$this->EMailData['titleField']]) && $cdata[$this->EMailData['titleField']] != '' ) ? $cdata[$this->EMailData['titleField']] . ' ' : '') . $cdata['Forename'] . ' ' . $cdata['Surname']);
			if(isset($this->EMailData['bcc']) && $this->EMailData['bcc'] != ''){
				$bccArray = explode(',', $this->EMailData['bcc']);
				$phpmail->setBCC($bccArray);
			}
			if(isset($this->EMailData['DocumentAttachmentFieldA']) && $this->EMailData['DocumentAttachmentFieldA'] != ''){
				$attachmentAinternal = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldA'] . '_we_jkhdsf_int');
				$attachmentA = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldA'] . ($attachmentAinternal ? '_we_jkhdsf_intPath' : ''));
				if($attachmentA){
					$phpmail->doaddAttachment($_SERVER['DOCUMENT_ROOT'] . $attachmentA);
				}
			}
			if(isset($this->EMailData['DocumentAttachmentFieldB']) && $this->EMailData['DocumentAttachmentFieldB'] != ''){
				$attachmentBinternal = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldB'] . '_we_jkhdsf_int');
				$attachmentB = $maildoc->getElement($this->EMailData['DocumentAttachmentFieldB'] . ($attachmentBinternal ? '_we_jkhdsf_intPath' : ''));
				if($attachmentB){
					$phpmail->doaddAttachment($_SERVER['DOCUMENT_ROOT'] . $attachmentB);
				}
			}
			$phpmail->buildMessage();
			if($phpmail->Send()){
				$dasDatum = date('Y-m-d H:i:s');
				$DB_WE->query('UPDATE ' . SHOP_TABLE . ' SET Mail' . $DB_WE->escape($was) . '="' . $DB_WE->escape($dasDatum) . '" WHERE IntOrderID = ' . intval($order));

				return true;
			}
		}
		return false;
	}

	function checkAutoMailAndSend($was, $order, $cdata){
		if($this->FieldsMails['Date' . $was] == 2){
			$this->sendEMail($was, $order, $cdata);
		}
	}

	function getEMailHandlerCode($was, $dateSet){
		$datetimeform = "00.00.0000 00:00";
		$dateform = "00.00.0000";
		if($this->FieldsMails['Date' . $was]){
			$EMailhandler = '<table cellpadding="0" cellspacing="0" border="0" width="99%" class="defaultfont"><tr><td class="defaultfont">' . g_l('modules_shop', '[statusmails][EMail]') . ': </td>';
			if($_REQUEST["Mail" . $was] != $datetimeform && $_REQUEST["Mail" . $was] != ''){
				$EMailhandler .= '<td class="defaultfont" width="150">' . $_REQUEST["Mail" . $was] . '</td>';
				$but = we_button::create_button("image:/mail_resend", "javascript:check=confirm('" . g_l('modules_shop', '[statusmails][resent]') . "'); if (check){SendMail('" . $was . "');}");
			} else{
				$EMailhandler .= '<td class="defaultfont" width="150">&nbsp;</td>';
				$but = we_button::create_button("image:/mail_send", "javascript:SendMail('" . $was . "')");
			}
			$EMailhandler .= '<td class="defaultfont">' . ($dateSet != $dateform ? $but : we_html_tools::getPixel(30, 15)) . '</td></tr></table>';
		} else{
			return we_html_tools::getPixel(30, 15);
		}

		return $EMailhandler;
	}

	function save(){
		$DB_WE = $GLOBALS['DB_WE'];

		$query = 'REPLACE ' . ANZEIGE_PREFS_TABLE . ' SET strFelder="' . $DB_WE->escape(serialize($this)) . '",strDateiname="weShopStatusMails"';

		if($DB_WE->query($query)){
			$strFelder = f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', 'strFelder', $DB_WE);
			if($strFelder !== ''){
				$CLFields = unserialize($strFelder);
				$CLFields['languageField'] = $this->LanguageData['languageField'];
				$CLFields['languageFieldIsISO'] = $this->LanguageData['languageFieldIsISO'];
				$DB_WE->query('REPLACE ' . ANZEIGE_PREFS_TABLE . ' SET strFelder = "' . $DB_WE->escape(serialize($CLFields)) . '", strDateiname ="shop_CountryLanguage"');
			}
			return true;
		} else{
			return false;
		}
	}

}