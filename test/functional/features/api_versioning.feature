Feature: api_versioning
    In order to access the API
    As an API client
    I need to be able to access different versions of the API

Scenario: Make an API request with depricated version
    Given I set the "Accept" header to "application/vnd.com.myservice.v1+json"
    And Make a "GET" request to "/"
    Then I should get:
        """
        {"error":"Version application\/vnd.com.myservice.v1+json has been deprecated. Please upgrade."}
        """
    And the response status code should be 426

Scenario: Make an API request with depricated version and valid version
    Given I set the "Accept" header to "application/vnd.com.myservice.v1+json,application/vnd.com.myservice.v3+json; charset=utf-8"
    And Make a "GET" request to "/"
    Then the response status code should be 200
    And the header "Content-Type" should be "application/vnd.com.myservice.v3+json"
    And the header "Vary" should be "Content-Type, Content-Language"
    And the header "Content-Language" should be "en"

Scenario: Make an API request with depricated version and valid version with version preference weighting
    Given I set the "Accept" header to "application/vnd.com.myservice.v2+json;q=0.9,application/vnd.com.myservice.v3+json;q=0.8 charset=utf-8"
    And I set the "Accept-Language" header to "da,en;q=0.7,en-gb;q=0.9"
    And Make a "GET" request to "/"
    Then the response status code should be 200
    And the header "Content-Type" should be "application/vnd.com.myservice.v2+json"
    And the header "Content-Language" should be "en_GB"
    And the header "Vary" should be "Content-Type, Content-Language"

Scenario: Make an API request with depricated version and valid version with version preference weighting
    Given I set the "Accept" header to "application/vnd.com.myservice.v2+json;q=0.9,application/vnd.com.myservice.v3+json;q=0.8 charset=utf-8"
    And I set the "Accept-Language" header to "da,de"
    And Make a "GET" request to "/"
    Then I should get:
        """
        {"error":"The requested languages are not supported: da, de."}
        """
    And the response status code should be 406

Scenario: Make an TRACE request to the API to test debugging
    Given I set the "Accept" header to "application/vnd.com.myservice.v2+json;q=0.9,application/vnd.com.myservice.v3+json;q=0.8 charset=utf-8"
    And I set the "Accept-Language" header to "da,en;q=0.7,en-gb;q=0.9"
    And Make a "TRACE" request to "/"
    Then I should get:
        """

        """
    And the header "Content-Type" should be "message/http"
    And the response status code should be 200
