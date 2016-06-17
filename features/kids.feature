Feature: ChildController

  Scenario: Add a valid Child for a parent with no children
    Given that I want to make a new "Child"
    And I have a user with email "timothy.broder@gmail.com"
    And user with email "timothy.broder@gmail.com" has 0 children
    And that its "first_name" is "Frodo"
    And that its "last_name" is "Baggins"
    And that its "birthday" is "01-03-1892"
    And that its "wants" is "Adventurer"
    When I request "/api/v1/child"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "201"
    And the response has a "result" property
    And the "result" property equals "success"
    And the response has a "child_id" property
    And the response has a "birthday" property
    And the "birthday" property equals "1892-03-01"

  Scenario: Can not add identical children
    Given that I want to make a new "Child"
    And I have a user with email "timothy.broder@gmail.com"
    And user with email "timothy.broder@gmail.com" has 1 children
    And that its "first_name" is "Bilbo"
    And that its "last_name" is "Baggins"
    And that its "birthday" is "01-03-1892"
    And that its "wants" is "Adventurer"
    When I request "/api/v1/child"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "201"
    And the response has a "result" property
    And the "result" property equals "success"
    And the response has a "child_id" property
    And the response has a "birthday" property
    And the "birthday" property equals "1892-03-01"
    And user with email "timothy.broder@gmail.com" has 2 children

  Scenario: Can not add identical children
    Given that I want to make a new "Child"
    And I have a user with email "timothy.broder@gmail.com"
    And user with email "timothy.broder@gmail.com" has 2 children
    And that its "first_name" is "Bilbo"
    And that its "last_name" is "Baggins"
    And that its "birthday" is "01-03-1892"
    And that its "wants" is "Adventurer"
    When I request "/api/v1/child"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "401"
    And the response has a "result" property
    And the "result" property equals "fail"
    And the response has a "error" property
    And the "error" property equals "invalid_parameters_child_exists"
    And user with email "timothy.broder@gmail.com" has 2 children

  Scenario: First Name too long
    Given that I want to make a new "Child"
    And I have a user with email "timothy.broder@gmail.com"
    And that its "first_name" is "BilboBilboBilboBilboBilboBilboBilboBilboBilboBilboBilbo"
    And that its "last_name" is "Baggins"
    And that its "birthday" is "01-03-1892"
    And that its "wants" is "Adventurer"
    When I request "/api/v1/child"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property stringly equals "401"
    And the response has a "result" property
    And the "result" property equals "fail"
    And the response has a "error" property
    And the "error" property equals "invalid_parameters"
    And the response has a "validation_errors" property


  Scenario: Last Name too long
    Given that I want to make a new "Child"
    And I have a user with email "timothy.broder@gmail.com"
    And that its "first_name" is "Bilbo"
    And that its "last_name" is "BagginsBagginsBagginsBagginsBagginsBagginsBagginsBaggins"
    And that its "birthday" is "01-03-1892"
    And that its "wants" is "Adventurer"
    When I request "/api/v1/child"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property stringly equals "401"
    And the response has a "result" property
    And the "result" property equals "fail"
    And the response has a "error" property
    And the "error" property equals "invalid_parameters"
    And the response has a "validation_errors" property


  Scenario: Bad Date format
    Given that I want to make a new "Child"
    And I have a user with email "timothy.broder@gmail.com"
    And that its "first_name" is "Bilbo"
    And that its "last_name" is "Baggins"
    And that its "birthday" is "1892-03-01"
    And that its "wants" is "Adventurer"
    When I request "/api/v1/child"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property stringly equals "401"
    And the response has a "result" property
    And the "result" property equals "fail"
    And the response has a "error" property
    And the "error" property equals "invalid_parameters"
    And the response has a "validation_errors" property
