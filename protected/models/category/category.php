<?php

/**
 * ModelCore Class For Category Model
 */
class Category extends ModelCore
{
    const ERROR_CAN_NOT_REMOVE_CATEGORY = 'Can Not Remove Category (ID: %d)';

    const EMPTY_SLUG_VALUE = 'empty';

    const FORBIDDEN_SLUGS = [
        'add',
        'all',
        'edit',
        'remove',
        'main'
    ];

    /**
     * Create Category
     *
     * @param array $inputData Input Data
     *
     * @return CategoryFormObject Category Form Object
     */
    public function add(array $inputData): CategoryFormObject
    {
        $formObject = new CategoryFormObject($inputData);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $slug     = $this->_getSlugFromFormObject($formObject);
        $title    = $formObject->getTitle();
        $parentId = $formObject->getParentId();

        $markupPlugin = $this->getPlugin('markup');

        if (!empty($title)) {
            $title = $markupPlugin->normalizeText($title);
        }

        $title = preg_replace('/\s+/su', ' ', $title);

        $formObject->setTitle($title);
        $formObject->setSlug($slug);

        if (!empty($this->getVOByTitle($title))) {
            $formObject->setError(
                CategoryFormObject::ERROR_TITLE_ALREADY_EXISTS
            );
        }

        if (!empty($parentId) && empty($this->getVOById($parentId))) {
            $formObject->setError(
                CategoryFormObject::ERROR_INVALID_PARENT_CATEGORY
            );
        }

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $row = [
            'title' => $title,
            'slug'  => $slug
        ];

        if (!empty($parentId)) {
            $row['id_parent'] = $parentId;
        }

        if ($this->store->insertCategory($row)) {
            $formObject->setSuccess();
        }

        return $formObject;
    }

    /**
     * Edit Category
     *
     * @param int|null $id        Category ID
     * @param array    $inputData Input Data
     *
     * @return CategoryFormObject Category Form Object
     */
    public function edit(?int $id = null, array $inputData): CategoryFormObject
    {
        $formObject = new CategoryFormObject($inputData);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        if (empty($id)) {
            return $formObject;
        }

        $slug     = $this->_getSlugFromFormObject($formObject, $id);
        $title    = $formObject->getTitle();
        $parentId = $formObject->getParentId();

        $markupPlugin = $this->getPlugin('markup');

        if (!empty($title)) {
            $title = $markupPlugin->normalizeText($title);
        }

        $title = preg_replace('/\s+/su', ' ', $title);

        $formObject->setTitle($title);
        $formObject->setSlug($slug);

        $category = $this->getVOByTitle($title);

        if (!empty($category) && $category->getId() != $id) {
            $formObject->setError(
                CategoryFormObject::ERROR_TITLE_ALREADY_EXISTS
            );
        }

        if (
            !empty($parentId) &&
            empty($this->getVOById($parentId)) &&
            $id == $parentId
        ) {
            $formObject->setError(
                CategoryFormObject::ERROR_INVALID_PARENT_CATEGORY
            );
        }

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $row = [
            'title' => $title,
            'slug'  => $slug,
            'mdate' => date('Y-m-d H:i:s')
        ];

        if (!empty($parentId)) {
            $row['id_parent'] = $parentId;
        }

        if ($this->store->updateCategoryById($id, $row)) {
            $formObject->setSuccess();
        }

        return $formObject;
    }

