<?php
/**
 * ModelStore Class For File Model
 */
class FileStore extends ModelStoreCore
{
    const TABLE_FILES = 'files';

    /**
     * @var string Database Queries Cache Scope
     */
    public $scope = 'file';

    /**
     * Insert Row To Files
     *
     * @param array|null $row Row
     *
     * @return bool Is Row Successfully Inserted
     */
    public function insertFile(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(static::TABLE_FILES, $row);
    }

    /**
     * Get Row File By ID
     *
     * @param int|null $id File ID
     *
     * @return array|null File Row
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

        $sql = sprintf($sql, static::TABLE_FILES, $id);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Get Files By Category ID
     *
     * @param int|null $categoryId Category ID
     *
     * @return array|null File Row
     */
    public function getRowsByCategoryId(?int $categoryId = null): ?array
    {
        if (empty($categoryId)) {
            return null;
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE
                "id_category" = \'%d\' AND
                "is_active"   = true
            ORDER BY short_title;
        ';

        $sql = sprintf($sql, static::TABLE_FILES, $categoryId);

        $rows = $this->getRows($sql);

        if (empty($rows)) {
            return null;
        }

        return $rows;
    }

    /**
     * Get Row File By Slug
     *
     * @param string|null $slug Slug
     *
     * @return array|null File Row
     */
    public function getRowBySlug(?string $slug = null): ?array
    {
        if (empty($slug)) {
            return null;
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE
                "slug"      = \'%s\' AND
                "is_active" = true;
        ';

        $sql = sprintf($sql, static::TABLE_FILES, $slug);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Get Row File By Title
     *
     * @param string|null $title Title
     *
     * @return array|null File Row
     */
    public function getRowByTitle(?string $title = null): ?array
    {
        if (empty($title)) {
            return null;
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE "title" = \'%s\';
        ';

        $sql = sprintf($sql, static::TABLE_FILES, $title);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Get Row File By Short Title
     *
     * @param string|null $shortTitle Short Title
     *
     * @return array|null File Row
     */
    public function getRowByShortTitle(?string $shortTitle = null): ?array
    {
        if (empty($shortTitle)) {
            return null;
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE "short_title" = \'%s\';
        ';

        $sql = sprintf($sql, static::TABLE_FILES, $shortTitle);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Update File By ID
     *
     * @param int|null   $id  File ID
     * @param array|null $row File Row
     *
     * @return bool Is File Successfully Updated
     */
    public function updateFileById(
        ?int   $id  = null,
        ?array $row = null
    ): bool
    {
        if (empty($id)) {
            return false;
        }

        if (empty($row)) {
            return false;
        }

        return $this->updateRowById(static::TABLE_FILES, $row, $id);
    }

    /**
     * Update File By Category ID
     *
     * @param int|null   $categoryId Category ID
     * @param array|null $row        File Row
     *
     * @return bool Is File Successfully Updated
     */
    public function updateFilesByCategoryId(
        ?int   $categoryId = null,
        ?array $row        = null
    ): bool
    {
        if (empty($categoryId)) {
            return false;
        }

        if (empty($row)) {
            return false;
        }

        $condition = sprintf('"id_category" = \'%d\'', $categoryId);

        return $this->updateRows(static::TABLE_FILES, $row, $condition);
    }
}
