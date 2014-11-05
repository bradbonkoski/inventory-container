<?php

namespace SimpleRoles;

use Symfony\Component\Yaml\Yaml;

class Config
{
    const KEYROOT = 'SIMPLE-ROLES-CONF';

    public static function getConfigValue($section, $key)
    {
        if (apc_exists(Config::KEYROOT)) {
            $config = apc_fetch(Config::KEYROOT);
        } else {
            $path = BASE_PATH."/config";
            $config = Yaml::parse(file_get_contents("$path/app.yml"));
            apc_store(Config::KEYROOT, $config, 0);
        }

        if (array_key_exists($section, $config)) {
            return $config[$section][$key];
        }

        throw new \Exception("Conf Exception: Key ($key) in Section ($section) does not exist");
    }

    public static function getConfigSection($section)
    {
        apc_clear_cache();
        if (apc_exists(Config::KEYROOT)) {
            $config = apc_fetch(Config::KEYROOT);
        } else {
            $path = BASE_PATH."/config";
            $config = Yaml::parse(file_get_contents("$path/app.yml"));
            apc_store(Config::KEYROOT, $config, 0);
        }

        if (array_key_exists($section, $config)) {
            return $config[$section];
        }

        throw new \Exception("Conf Exception: Section ($section) does not exist");
    }
}
