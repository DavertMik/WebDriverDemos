<?php

class BookingTest extends \PHPUnit_Extensions_Selenium2TestCase
{
    const FROM_INPUT = 'kyi';
    const FROM_FIND = 'Kyiv';
    const TO_INPUT = 'lv';
    const TO_FIND = 'Lviv';

    const URL = 'http://booking.uz.gov.ua/';

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->setupSpecificBrowser([
            'browserName' => 'firefox',
//            'host' => 'windows.host'
        ]);
        $this->setBrowserUrl(self::URL);
    }

    /**
     * @param $strategy
     * @param $value
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    protected function _waitForElement($strategy, $value)
    {
        return $this->waitUntil(function() use ($strategy, $value) {
            $element = $this->element($this->using($strategy)->value($value));
            return $element && $element->displayed() ? $element : null;
        });
    }

    public function testSomething()
    {
        $this->url(self::URL);
        $this->selectDestination('station_from', 'stations_from', self::FROM_INPUT, self::FROM_FIND);
        $this->selectDestination('station_till', 'stations_till', self::TO_INPUT, self::TO_FIND);
        $dateElement = $this->byId('date_dep');
        $this->assertNotEmpty($dateElement);
        $dateElement->clear();
        $this->keys(date('m.d.Y', time() + 86400));
        $search = $this->byName('search');
        $search->click();
        $trainsTable = new Element('id', 'ts_res', $this, $this);
        $trainsTable = $trainsTable->waitForElement();
        $trainRow = $trainsTable->byTag('tbody')->byTag('tr');
        $places = $trainRow->byClassName('place');
        $places = $places->elements($this->using('tag name')->value('div'));
        $this->assertNotEmpty($places);
        /** @var \PHPUnit_Extensions_Selenium2TestCase_Element $placeLast */
        $placeLast = end($places);
        $placeLast->byTag('button')->click();
        $trainText = $trainRow->byClassName('num')->text();
        $popup = new Element('class name', 'vToolsPopup', $this, $this);
        $popup = $popup->waitForElement();
        $this->assertContains($trainText, $popup->byClassName('vToolsPopupHeader')->text());
        sleep(6);
    }

    private function selectDestination($inputName, $dropdownId, $inputStr, $dropdownFind)
    {
        $element = $this->byName($inputName);
        $element->click();
        $this->keys($inputStr);
        $elementStations = $this->byId($dropdownId);
        $this->assertNotEmpty($elementStations);
        $elementStationsFound = new Element('xpath', "./div[@title='" . $dropdownFind . "']", $elementStations, $this);
        $elementStationsFound = $elementStationsFound->waitForElement();
        $this->assertNotEmpty($elementStationsFound);
        $elementStationsFound->click();
    }
}


class Element
{
    private $strategy;

    private $value;

    /**
     * @var \PHPUnit_Extensions_Selenium2TestCase_Element|\PHPUnit_Extensions_Selenium2TestCase
     */
    private $context;

    /**
     * @var \PHPUnit_Extensions_Selenium2TestCase
     */
    private $testCase;

    public function __construct($strategy, $value, $context, $testCase)
    {
        $this->testCase = $testCase;
        $this->strategy = $strategy;
        $this->value = $value;
        $this->context = $context;
    }

    /**
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function waitForElement()
    {
        return $this->testCase->waitUntil(function() {
            $element = $this->context->element($this->testCase->using($this->strategy)->value($this->value));
            return $element && $element->displayed() ? $element : null;
        }, 10000);
    }
}