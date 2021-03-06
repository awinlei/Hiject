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

use Hiject\Core\Base;

/**
 * Password Reset Model.
 */
class PasswordResetModel extends Base
{
    /**
     * SQL table name.
     *
     * @var string
     */
    const TABLE = 'password_reset';

    /**
     * Token duration (30 minutes).
     *
     * @var int
     */
    const DURATION = 1800;

    /**
     * Get all tokens.
     *
     * @param int $user_id
     *
     * @return array
     */
    public function getAll($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->desc('date_creation')->limit(100)->findAll();
    }

    /**
     * Generate a new reset token for a user.
     *
     * @param string $username
     * @param int    $expiration
     *
     * @return bool|string
     */
    public function create($username, $expiration = 0)
    {
        $user_id = $this->db->table(UserModel::TABLE)->eq('username', $username)->neq('email', '')->notNull('email')->findOneColumn('id');

        if (!$user_id) {
            return false;
        }

        $token = $this->token->getToken();

        $result = $this->db->table(self::TABLE)->insert([
            'token'           => $token,
            'user_id'         => $user_id,
            'date_expiration' => $expiration ?: time() + self::DURATION,
            'date_creation'   => time(),
            'ip'              => $this->request->getIpAddress(),
            'user_agent'      => $this->request->getUserAgent(),
            'is_active'       => 1,
        ]);

        return $result ? $token : false;
    }

    /**
     * Get user id from the token.
     *
     * @param string $token
     *
     * @return int
     */
    public function getUserIdByToken($token)
    {
        return $this->db->table(self::TABLE)->eq('token', $token)->eq('is_active', 1)->gte('date_expiration', time())->findOneColumn('user_id');
    }

    /**
     * Disable all tokens for a user.
     *
     * @param int $user_id
     *
     * @return bool
     */
    public function disable($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->update(['is_active' => 0]);
    }
}
