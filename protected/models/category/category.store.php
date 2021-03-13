<?php
/**
 * ModelStore Class For Category Model
 */
class CategoryStore extends ModelStoreCore
{
    const TABLE_CATEGORIES = 'categories';

    /**
     * @var string Database Queries Cache Scope
     */
    public $scope = 'category';

    /**
     * Insert Row To Category
     *
     * @param array|null $row Row
     *
     * @return bool Is Row Successfully Inserted
     */
    public function insertCategory(?array $row = null): bool
    {
        if (empty($row)) {
            return false;
        }

        return $this->addRow(static::TABLE_CATEGORIES, $row);
    }

    /**
     * Get Row Category By ID
     *
     * @param int|null $id Category ID
     *
     * @return array|null Category Row
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

        $sql = sprintf($sql, static::TABLE_CATEGORIES, $id);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Get Row Category By Slug
     *
     * @param string|null $slug Slug
     *
     * @return array|null Category Row
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

        $sql = sprintf($sql, static::TABLE_CATEGORIES, $slug);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Get Row Category By Title
     *
     * @param string|null $title Title
     *
     * @return array|null Category Row
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

        $sql = sprintf($sql, static::TABLE_CATEGORIES, $title);

        $row = $this->getRow($sql);

        if (empty($row)) {
            return null;
        }

        return $row;
    }

    /**
     * Get Categories By Parent ID
     *
     * @param int|null $parentId Parent ID
     *
     * @return array|null Category Rows
     */
    public function getRowsByParentId(?int $parentId = null): ?array
    {
        if (empty($parentId)) {
            return null;
        }

        $sql = '
            SELECT *
            FROM "%s"
            WHERE
                "id_parent" = \'%d\' AND
                "is_active" = true;
        ';

        $sql = sprintf($sql, static::TABLE_CATEGORIES, $parentId);

        $rows = $this->getRows($sql);

        if (empty($rows)) {
            return null;
        }

        return $rows;
    }

    /**
     * Get All Categories
     *
     * @return array|null Category Rows
     */
    public function getAllCategories(): ?array
    {
        $sql = '
            SELECT *
            FROM "%s"
            WHERE "is_active" = true;
        ';

        $sql = sprintf($sql, static::TABLE_CATEGORIES);

        $rows = $this->getRows($sql);

        if (empty($rows)) {
            return null;
        }

        return $rows;
    }

    /**
     * Get Top Categories
     *
     * @return array|null Category Rows
     */
    public function getTopCategories(): ?array
    {
        $sql = '
            SELECT *
            FROM "%s"
            WHERE
                "id_parent" IS NULL AND
                "is_active" = true;
        ';

        $sql = sprintf($sql, static::TABLE_CATEGORIES);

        $rows = $this->getRows($sql);

        if (empty($rows)) {
            return null;
        }

        return $rows;
    }

    /**
     * Update Category By ID
     *
     * @param int|null   $id  Category ID
     * @param array|null $row Category Row
     *
     * @return bool Is Category Successfully Updated
     */
    public function updateCategoryById(
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

        return $this->updateRowById(static::TABLE_CATEGORIES, $row, $id);
    }
}
