Feature: Add a new user

  Scenario: I want to ingest a new user into my SimpleRoles application
    Given: There are no "users" in the system
    And I want to create a new user named "Frank John" with username of "johnf" and a reference of "someref"
    When I issue a "POST" request to "/users"
    Then The Response Code will be "200"
    And The response is JSON