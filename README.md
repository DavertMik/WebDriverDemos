# PHP WebDriver Demos

Comparison of different WebDriver APIs in PHP

* PHPUnit Selenium2
* Element34/php-webdriver
* Instaclick/php-webdriver
* Facebook/php-webdriver

## What is testing?

We are using site of Ukrainian railroad ticket booking <http://booking.uz.gov.ua/>.
It was chosen because it has lots of ajax (so we use explicit waits).

### Scenario

* open site
* enter "Ky" as departure point
* choose "Kyiv" from a list
* enter "Lv" as destination
* choose "Lviv" from a list
* set a date for tomorrow
* search for all trains
* choose the first train
* select the cheapest seats
* check that there is a popup with train name.
* we assert that popup contains train name

## Usage

1. Clone this repo
2. Install [Composer](http://packagist.org)
3. Run `php composer.phar install`
4. Download and start latest Selenium server
5. Execute tests one by one with PHPUnit

```
vendor/bin/phpunit tests/Element34WebDriverTest.php
vendor/bin/phpunit tests/FacebookWebDriverTest.php
vendor/bin/phpunit tests/InstaclickWebDriverTest.php
vendor/bin/phpunit tests/PHPUnitSelenium2Test.php
```





## Credits

Tests developed by **@DavertMik** and **@Blackakula**