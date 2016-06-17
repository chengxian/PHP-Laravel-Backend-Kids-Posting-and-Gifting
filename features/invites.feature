Feature: Invites
  Send dem invites

  Scenario: Invite email is sent
    When the "invite_code" private property is "12345"
    And the "email" private property is "kfff@mailinator.com"
    And the system creates an invite job with email "kfff@mailinator.com" and invite_code "12345"
    Then an invite should be sent to "kfff@mailinator.com" with the private property "invite_code"