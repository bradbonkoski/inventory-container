Feature: Test out suite of Roles Manipulation Web Services

  Scenario: As a user I would like to be able to list all Roles
    Given I have this information in my "role" table
      | id  | name            | description                        |
      |   1 | role1           | Description of Role 1              |
      |   2 | role2           | Description of Role 2              |
      |   3 | role3           | Description of Role 3              |
    When I issue a "GET" request to "/roles"
    Then The Response Code will be "200"
    And The response is JSON
    And I will get the following list of roles:
      |   1 | role1           | Description of Role 1              |
      |   2 | role2           | Description of Role 2              |
      |   3 | role3           | Description of Role 3              |


  Scenario: As a user I would like to be able to list all Roles By Pattern
    Given I have this information in my "role" table
      | id  | name            | description                        |
      |   1 | role_read1      | Description of Role Read 1         |
      |   2 | role_write1     | Description of Role Write 1        |
      |   3 | role_read2      | Description of Role Read 2         |
    When I issue a "GET" request to "/roles/read"
    Then The Response Code will be "200"
    And The response is JSON
    And I will get the following list of roles:
      |   1 | role_read1      | Description of Role Read 1         |
      |   3 | role_read2      | Description of Role Read 2         |