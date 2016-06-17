Feature: Beta code creation
  As an admin, I'd like to create beta codes for my users

  Background:
    Given the user is logged in as an admin
    And a non-admin cannot get to these screen
    Given I am logged in
    And I am on "/invite-user"

   @mail
   Scenario: Admin generate new beta code to invite a user
     When I fill in "email" with "foo@mailinator.com"
     And I press "invite"
     Then a beta code should be generated for "foo@mailinator.com"
     And an email should be sent to the "foo@mailinator.com" with the beta code
     And I should see "Successfully Invited"
     And this beta code should be unique compared to all beta codes in the system