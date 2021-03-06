<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Model;

use Hiject\Core\Security\Token;

/**
 * Config model.
 */
class ConfigModel extends SettingModel
{
    /**
     * Get a config variable with in-memory caching.
     *
     * @param string $name          Parameter name
     * @param string $default_value Default value of the parameter
     *
     * @return string
     */
    public function get($name, $default_value = '')
    {
        $options = $this->memoryCache->proxy($this, 'getAll');

        return isset($options[$name]) && $options[$name] !== '' ? $options[$name] : $default_value;
    }

    /**
     * Optimize the Sqlite database.
     *
     * @return bool
     */
    public function optimizeDatabase()
    {
        return $this->db->getConnection()->exec('VACUUM');
    }

    /**
     * Compress the Sqlite database.
     *
     * @return string
     */
    public function downloadDatabase()
    {
        return gzencode(file_get_contents(DB_FILENAME));
    }

    /**
     * Get the Sqlite database size in bytes.
     *
     * @return int
     */
    public function getDatabaseSize()
    {
        return DB_DRIVER === 'sqlite' ? filesize(DB_FILENAME) : 0;
    }

    /**
     * Regenerate a token.
     *
     * @param string $option Parameter name
     *
     * @return bool
     */
    public function regenerateToken($option)
    {
        return $this->save([$option => Token::getToken()]);
    }

    /**
     * Prepare data before save.
     *
     * @param array $values
     *
     * @return array
     */
    public function prepare(array $values)
    {
        if (!empty($values['application_url']) && substr($values['application_url'], -1) !== '/') {
            $values['application_url'] = $values['application_url'].'/';
        }

        return $values;
    }
}
