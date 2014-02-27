<?php


class Element34WebDriverTest extends PHPUnit_Framework_TestCase {

    /**
     * @var WebDriver\Session;
     */
    protected $session;

    public function setUp()
    {
        $wd_host = 'http://localhost:4444/wd/hub'; // this is the default
        $web_driver = new PHPWebDriver_WebDriver($wd_host);
        $this->session = $web_driver->session();
    }

    public function tearDown()
    {
        $this->session->close();
    }

    protected $from = 'Kyiv';
    protected $fromInput = 'ky';
    protected $to = 'Lviv';
    protected $toInput = 'lv';


    public function testKyivLvivBooking()
    {
        $this->session->open('http://booking.uz.gov.ua/');
        $from = $this->fillStation('station_from', $this->fromInput);
        $this->pickStation('stations_from', $this->from);
        $this->assertEquals("Kyiv", $from->attribute('value'));

        $to = $this->fillStation('station_till', $this->toInput);
        $this->pickStation('stations_till', $this->to);
        $this->assertEquals("Lviv", $to->attribute('value'));

        $this->forTomorrow();
        $this->session->element('name', 'search')->click();

        $result = $this->trains();

        (new PHPWebDriver_WebDriverWait($this->session))->until(
           function($session) {
             return $session->element(PHPWebDriver_WebDriverBy::ID, 'ts_res')->displayed();
           }
        );

        $trains = $result->elements(PHPWebDriver_WebDriverBy::TAG_NAME, 'tr');
        $this->assertNotEmpty($trains);
        $trainId = $trains[0]->element(PHPWebDriver_WebDriverBy::CLASS_NAME, 'num')->text();

        $trains = $result->elements(PHPWebDriver_WebDriverBy::TAG_NAME, 'tr');
        $place = $trains[0]->element(PHPWebDriver_WebDriverBy::CLASS_NAME, 'place');
        $places = $place->elements(PHPWebDriver_WebDriverBy::TAG_NAME, 'div');
        $this->assertNotEmpty($places);
        $cheapest = end($places);
        $cheapest->element(PHPWebDriver_WebDriverBy::TAG_NAME, 'button')->click();

        (new PHPWebDriver_WebDriverWait($this->session))->until(
           function($session) {
             return count($session->elements(PHPWebDriver_WebDriverBy::CLASS_NAME, 'vToolsPopup'));
           }
        );

        $popup = $this->session->element(PHPWebDriver_WebDriverBy::CLASS_NAME, 'vToolsPopup');
        $this->assertTrue($popup->displayed());
        $this->assertContains($trainId, $popup->element(PHPWebDriver_WebDriverBy::CLASS_NAME, 'vToolsPopupHeader')->text());
    }


    protected function trains()
    {
        $result = $this->session->element('id', 'ts_res');
        return $result->element(PHPWebDriver_WebDriverBy::TAG_NAME, 'tbody');
    }


    protected function fillStation($el, $station)
    {
        $el = $this->session->element(PHPWebDriver_WebDriverBy::NAME, $el);
        $el->sendKeys($station);
        return $el;
    }

    protected function pickStation($el, $station)
    {
        (new PHPWebDriver_WebDriverWait($this->session))->until(
           function($session) use ($el) {
             return $session->element(PHPWebDriver_WebDriverBy::ID, $el)->displayed();
           }
        );
        return $this->session->element(PHPWebDriver_WebDriverBy::ID, $el)->element('xpath', "./div[@title='$station']")->click();
    }

    protected function forTomorrow()
    {
        $el = $this->session->element(PHPWebDriver_WebDriverBy::ID, 'date_dep');
        $el->sendKeys((new PHPWebDriver_WebDriverKeys('ControlKey'))->key.'a');
        $el->sendKeys(date("m.d.Y", time() + 86400));
        return $el;
    }



    protected function waitForUserInput()
    {
        if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
    }

}
