<?php
/**
 * Class For Category Form Object
 */
class CategoryFormObject extends FormObject
{
    const TITLE_MIN_LENGTH = 3;
    const TITLE_MAX_LENGTH = 128;

    const SLUG_MAX_LENGTH = 64;

    const ERROR_TITLE_NOT_SET   = 'Title Is Not Set';
    const ERROR_TITLE_TOO_SHORT = 'Title Too Short';
    const ERROR_TITLE_TOO_LONG  = 'Title Too Long';

    const ERROR_SLUG_TOO_LONG = 'Slug Too Long';

    const ERROR_TITLE_ALREADY_EXISTS = 'Title Already Exists';
    
    const ERROR_INVALID_PARENT_CATEGORY = 'Invalid Parent Category';

    const ERRROR_SLUG_ALREADY_EXISTS = 'Slug Already Exists';

    /**
     * Check Input Data
     */
    public function checkInputData(): void
    {
        $this->_checkTitle();
        $this->_checkSlug();
    }

    /**
     * Get Category Title From Form
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        $title = null;

        if ($this->has('title')) {
            $title = $this->get('title');
        }

        if (empty($title)) {
            return null;
        }

        return $title;
    }

    /**
     * Get Category Slug From Form
     *
     * @return string|null Slug
     */
    public function getSlug(): ?string
    {
        $slug = null;

        if ($this->has('slug')) {
            $slug = $this->get('slug');
        }

        if (empty($slug)) {
            return null;
        }

        return $slug;
    }

    /**
     * Get Parent ID From Form
     *
     * @return int|null Slug
     */
    public function getParentId(): ?int
    {
        $parentId = null;

        if ($this->has('id_parent')) {
            $parentId = $this->get('id_parent');
        }

        if (empty($parentId)) {
            return null;
        }

        return (int) $parentId;
    }

    /**
     * Set Category Title To Form
     *
     * @param string|null $title Category Title
     */
    public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * Set Category Slug To Form
     *
     * @param string|null $slug Category Slug
     */
    public function setSlug(?string $slug = null): void
    {
        $this->set('slug', $slug);
    }

    /**
     * Check Category Title
     *
     * @return bool Is Title Has Correct Format
     */
    private function _checkTitle(): bool
    {
        $title = $this->getTitle();

        if (empty($title)) {
            $this->setError(static::ERROR_TITLE_NOT_SET);

            return false;
        }

        if (strlen($title) < static::TITLE_MIN_LENGTH) {
            $this->setError(static::ERROR_TITLE_TOO_SHORT);

            return false;
        }

        if (strlen($title) > static::TITLE_MAX_LENGTH) {
            $this->setError(static::ERROR_TITLE_TOO_LONG);

            return false;
        }

        return true;
    }

    /**
     * Check Category Slug From Form
     *
     * @return bool Is Slug Has Correct Format
     */
    private function _checkSlug(): bool
    {
        $slug = $this->getSlug();

        if (empty($slug)) {
            return false;
        }

        if (strlen($slug) > static::SLUG_MAX_LENGTH) {
            $this->setError(static::ERROR_SLUG_TOO_LONG);

            return false;
        }

        return true;
    }
}
