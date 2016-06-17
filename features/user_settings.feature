Feature: UserController

  Scenario: User wants to save a valid donation percent for the first time
    Given that I want to make a new "User"
    And I have a user with email "timothy.broder@gmail.com"
    And user with email "timothy.broder@gmail.com" has no settings
    And that its "donation_percent" is "5"
    When I request "/api/v1/set-donation-percent"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "200"
    And the response has a "result" property
    And the "result" property equals "success"
    And the response has a "percent" property
    And the "percent" property equals "5"
    And the setting "donation_percent" for user with email "timothy.broder@gmail.com" should be "5.0"


  Scenario: User wants to save a valid donation percent
    Given that I want to make a new "User"
    And I have a user with email "timothy.broder@gmail.com"
    And that its "donation_percent" is "66"
    When I request "/api/v1/set-donation-percent"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "200"
    And the response has a "result" property
    And the "result" property equals "success"
    And the response has a "percent" property
    And the "percent" property equals "66"
    And the setting "donation_percent" for user with email "timothy.broder@gmail.com" should be "66.0"

  Scenario: User cannot save donation amount < 0
    Given that I want to make a new "User"
    And I have a user with email "timothy.broder@gmail.com"
    And that its "donation_percent" is "-1"
    When I request "/api/v1/set-donation-percent"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "401"
    And the response has a "error" property
    And the "error" property equals "invalid_donation_percent"

  Scenario: User cannot save donation amount > 100
    Given that I want to make a new "User"
    And I have a user with email "timothy.broder@gmail.com"
    And that its "donation_percent" is "101"
    When I request "/api/v1/set-donation-percent"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "401"
    And the response has a "error" property
    And the "error" property equals "invalid_donation_percent"

  Scenario: User cannot save donation amount of letters
    Given that I want to make a new "User"
    And I have a user with email "timothy.broder@gmail.com"
    And that its "donation_percent" is "aadasdasd"
    When I request "/api/v1/set-donation-percent"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "401"
    And the response has a "error" property
    And the "error" property equals "invalid_donation_percent"
