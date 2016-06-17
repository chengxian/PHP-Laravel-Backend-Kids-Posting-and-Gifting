Feature: Invites
  Send dem invites

  Scenario: Gift email is sent
    When the "amount" private property is "10.00"
    And the system creates an gift job with email
    Then an invite should be sent with the private property "amount"

  Scenario: Gift Receive email is sent
    When the "amount" private property is "11.00"
    And the system creates an gift receive job with email
    Then an invite should be sent with the private property "amount"