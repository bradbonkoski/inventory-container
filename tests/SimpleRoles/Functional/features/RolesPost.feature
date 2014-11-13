Feature: Test out Posting to Roles

  Scenario: As a user I would like to create a new role
    Given There are no "roles" in the system
    And I want to create a new role named "role1" with description "Description of Role 1"
    And I want to create a new role named "role2" with description "Description of Role 2"
    When I issue a "POST" request to "/roles"
    Then The Response Code will be "200"
    And The response is JSON
    And I will get the following list of roles:
      | role1           | Description of Role 1              |
      | role2           | Description of Role 2              |


  Scenario: As a user I would like to create a new role
    Given There are no "roles" in the system
    And I want to create a new role named "role1" with description "Description of Role 1"
    And I want to create a new role named "role2" with description "Description of Role 2"
    And I want to create a new role named "role1" with description "Description of Role 1"
    When I issue a "POST" request to "/roles"
    Then The Response Code will be "200"
    And The response is JSON
    And I will get the following list of roles:
      | role1           | Description of Role 1              |
      | role2           | Description of Role 2              |