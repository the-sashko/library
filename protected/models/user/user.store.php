<?php
/**
 * ModelStore Class For User Model
 */
class UserStore extends ModelStoreCore
{
    /**
     * @var string Table Of Users
     */
    const TABLE_USERS = 'users';

    /**
     * @var string Database Queries Cache Scope
     */
    public $scope = 'user';

    /**
     * Insert Row To Users
     *
     * @param array|null $row Row
     *
     * @return bool Is Row Successfully Inserted
     */
    public function insertUser(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(static::TABLE_USERS, $row);
    }

    /**
     * Get Row User By ID
     *
     * @param int|null $id User ID
     *
     * @return array|null User Row
     */
    public function getRowById(?int $id = null): ?array
    {
        if (empty($id)) {
            return null;
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE
                "id"        = \'%d\' AND
                "is_active" = true;
        ';

        $sql = sprintf($sql, static::TABLE_USERS, $id);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Get Row User By Email
     *
     * @param string|null $email User Email
     *
     * @return array|null User Row
     */
    public function getRowByEmail(?string $email = null): ?array
    {
        if (empty($email)) {
            return null;
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE
                "email"     = \'%s\' AND
                "is_active" = true;
        ';

        $sql = sprintf($sql, static::TABLE_USERS, $email);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Update User Token
     *
     * @param int|null    $id    User ID
     * @param string|null $token User Token
     *
     * @return bool Is User Token Successfully Updated
     */
    public function updateRowToken(
        ?int    $id    = null,
        ?string $token = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'token' => (string) $token
        ];

        return $this->updateRowById(static::TABLE_USERS, $row, $id);
    }

    /**
     * Update User Password Hash
     *
     * @param int|null    $id           User ID
     * @param string|null $passwordHash User Password Hash
     *
     * @return bool Is User Token Successfully Updated
     */
    public function updateRowPasswordHash(
        ?int    $id           = null,
        ?string $passwordHash = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = [
            'password_hash' => (string) $passwordHash,
            'mdate'         => date('Y-m-d H:i:s')
        ];

        return $this->updateRowById(static::TABLE_USERS, $row, $id);
    }
}
