<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//
require "./../../../bootstrap.php";

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{

    private $baseUrl = "http://localhost/sr";
    private static $db;
    private $restObject        = null;
    private $response          = null;

    /**
     * Initializes context.
     * Every scenario gets its own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        self::log("New Test Starting: ".date('m/d/Y h:i:s'));
        $dbh = new \SimpleRoles\DB();
        self::$db = $dbh->getConn();

        $this->restObject = new stdClass();
        $this->client = new Guzzle\Service\Client();
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
            $stmt->execute($row);
        }
        //throw new PendingException();
    }

    /**
     * @When /^I issue a "([^"]*)" request to "([^"]*)"$/
     */
    public function iIssueARequestTo($verb, $page)
    {
        $this->requestUrl = $this->baseUrl.$page;

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
                } catch (\Guzzle\Http\Exception\RequestException $e) {
                    $resp = $e->getResponse();
                }
                break;
            case 'put':
                try {
                    $resp = $this->client->put($this->requestUrl)
                        ->send();
                } catch (\Guzzle\Http\Exception\RequestException $e) {
                    $resp = $e->getResponse();
                }
                break;
        }
        $this->response = $resp;
        //throw new PendingException();
    }

    /**
     * @Then /^The Response Code will be "([^"]*)"$/
     */
    public function theResponseCodeWillBe($code)
    {
        $testCode = $this->response->getStatusCode();
        if ($code != $testCode) {
            throw new Exception("Invliad Response Code.  Expected: $code got: $testCode");
        }
    }

    /**
     * @Given /^The response is JSON$/
     */
    public function theResponseIsJson()
    {
        $data = $this->response->json();
        if (empty($data)) {
            throw new Exception("Request was not JSON");
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
            $act = $data[$i];
            $exp = $rows[$i];
            if ($act['id'] != $exp[0]) {
                throw new Exception("Bad[id]");
            }

            if ($act['name'] != $exp[1]) {
                throw new Exception("Bad[name]");
            }

            if ($act['description'] != $exp[2]) {
                throw new Exception("Bad[desc] ");
            }
        }
        //throw new PendingException();
    }
}
