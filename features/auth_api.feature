Feature: Login

#  Scenario: Test GWT
#    Given I want to test JWT


  Scenario: Logging in as a valid user
    Given that I want to make a new "Auth"
    And that its "email" is "timothy.broder@gmail.com"
    And that its "password" is "timtim"
    When I request "/api/v1/signin"
    Then the response is JSON
    And the response has a "token" property
    And the response has a "result" property
    And the "result" property equals "success"
    And the rest response status code should be 200

  Scenario: Logging in with a bad password
    Given that I want to make a new "Auth"
    And that its "email" is "timothy.broder@gmail.com"
    And that its "password" is "timtimm"
    When I request "/api/v1/signin"
    Then the response is JSON
    And the response has a "error" property
    And the "error" property equals "invalid_credentials"
    And the rest response status code should be 401

  Scenario: Logging in with a bad email
    Given that I want to make a new "Auth"
    And that its "email" is "timothy.broderrrrrrrr@gmail.com"
    And that its "password" is "timtimm"
    When I request "/api/v1/signin"
    Then the response is JSON
    And the response has a "error" property
    And the "error" property equals "invalid_credentials"
    And the rest response status code should be 401

  Scenario: No Beta or Invite code will fail signup
    Given that I want to make a new "Signup"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the response has a "error" property
    And the "error" property equals "undefined_betacode_or_invitecode"
    And the response has a "code" property
    And the "code" property equals "401"
    And the rest response status code should be 200

  Scenario: Invalid Beta Code
    Given that I want to make a new "Signup"
    And I have checked to make sure the beta code "12345" does not exist
    And that its "betacode" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the response has a "error" property
    And the "error" property equals "invalid_betacode"
    And the response has a "code" property
    And the "code" property equals "401"
    And the rest response status code should be 200

  Scenario: Invalid Intive Code
    Given that I want to make a new "Signup"
    And I have checked to make sure the invite code "123456" does not exist
    And that its "invite_code" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the response has a "error" property
    And the "error" property equals "invalid_invite_code"
    And the response has a "code" property
    And the "code" property equals "401"
    And the rest response status code should be 200

  Scenario: Valid Beta Code
    Given that I want to make a new "Auth"
    And I have a unique beta code "123456" for "timothy.broder@gmail.com"
    And I have a betacode "123456"
    And user "timothy.broder@gmail.com" does not exist
    And that its "email" is "timothy.broder@gmail.com"
    And that its "password" is "TimTimTim123!"
    And that its "betacode" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "201"
    And the response has a "result" property
    And the "result" property equals "success"
    And the response has a "token" property

  Scenario: Can't use a Valid Beta Code again
    Given that I want to make a new "Auth"
    And I have a betacode "123456"
    And that its "email" is "timothy.broder@gmail.com"
    And that its "password" is "TimTimTim123!"
    And that its "betacode" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property equals "401"
    And the response has a "error" property
    And the "error" property equals "betacode_used"


  Scenario Outline: Invalid Emails
    Given that I want to make a new "Auth"
    And I have a unique beta code "123456" for "<email>"
    And I have a betacode "123456"
    And user "<email>" does not exist
    And that its "email" is "<email>"
    And that its "password" is "TimTimTim123!"
    And that its "betacode" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property stringly equals "401"
    And the response has a "result" property
    And the "result" property equals "fail"
    And the response has a "error" property
    And the "error" property equals "email_fraud"

    Examples:
      | email |
      |  foooooo   |
      |  The@DomainNoDotCom   |
      |  <script>@/injection.com   |
      | A@double.com.com  |

  Scenario Outline: Valid Emails
    Given that I want to make a new "Auth"
    And I have a unique beta code "123456" for "<email>"
    And I have a betacode "123456"
    And user "<email>" does not exist
    And that its "email" is "<email>"
    And that its "password" is "TimTimTim123!"
    And that its "betacode" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property stringly equals "201"
    And the response has a "result" property
    And the "result" property equals "success"
    And the response has a "token" property
    And the response has a "user_data" property

    Examples:
      | email |
      |  timothy.broder@gmail.com   |
      |  tim@kidgifting.com   |

  Scenario Outline: Invalid Passwords
    Given that I want to make a new "Auth"
    And I have a unique beta code "123456" for "timothy.broder@gmail.com"
    And I have a betacode "123456"
    And user "timothy.broder@gmail.com" does not exist
    And that its "email" is "timothy.broder@gmail.com"
    And that its "password" is "<password>"
    And that its "betacode" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property stringly equals "401"
    And the response has a "result" property
    And the "result" property equals "fail"
    And the response has a "error" property
    And the "error" property equals "email_fraud"

    Examples:
      | password |
      |  welcome123!W   |
      |  welcomewelcome123   |
      |  welcomewelcome!   |
      | A@Short1!  |

  Scenario Outline: Valid Passwords
    Given that I want to make a new "Auth"
    And I have a unique beta code "123456" for "timothy.broder@gmail.com"
    And I have a betacode "123456"
    And user "timothy.broder@gmail.com" does not exist
    And that its "email" is "timothy.broder@gmail.com"
    And that its "password" is "<password>"
    And that its "betacode" is "123456"
    When I request "/api/v1/signup"
    Then the response is JSON
    And the rest response status code should be 200
    And the response has a "code" property
    And the "code" property stringly equals "201"
    And the response has a "result" property
    And the "result" property equals "success"
    And the response has a "token" property
    And the response has a "user_data" property

    Examples:
      | password |
      |  welcomewelcome123!   |
      |  welcomeWelcome!   |
      |  welcomeWelcome123 |


