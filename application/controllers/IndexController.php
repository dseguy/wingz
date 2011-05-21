<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $cs = $this->_helper->contextSwitch();
        $cs->setActionContext('index', array ('xml', 'json'))
           ->setAutoDisableLayout(true)
           ->initContext();
           
        $this->view->googleTracker('UA-352655-7');
    }

    public function indexAction()
    {
        $data = file_get_contents(APPLICATION_PATH 
            . '/../tests/library/Wingz/Service/Joindin/_files/eventlisthot.xml');
        $response = <<<EOL
HTTP/1.1 200 OK
Content-Type: text/xml

{$data}
EOL;
        $joindin = new Wingz_Service_Joindin();
        $joindin->getClient()->setAdapter('Zend_Http_Client_Adapter_Test');
        $joindin->getClient()->getAdapter()->setResponse($response);
        try {
            $events = $joindin->event()->getListing(
                Wingz_Service_Joindin_Event::LISTING_UPCOMING, 3);
            $this->view->assign(array (
                'events' => simplexml_load_string($events),
            ));
        } catch (Wingz_Service_Joindin_Exception $e) {
            $this->view->error = Wingz_Service_Exception::FAIL_MESSAGE;
        }
    }

    public function langAction()
    {
        $this->_helper->layout()->disableLayout();
        $lang = $this->getRequest()->getParam('lang', 'en');
        $locales = array ('en' => 'en_US', 'nl' => 'nl_NL');
        if (!key_exists($lang, $locales)) {
            $lang = 'en';
        }
        
        $bootstrap = $this->getInvokeArg('bootstrap');
        $bootstrap->bootstrap('translate');
        $translate = $bootstrap->getResource('translate');
        $translate->setLocale($lang);
        
        Zend_Registry::set('Zend_Translate', $translate);
        Zend_Registry::set('Zend_Locale', $lang);
        $this->view->locale = $locales[$lang];
        Zend_Debug::dump($translate, 'translate');
        Zend_Debug::dump($locales[$lang], 'locale');
    }

    public function eventAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        if (null === $id) {
            return $this->_helper->redirector('index');
        }
        $data = file_get_contents(APPLICATION_PATH 
            . '/../tests/library/Wingz/Service/Joindin/_files/eventdetail.xml');
        $response = <<<EOL
HTTP/1.1 200 OK
Content-Type: text/xml

{$data}
EOL;
        $joindin = new Wingz_Service_Joindin();
        $joindin->getClient()->setAdapter('Zend_Http_Client_Adapter_Test');
        $joindin->getClient()->getAdapter()->setResponse($response);
        $event = $joindin->event()->getEventDetail($id);
        $xml = simplexml_load_string($event);
//        $this->getResponse()->setHeader('Content-type', 'text/xml');
//        $this->_helper->layout()->disableLayout();
//        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
//        echo $xml->asXML();
        $this->view->event = $xml;
    }

    public function qrcodeAction()
    {
        error_reporting(E_ALL|E_STRICT);
        ini_set('display_errors', 1);
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParam('data', null);
        $data = urldecode($data);
        $qrCode = null;
        if (null !== $data) {
            require_once realpath(APPLICATION_PATH . '/../library/Image/QRCode.php');
            $qr = new Image_QRCode();
            $options = array (
                'image_type' => 'png',
            );
            try {
                $qrCode = $qr->makeCode($data, $options);
                $this->getResponse()->setHeader('Content-Type', 'image/png');
            } catch (Exception $e) {
                $qrCode = $e->getMessage();
            }
        }
        $this->view->qrCode = $qrCode;
    }


}







