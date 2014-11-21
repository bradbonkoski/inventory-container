<?php

namespace SimpleRoles;

/**
 * DB Base Class
 *
 * PHP Version 5.3+
 *
 * LICENSE: Version 2.0 (the "License"); you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the License.
 *
 * @category DB\Controller
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 * @copyright 2014 Brad Bonkoski
 * @link https://github.com/bradbonkoski/simpleRoles
 */

class DB extends \PDO
{
    protected $db;

    /**
     * @param $dbname
     * @param $host
     * @param $port
     * @return string
     */
    private function buildDSN($dbname, $host, $port)
    {
        $dsn = "mysql:dbname=$dbname;host=$host;port=$port";
        return $dsn;
    }

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $config = Config::getConfigSection('db');

        $dsn = $this->buildDSN(
            $config['dbname'],
            $config['host'],
            $config['port']
        );

        $this->db = new \PDO($dsn, $config['user'], $config['pass']);
        $this->db->setAttribute(
            \PDO::ATTR_DEFAULT_FETCH_MODE,
            \PDO::FETCH_ASSOC
        );
    }

    /**
     * Helper Function to get direct access to the PDO object contained in the class
     * 
     * @return \PDO
     */
    public function getConn()
    {
        return $this->db;
    }
}