    /**
     * Get All Categories
     *
     * @return array|null List Of Categories
     */
    public function getAll(): ?array
    {
        $rows = $this->store->getAllCategories();

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * Get Top Categories
     *
     * @return array|null List Of Categories
     */
    public function getTop(): ?array
    {
        $rows = $this->store->getTopCategories();

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * Get Category By ID
     *
     * @param int|null $id ID
     *
     * @return CategoryValuesObject|null Category
     */
    public function getVOById(?int $id = null): ?CategoryValuesObject
    {
        if (empty($id)) {
            return null;
        }

        $row = $this->store->getRowById($id);

        if (empty($row)) {
            return null;
        }

        return $this->getVO($row);
    }

    /**
     * Get Category By Slug
     *
     * @param string|null $slug Slug
     *
     * @return CategoryValuesObject|null Category
     */
    public function getVOBySlug(?string $slug = null): ?CategoryValuesObject
    {
        if (empty($slug)) {
            return null;
        }

        $row = $this->store->getRowBySlug($slug);

        if (empty($row)) {
            return null;
        }

        $categoryVO = $this->getVO($row);
        $parentVO   = $this->getVOById($categoryVO->getParentId());

        if (!empty($parentVO)) {
            $categoryVO->setParent($parentVO);
        } 

        return $categoryVO;
    }

    /**
     * Get Category By Title
     *
     * @param string|null $title Title
     *
     * @return CategoryValuesObject|null Category
     */
    public function getVOByTitle(?string $title = null): ?CategoryValuesObject
    {
        if (empty($title)) {
            return null;
        }

        $row = $this->store->getRowByTitle($title);

        if (empty($row)) {
            return null;
        }

        return $this->getVO($row);
    }

    /**
     * Soft Remove Category By ID
     *
     * @param int|null $id Category ID
     *
     * @return bool Is Category Successfully Soft Removed
     */
    public function removeById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $category = $this->getVOById($id); 

        if (empty($category)) {
            return false;
        }

        $row = [
            'ddate'     => date('Y-m-d H:i:s'),
            'is_active' => 'f'
        ];

        $this->store->start();

        try {
            if (!$this->store->updateCategoryById($id, $row)) {
                $errorMessage = sprintf(
                    __t(static::ERROR_CAN_NOT_REMOVE_CATEGORY),
                    $id
                );

                throw new Exception($errorMessage);
            }

            if (!$this->getModel('file')->removeByCategoryId($id)) {
                $errorMessage = sprintf(
                    __t(static::ERROR_CAN_NOT_REMOVE_CATEGORY),
                    $id
                );

                throw new Exception($errorMessage);
            }

            foreach ((array) $category->getChildren() as $child) {
                $this->removeById($child->getId());
            }

            return $this->store->commit();
        } catch (Exception $exp) {
            $this->store->rollback();

            throw new Exception($exp);
        }

        $this->store->rollback();

        return false;
    }

    /**
     * Get Hierarchy Of Category
     *
     * @param int|null $id Category ID
     *
     * @return array|null List Of Hierarchy Links
     */
    public function getHierarchy(?int $id = null): ?array
    {
        if (empty($id)) {
            return null;
        }

        $category = $this->getVOById($id);

        if (empty($category)) {
            return null;
        }

        $hierarchy = [
            $category->getLink() => $category->getTitle()
        ];

        while (!empty($category->getParentId())) {
            $category = $this->getVOById($category->getParentId());

            if (empty($category)) {
                break;
            }

            $hierarchy[$category->getLink()] = $category->getTitle();
        }

        return array_reverse($hierarchy);
    }

    /**
     * Get Category Value Object Instance
     *
     * @param array|null $row Category Values
     *
     * @return ValuesObject Category Value Object
     */
    public function getVO(?array $row = null): ValuesObject
    {
        $categoryVO = parent::getVO($row);

        $id           = $categoryVO->getId();
        $childrenRows = $this->store->getRowsByParentId($id);
        $files        = $this->getModel('file')->getByCategoryId($id);

        if (!empty($childrenRows)) {
            $childrenVOs = $this->getVOArray($childrenRows);

            $categoryVO->setChildren($childrenVOs);
        }

        if (!empty($files)) {
            $categoryVO->setFiles($files);
        }

        return $categoryVO;
    }

    /**
     * Get Category Slug From Form Object
     *
     * @param CategoryFormObject &$formObject Form Object
     * @param int|null           $id          Category ID
     *
     * @return string Slug
     */
    private function _getSlugFromFormObject(
        CategoryFormObject &$formObject,
        ?int               $id           = null
    ): string
    {
        $slug = $formObject->getSlug();

        if (empty($slug)) {
            $slug = $formObject->getTitle();
        }

        $translitPlugin = $this->getPlugin('translit');
        $slug           = $translitPlugin->getSlug($slug);

        if (empty($slug)) {
            $slug = static::EMPTY_SLUG_VALUE;
        }

        if ($this->_isSlugCorrect($slug, $id)) {
            return $slug;
        }

        $count = 0;

        if (preg_match('/^(.*?)\-([0-9]+)$/su', $slug)) {
            $count = (int) preg_replace('/^(.*?)\-([0-9]+)$/su', '$2', $slug);
            $slug  = preg_replace('/^(.*?)\-([0-9]+)$/su', '$1', $slug);
        }

        do {
            $count++;

            $uniqSlug = sprintf('%s-%d', $slug, $count);
        } while (!$this->_isSlugCorrect($uniqSlug, $id));

        if (strlen($uniqSlug) > CategoryFormObject::SLUG_MAX_LENGTH) {
            $formObject->setError(
                CategoryFormObject::ERROR_SLUG_ALREADY_EXISTS
            );
        }

        return $uniqSlug;
    }

    /**
     * Is Slug Has Correct Value
     *
     * @param string|null $slug Slug
     * @param int|null    $id   Category ID
     *
     * @return bool Is Slug Has Correct Value
     */
    private function _isSlugCorrect(
        ?string $slug = null,
        ?int    $id   = null
    ): bool
    {
        return !empty($slug) &&
               $this->_isSlugUnique($slug, $id) &&
               !in_array($slug, static::FORBIDDEN_SLUGS);
    }

    /**
     * Is Slug Unique
     *
     * @param string|null $slug Slug
     * @param int|null    $id   Category ID For Exclude In Checking
     *
     * @return bool Is Slug Exists
     */
    private function _isSlugUnique(
        ?string $slug = null,
        ?int    $id   = null
    ): bool
    {
        if (empty($slug)) {
            return false;
        }

        $category = $this->getVOBySlug($slug);

        if (empty($category)) {
            return true;
        }

        return $category->getId() == $id;
    }
}
