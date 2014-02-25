<?php


class InstaclickWebDriverTest extends PHPUnit_Framework_TestCase {

    /**
     * @var WebDriver\Session;
     */
    protected $wd;

    public function setUp()
    {
        $wd_host = 'http://localhost:4444/wd/hub'; // this is the default
        $web_driver = new \WebDriver\WebDriver($wd_host);
        $this->wd = $web_driver->session('firefox');
    }

    public function tearDown()
    {
        $this->wd->close();
    }


    protected $from = 'Kyiv';
    protected $fromInput = 'ky';
    protected $to = 'Lviv';
    protected $toInput = 'lv';


    public function testKyivLvivBooking()
    {
        $this->wd->open('http://booking.uz.gov.ua/');
        $input = $this->wd->element('name', 'station_from');
        $input->click('');
        $input->postValue($this->fromInput);
        $this->waitForUserInput();

    }

    protected function waitForUserInput()
    {
        if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
    }


    
//$this->wd->get('http://booking.uz.gov.ua/');
//$from = $this->fillStation(WebDriverBy::name('station_from'), $this->fromInput);
//$this->pickStattion(WebDriverBy::id('stations_from'), $this->from);
//$this->assertEquals("Kyiv", $from->getAttribute('value'));
//
//$to = $this->fillStation(WebDriverBy::name('station_till'), $this->toInput);
//$this->pickStattion(WebDriverBy::id('stations_till'), $this->to);
//$this->assertEquals("Lviv", $to->getAttribute('value'));
//
//$this->forTomorrow();
//
//$this->wd->findElement(WebDriverBy::name('search'))->click();
//
//$result = $this->wd->findElement(WebDriverBy::id('ts_res'));
//
//(new WebDriverWait($this->wd, 20))
//    ->until(WebDriverExpectedCondition::visibilityOf($result));
//
//$train = $this->firstTrainId($result);
//$this->openCheapestPlaceForFirstTrain($result);
//
//(new WebDriverWait($this->wd, 10))
//    ->until(WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('vToolsPopup')));
//
//$popup = $this->wd->findElement(WebDriverBy::className('vToolsPopup'));
//$this->assertTrue($popup->isDisplayed());
//$this->assertContains($train, $popup->findElement(WebDriverBy::className('vToolsPopupHeader'))->getText());
    
}
