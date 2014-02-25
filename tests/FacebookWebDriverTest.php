<?php
require_once __DIR__.'/../vendor/autoload.php';

class FacebookWebDriverTest extends PHPUnit_Framework_TestCase {

    /**
     * @var RemoteWebDriver
     */
    protected $wd;

    public function setUp()
    {
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $capabilities = array(WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->wd = RemoteWebDriver::create($host, $capabilities);
    }

    public function tearDown()
    {
        $this->wd->quit();        
    }

    protected $from = 'Kyiv';
    protected $fromInput = 'ky';
    protected $to = 'Lviv';
    protected $toInput = 'lv';

    public function testKyivLvivBooking()
    {
        $this->wd->get('http://booking.uz.gov.ua/');
        $from = $this->fillStation(WebDriverBy::name('station_from'), $this->fromInput);
        $this->pickStattion(WebDriverBy::id('stations_from'), $this->from);
        $this->assertEquals("Kyiv", $from->getAttribute('value'));

        $to = $this->fillStation(WebDriverBy::name('station_till'), $this->toInput);
        $this->pickStattion(WebDriverBy::id('stations_till'), $this->to);
        $this->assertEquals("Lviv", $to->getAttribute('value'));
        
        $this->forTomorrow();

        $this->wd->findElement(WebDriverBy::name('search'))->click();

        $result = $this->wd->findElement(WebDriverBy::id('ts_res'));

        (new WebDriverWait($this->wd, 20))
            ->until(WebDriverExpectedCondition::visibilityOf($result));

        $train = $this->firstTrainId($result);
        $this->openCheapestPlaceForFirstTrain($result);

        (new WebDriverWait($this->wd, 10))
            ->until(WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('vToolsPopup')));

        $popup = $this->wd->findElement(WebDriverBy::className('vToolsPopup'));
        $this->assertTrue($popup->isDisplayed());
        $this->assertContains($train, $popup->findElement(WebDriverBy::className('vToolsPopupHeader'))->getText());
    }

    protected function firstTrainId(WebDriverElement $result)
    {
        $trains = $result->findElements(WebDriverBy::className('vToolsDataTableRow2'));
        $this->assertNotEmpty($trains);
        return  $trains[0]->findElement(WebDriverBy::className('num'))->getText();
    }

    protected function openCheapestPlaceForFirstTrain(WebDriverElement $result)
    {
        $trains = $result->findElements(WebDriverBy::className('vToolsDataTableRow2'));
        $place = $trains[0]->findElement(WebDriverBy::className('place'));
        $places = $place->findElements(WebDriverBy::tagName('div'));
        $this->assertNotEmpty($places);
        $cheapest = end($places);
        $cheapest->findElement(WebDriverBy::tagName('button'))->click();
    }

    protected function fillStation($el, $station)
    {
        $el = $this->wd->findElement($el);
        $el->click();
        return $el->sendKeys($station);
    }

    protected function pickStattion($el, $station)
    {
        $suggest = $this->wd->findElement($el);
        (new WebDriverWait($this->wd, 10))->until(WebDriverExpectedCondition::visibilityOf($suggest));
        return $suggest->findElement(WebDriverBy::xpath("./div[@title='$station']"))->click();
    }

    protected function forTomorrow()
    {
        $this->wd->findElement(WebDriverBy::id('date_dep'))
            ->sendKeys([WebDriverKeys::CONTROL, 'a'])
            ->sendKeys(date("m.d.Y", time() + 86400));
    }

    protected function waitForUserInput()
    {
        if(trim(fgets(fopen("php://stdin","r"))) != chr(13)) return;
    }
}
