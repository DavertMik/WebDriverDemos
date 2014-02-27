<?php


class InstaclickWebDriverTest extends PHPUnit_Framework_TestCase {

    /**
     * @var WebDriver\Session;
     */
    protected $session;

    public function setUp()
    {
        $wd_host = 'http://localhost:4444/wd/hub'; // this is the default
        $web_driver = new \WebDriver\WebDriver($wd_host);
        $this->session = $web_driver->session('firefox');
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

        $this->session->timeouts()->wait(
           function($session) {
             return $session->element(\WebDriver\LocatorStrategy::ID, 'ts_res')->displayed();
           }
        , 20, 1, [$this->session]);

        $trains = $result->elements(\Webdriver\LocatorStrategy::TAG_NAME, 'tr');
        $this->assertNotEmpty($trains);
        $trainId = $trains[0]->element(\Webdriver\LocatorStrategy::CLASS_NAME, 'num')->text();

        $trains = $result->elements(\Webdriver\LocatorStrategy::TAG_NAME, 'tr');
        $place = $trains[0]->element(\Webdriver\LocatorStrategy::CLASS_NAME, 'place');
        $places = $place->elements(\Webdriver\LocatorStrategy::TAG_NAME, 'div');
        $this->assertNotEmpty($places);
        $cheapest = end($places);
        $cheapest->element(\Webdriver\LocatorStrategy::TAG_NAME, 'button')->click();

        $this->session->timeouts()->wait(
           function($session) {
             return count($session->elements(\Webdriver\LocatorStrategy::CLASS_NAME, 'vToolsPopup')) > 0;
           }
        , 20, 5, [$this->session]);

        $popup = $this->session->element(\Webdriver\LocatorStrategy::CLASS_NAME, 'vToolsPopup');
        $this->assertTrue($popup->displayed());
        $this->assertContains($trainId, $popup->element(\Webdriver\LocatorStrategy::CLASS_NAME, 'vToolsPopupHeader')->text());
    }


    protected function trains()
    {
        $result = $this->session->element('id', 'ts_res');
        return $result->element(\Webdriver\LocatorStrategy::TAG_NAME, 'tbody');
    }


    protected function fillStation($el, $station)
    {
        $el = $this->session->element(\Webdriver\LocatorStrategy::NAME, $el);
        $el->value(array('value' => array($station)));
        return $el;
    }

    protected function pickStation($el, $station)
    {
        $this->session->timeouts()->wait(
           function($session) use ($el) {
             return $session->element(\Webdriver\LocatorStrategy::ID, $el)->displayed();
           }
        , 20, 1, [$this->session]);
        return $this->session->element(\Webdriver\LocatorStrategy::ID, $el)->element('xpath', "./div[@title='$station']")->click();
    }

    protected function forTomorrow()
    {
        $el = $this->session->element(\Webdriver\LocatorStrategy::ID, 'date_dep');
        $el->value(array('value' => array(\WebDriver\Key::CONTROL.'a')));
        $el->value(array('value' => array(date("m.d.Y", time() + 86400))));
        return $el;
    }


    protected function waitForUserInput()
    {
        if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
    }



}
