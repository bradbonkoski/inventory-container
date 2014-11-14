<?php

namespace SimpleRoles;

use Symfony\Component\Yaml\Yaml;

class Config
{

    public static function getConfigValue($section, $key)
    {
        $path = BASE_PATH."/config";
        $config = Yaml::parse(file_get_contents("$path/app.yml"));

        if (array_key_exists($section, $config)) {
            return $config[$section][$key];
        }

        throw new \Exception("Conf Exception: Key ($key) in Section ($section) does not exist");
    }

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
