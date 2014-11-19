<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $_response;
    private $_client;
    private $_parameters = [];
    private $_headers = [];

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {

        $this->_parameters = $parameters;
        $baseUrl = $this->getParameter('base_url');
        $client = new Client(['base_url' => $baseUrl]);
        $this->_client = $client;
    }

    public function getParameter($name)
    {
        if (count($this->_parameters) === 0) {
            throw new \Exception('Parameters not loaded!');
        } else {
            $parameters = $this->_parameters;
            return (isset($parameters[$name])) ? $parameters[$name] : null;
        }
    }

    /**
     * @Given /^I set the "([^"]*)" header to "([^"]*)"$/
     */
    public function iSetTheHeaderTo($header, $value)
    {
        $this->_headers[$header] = $value;
    }

    /**
     * @Given /^Make a "([^"]*)" request to "([^"]*)"$/
     */
    public function makeARequestTo($method, $path)
    {
        try {
            $request = $this->_client->createRequest(
                $method,
                $path,
                [
                    'headers' => $this->_headers
                ]
            );
            $this->_response = $this->_client->send($request);
        } catch (\Exception $e) {
            $this->_response = $e->getResponse();
        }
    }

    /**
     * @Then /^I should get:$/
     */
    public function iShouldGet(PyStringNode $string)
    {
        if ($this->_response->getBody() != $string->__toString()) {
            throw new \Exception('Body ('.$this->_response->getBody().') does not match expected: ' . $string);
        }
    }

    /**
     * @Given /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe($httpStatus)
    {
        if ((string)$this->_response->getStatusCode() !== $httpStatus) {
            throw new \Exception('HTTP code does not match '.$httpStatus.
                ' (actual: '.$this->_response->getStatusCode().')');
        }
    }

    /**
     * @Given /^the header "([^"]*)" should be "([^"]*)"$/
     */
    public function theHeaderShouldBe($key, $value)
    {
        if ($this->_response->getHeader($key) != $value) {
            throw new \Exception('Header ('.$key.': '.$this->_response->getHeader($key).') does not equal: ' . $value);
        }
    }
}
