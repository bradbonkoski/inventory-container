<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require "bootstrap.php";
/**
 * Features context.
 */
class FeatureContext extends BehatContext
{

    private $baseUrl;
    private static $db;
    private $restObject = null;
    private $response = null;
    private $requestUrl = null;
    private $requestData;

    /**
     * Initializes context.
     * Every scenario gets its own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->baseUrl = rtrim(
            $parameters['web_host'].":".$parameters['web_port']."/".$parameters['web_path'],
            '/'
        );

        self::log("New Test Starting: ".date('m/d/Y h:i:s'));
        $dsn = "mysql:dbname={$parameters['db_name']};host={$parameters['db_host']};port={$parameters['db_port']}";
        self::log("DSN is: $dsn");

        self::$db = new \PDO($dsn, 'test', '');
        self::$db->setAttribute(
            \PDO::ATTR_DEFAULT_FETCH_MODE,
            \PDO::FETCH_ASSOC
        );

        $this->restObject = new stdClass();
        $this->client = new Guzzle\Service\Client();

        $this->requestData = array();
    }

    private static function log($msg)
    {
        error_log("$msg\n", 3, "/tmp/simpleRoles-bdd.log");
    }

    /**
     * @BeforeScenario
     */
    public static function setUpScenario()
    {
        self::$db->exec(
            'delete from role;'
            .'delete from users;'
            .'delete from user_roles;'
        );
    }

    private function dbRows($table)
    {
        $sql = "select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where
            TABLE_SCHEMA = 'simpleRoles' and TABLE_NAME = '$table'";

        self::log($sql);
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();
        $ret =array();
        foreach ($res as $r) {
            $ret[] = $r['COLUMN_NAME'];
        }
        return $ret;

    }

    /**
     * @Given /^There are no "([^"]*)" in the system$/
     */
    public function thereAreNoInTheSystem($table)
    {
        if ($table == 'roles') {
            $table = 'role';
        }

        self::$db->exec("delete from $table");
    }


    /**
     * @Given /^I have this information in my "([^"]*)" table$/
     */
    public function iHaveThisInformationInMyTable($dbTable, TableNode $tableData)
    {

        $cols = $this->dbRows($dbTable);
        foreach ($tableData->getHash() as $row) {

            $tableKeys = array_keys($row);
            if ($cols != $tableKeys) {
                throw new Exception("Test and DB Data Mismatch");
            }

            $colDef = implode(",", $cols);
            $valDef = implode(", :", $tableKeys);

            $sql = "insert into $dbTable ($colDef)
              values(:$valDef)";

            self::log($sql);

            $stmt = self::$db->prepare($sql);

            foreach ($row as $k => &$v) {
                if (strlen($v) <= 0 || $v == 'null') {
                    $row[$k] = null;
                }
            }
            if (!$stmt->execute($row)) {
                $err = $stmt->errorInfo();
                self::log("DB Insert Error!");
                self::log(print_r($err, true));
            }
        }
        //throw new PendingException();
    }

    /**
     * @When /^I issue a "([^"]*)" request to "([^"]*)"$/
     */
    public function iIssueARequestTo($verb, $page)
    {
        $this->requestUrl = $this->baseUrl.$page;
        self::log("URL: {$this->requestUrl}");

        switch (strtolower($verb)) {
            case 'get':
                $resp = $this->client
                    ->get($this->requestUrl)
                    ->send();
                break;
            case 'delete':
                try {
                    $resp = $this->client
                        ->delete($this->requestUrl)
                        ->send();
                } catch (\Guzzle\Http\Exception\BadResponseException $e) {
                    $resp = $e->getResponse();
                }
                break;
            case 'put':
                try {
                    $resp = $this->client->put($this->requestUrl)
                        ->send();
                } catch (\Guzzle\Http\Exception\BadResponseException $e) {
                    $resp = $e->getResponse();
                }
                break;
            case 'post':
                try {
                    $resp = $this->client
                        ->post($this->requestUrl, array(), json_encode($this->requestData))
                        ->send();
                } catch (\Guzzle\Http\Exception\BadResponseException $e) {
                    $resp = $e->getResponse();
                }
            break;
        }
        $this->response = $resp;
    }

    /**
     * @Given /^I want to create a new role named "([^"]*)" with description "([^"]*)"$/
     */
    public function iWantToCreateANewRoleNamedWithDescription($name, $desc)
    {
        $arr = array('name' => $name, 'description' => $desc);
        $this->requestData[] = $arr;
    }

    /**
     * Test Compliment for added in a new user
     * @Given /^I want to create a new user named "([^"]*)" with username of "([^"]*)" and a reference of "([^"]*)"$/
     */
    public function iWantToCreateANewUserNamedWithUsernameOfAndAReferenceOf($user, $username, $ref)
    {
        $arr = array('name' => $user, 'username' => $username, 'ref' => $ref);
        $this->requestData[] = $arr;
    }

    /**
     * @Then /^The Response Code will be "([^"]*)"$/
     */
    public function theResponseCodeWillBe($code)
    {
        $testCode = $this->response->getStatusCode();
        if ($code != $testCode) {
            throw new Exception("Invalid Response Code.  Expected: $code got: $testCode");
        }
    }

    /**
     * @Given /^The response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = $this->response->json();
        self::log(print_r($data, true));
        if (!is_array($data)) {
            self::log("TEST Failed - [FeatureContext::theResponseIsJson]");
            throw new Exception("Response was not JSON");
        }
    }

    /**
     * @Given /^I will get the following list of roles:$/
     */
    public function iWillGetTheFollowingListOfRoles(TableNode $tableData)
    {
        $data = $this->response->json();

        self::log(print_r($data, true));

        $rows = $tableData->getRows();
        self::log(print_r($rows, true));

        if (count($rows) != count($data)) {
            throw new Exception("Row Mismatch");
        }


        for ($i = 0; $i < count($data); $i++) {

            // Actual data received from the Web Service
            $act = $data[$i];

            // Expected Results from the TableNode
            $exp = $rows[$i];
            $cnt = count($exp);

            self::log("CNT is $cnt");
            self::log(print_r($act, true));
            if ($cnt == 3) {
                if ($act['id'] != $exp[0]) {
                    throw new Exception("Bad[id]");
                }
            }

            if ($act['name'] != $exp[$cnt-2]) {
                throw new Exception("Bad[name]");
            }

            if ($act['description'] != $exp[$cnt-1]) {
                throw new Exception("Bad[desc] ");
            }
        }
        //throw new PendingException();
    }

    /**
     * @Given /^I will get the following list of users:$/
     */
    public function iWillGetTheFollowingListOfUsers(TableNode $tableData)
    {
        $data = $this->response->json();
        self::log(print_r($data, true));

        $rows = $tableData->getRows();
        self::log(print_r($rows, true));

        if (count($rows) != count($data)) {
            throw new Exception("Row Mismatch");
        }

        for ($i = 0; $i < count($data); $i++) {
            $act = $data[$i];
            $exp = $rows[$i];
            if ($act['id'] != $exp[0]) {
                throw new Exception("Bad[id]");
            }

            if ($act['name'] != $exp[1]) {
                throw new Exception("Bad[name]");
            }

            if ($act['username'] != $exp[2]) {
                throw new Exception("Bad[username] ");
            }

            if ($act['ref'] != $exp[3]) {
                throw new Exception("Bad[ref] ");
            }
        }
        //throw new PendingException();
    }

}
