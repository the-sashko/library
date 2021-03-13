<?php
/**
 * ValuesObject Class For Category Model
 */
class CategoryValuesObject extends ValuesObject
{
    const LINK_FORMAT = '/cat/%s/';

    const AJAX_LINK_FORMAT = '/cat/ajax/%d/';

    const EDIT_LINK_FORMAT = '/cat/edit/%d/';

    const REMOVE_LINK_FORMAT = '/cat/remove/%d/';

    const ADD_LINK = '/cat/add/';

    const TOP_CATEGORIES_LINK = '/';

    /**
     * Get Category ID
     *
     * @return int Category ID
     */
    public function getId(): int
    {
        return (int) $this->get('id');
    }

    /**
     * Get Category Title
     *
     * @return string Title
     */
    public function getTitle(): string
    {
        return (string) $this->get('title');
    }

    /**
     * Get Category Slug
     *
     * @return string Slug
     */
    public function getSlug(): string
    {
        return (string) $this->get('slug');
    }

    /**
     * Get Category Description
     *
     * @return string Description
     */
    public function getDescription(): ?string
    {
        $description = $this->get('description');

        if (empty($description)) {
            return null;
        }

        return (string) $description;
    }

    /**
     * Get Category Parent ID
     *
     * @return int|null Parent ID
     */
    public function getParentId(): ?int
    {
        $parentId = $this->get('id_parent');

        if (empty($parentId)) {
            return null;
        }

        return (int) $parentId;
    }

    /**
     * Get Creation Date
     *
     * @return string|null Date Of Creation
     */
    public function getCdate(): ?string
    {
        $cdate = $this->get('cdate');

        if (empty($cdate)) {
            return null;
        }

        return date('d.m.Y H:i:s', strtotime($cdate));
    }

    /**
     * Get Date Of Last Change
     *
     * @return string|null Date Of Change
     */
    public function getMdate(): ?string
    {
        $mdate = $this->get('mdate');

        if (empty($mdate)) {
            return null;
        }

        return date('d.m.Y H:i:s', strtotime($mdate));
    }

    /**
     * Get Category Parent
     *
     * @return CategoryValuesObject|null Parent Category
     */
    public function getParent(): ?CategoryValuesObject
    {
        if (!$this->has('parent')) {
            return null;
        }

        $parent = $this->get('parent');

        if (empty($parent)) {
            return null;
        }

        return $parent;
    }

    /**
     * Get Category Children
     *
     * @return array|null Children
     */
    public function getChildren(): ?array
    {
        if (!$this->has('children')) {
            return null;
        }

        $children = $this->get('children');

        if (empty($children)) {
            return null;
        }

        return (array) $children;
    }

    /**
     * Get Files
     *
     * @return array|null Files
     */
    public function getFiles(): ?array
    {
        if (!$this->has('files')) {
            return null;
        }

        $files = $this->get('files');

        if (empty($files)) {
            return null;
        }

        return (array) $files;
    }

    /**
     * Get Children Count
     *
     * @return int Count Of Children
     */
    public function getChildrenCount(): int
    {
        $children = $this->getChildren();

        if (empty($children)) {
            return 0;
        }

        $count = count($children);

        foreach ($children as $child) {
            $count = $count + $child->getChildrenCount();
        }

        return $count;
    }

    /**
     * Get Files Count
     *
     * @return int Count Of Files
     */
    public function getFilesCount(): int
    {
        $files    = $this->getFiles();
        $children = $this->getChildren();

        $count = 0;

        if (!empty($files)) {
            $count = count($files);
        }

        if (empty($children)) {
            return $count;
        }

        foreach ($children as $child) {
            $count = $count + $child->getFilesCount();
        }

        return $count;
    }

    /**
     * Get Category Link
     *
     * @return string|null Link
     */
    public function getLink(): ?string
    {
        $slug = $this->getSlug();

        if (empty($slug)) {
            return null;
        }

        return sprintf(static::LINK_FORMAT, $slug);
    }

    /**
     * Get Ajax Link
     *
     * @return string Link
     */
    public function getAjaxLink(): string
    {
        $id = (int) $this->getId();

        return sprintf(static::AJAX_LINK_FORMAT, $id);
    }

    /**
     * Get Add Link
     *
     * @return string Link
     */
    public function getAddLink(): string
    {
        return static::ADD_LINK;
    }

    /**
     * Get Remove Link
     *
     * @return string Link
     */
    public function getRemoveLink(): string
    {
        $id = (int) $this->getId();

        return sprintf(static::REMOVE_LINK_FORMAT, $id);
    }

    /**
     * Get Edit Link
     *
     * @return string Link
     */
    public function getEditLink(): string
    {
        $id = (int) $this->getId();

        return sprintf(static::EDIT_LINK_FORMAT, $id);
    }

    /**
     * Get Parent Category Link
     *
     * @return string|null Parent Link
     */
    public function getParentLink(): ?string
    {
        $parent = $this->getParent();

        if (empty($parent)) {
            return static::TOP_CATEGORIES_LINK;
        }

        return $parent->getLink();
    }

    /**
     * Get Parent Category Link
     *
     * @return string|null Parent Link
     */
    public function getSeoDescription(): ?string
    {
        $title = $this->getTitle();

        if (empty($title)) {
            return null;
        }

        return sprintf(__t('Category «%s»'), $title);
    }

    /**
     * Is Category Has Parent
     *
     * @return bool Is Category Has Parent
     */
    public function hasParent(): bool
    {
        if (empty($this->getParent())) {
            return false;
        }

        return true;
    }

    /**
     * Set Category Title
     *
     * @param string|null $title Category Title
     */
    public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * Set Category Slug
     *
     * @param string|null $slug Category Slug
     */
    public function setSlug(?string $slug = null): void
    {
        $this->set('slug', $slug);
    }

    /**
     * Set Category Parent ID
     *
     * @param int|null $parentId Parent ID
     */
    public function setParentId(?string $parentId = null): void
    {
        $this->set('id_parent', $parentId);
    }

    /**
     * Set Creation Date
     */
    public function setCdate(): void
    {
        $this->set('cdate', date('Y-m-d H:i:s'));
    }

    /**
     * Set Date Of Last Change
     */
    public function setMdate(): void
    {
        $this->set('mdate', date('Y-m-d H:i:s'));
    }

    /**
     * Set Category Parent
     *
     * @param CategoryValuesObject|null $parent Parent
     */
    public function setParent(?CategoryValuesObject $parent = null): void
    {
        $this->set('parent', $parent);
    }

    /**
     * Set Category Children
     *
     * @param array|null $children List Of Children
     */
    public function setChildren(?array $children = null): void
    {
        $this->set('children', $children);
    }

    /**
     * Set Category Files
     *
     * @param array|null $files List Of Files
     */
    public function setFiles(?array $files = null): void
    {
        $this->set('files', $files);
    }
}
