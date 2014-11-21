<?php

namespace SimpleRoles;

/**
 * Config Base Class
 *
 * PHP Version 5.3+
 *
 * LICENSE: Version 2.0 (the "License"); you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the License.
 *
 * @category Config
 * @package SimpleRoles
 * @author Brad Bonkoski <brad.bonkoski@gmail.com>
 * @copyright 2014 Brad Bonkoski
 * @link https://github.com/bradbonkoski/simpleRoles
 */

use Symfony\Component\Yaml\Yaml;

class Config
{

    /**
     * @param $section
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public static function getConfigValue($section, $key)
    {
        $path = BASE_PATH."/config";
        $config = Yaml::parse(file_get_contents("$path/app.yml"));

        if (array_key_exists($section, $config)) {
            return $config[$section][$key];
        }
        throw new \Exception("Conf Exception: Key ($key) in Section ($section) does not exist");
    }

    /**
     * @param $section
     * @return mixed
     * @throws \Exception
     */
    public static function getConfigSection($section)
    {
        $path = BASE_PATH."/config";
        $config = Yaml::parse(file_get_contents("$path/app.yml"));

        if (array_key_exists($section, $config)) {
            return $config[$section];
        }
        throw new \Exception("Conf Exception: Section ($section) does not exist");
    }
}
