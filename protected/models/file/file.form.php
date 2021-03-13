<?php
/**
 * Class For File Form Object
 */
class FileFormObject extends FormObject
{
    const TITLE_MIN_LENGTH = 3;
    const TITLE_MAX_LENGTH = 128;

    const SHORT_TITLE_MIN_LENGTH = 3;
    const SHORT_TITLE_MAX_LENGTH = 64;

    const SLUG_MAX_LENGTH = 64;

    const DESCRIPTION_MAX_LENGTH = 5120;

    const UPLOAD_FILE_NAME = 'file';

    const ERROR_TITLE_NOT_SET   = 'Title Is Not Set';
    const ERROR_TITLE_TOO_SHORT = 'Title Too Short';
    const ERROR_TITLE_TOO_LONG  = 'Title Too Long';

    const ERROR_SHORT_TITLE_NOT_SET   = 'Short Title Is Not Set';
    const ERROR_SHORT_TITLE_TOO_SHORT = 'Short Title Too Short';
    const ERROR_SHORT_TITLE_TOO_LONG  = 'Short Title Too Long';

    const ERROR_CATEGORY_NOT_SET = 'Category Is Not Set';

    const ERROR_SLUG_TOO_LONG = 'Slug Too Long';

    const ERROR_DESCRIPTION_TOO_LONG = 'Description Too Long';

    const ERROR_TITLE_ALREADY_EXISTS = 'Title Already Exists';

    const ERROR_SHORT_TITLE_ALREADY_EXISTS = 'Short Title Already Exists';
    
    const ERROR_INVALID_CATEGORY = 'Invalid Category';

    const ERRROR_SLUG_ALREADY_EXISTS = 'Slug Already Exists';

    const ERROR_EMPTY_FILE             = 'File For Upload Is Empty';
    const ERROR_FILE_HAS_BAD_EXTENSION = 'Upload File Has Bad Extension';
    const ERROR_CAN_NOT_UPLOAD_FILE    = 'Can Not Upload File';
    const ERROR_FILE_IS_TOO_LARGE      = 'Upload File Is Too Large';

    const ERROR_CAN_NOT_CREATE_QR_FILE = 'Can Not Create QR File';

    /**
     * Check Input Data
     */
    public function checkInputData(): void
    {
        $this->_checkTitle();
        $this->_checkShortTitle();
        $this->_checkCategory();
        $this->_checkSlug();
        $this->_checkDescription();
    }

    /**
     * Get File Title From Form
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
     * Get File Short Title From Form
     *
     * @return string|null Short Title
     */
    public function getShortTitle(): ?string
    {
        $shortTitle = $this->getTitle();

        if ($this->has('short_title')) {
            $shortTitle = $this->get('short_title');
        }

        if (empty($shortTitle)) {
            return $this->getTitle();
        }

        return $shortTitle;
    }

    /**
     * Get File Slug From Form
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
     * Get Category ID From Form
     *
     * @return int|null Slug
     */
    public function getCategoryId(): ?int
    {
        $categoryId = null;

        if ($this->has('id_category')) {
            $categoryId = $this->get('id_category');
        }

        if (empty($categoryId)) {
            return null;
        }

        return (int) $categoryId;
    }

    /**
     * Get File Description From Form
     *
     * @return string|null Slug
     */
    public function getDescription(): ?string
    {
        $description = null;

        if ($this->has('description')) {
            $description = $this->get('description');
        }

        if (empty($description)) {
            return null;
        }

        return $description;
    }

    /**
     * Get File Path From Form
     *
     * @return string|null File Path
     */
    public function getFilePath(): ?string
    {
        if ($this->has('file_path')) {
            return $this->get('file_path');
        }

        return null;
    }

    /**
     * Set File Title To Form
     *
     * @param string|null $title File Title
     */
    public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * Set File Short Title To Form
     *
     * @param string|null $shortTitle File Short Title
     */
    public function setShortTitle(?string $shortTitle = null): void
    {
        $this->set('short_title', $shortTitle);
    }

    /**
     * Set File Slug To Form
     *
     * @param string|null $slug File Slug
     */
    public function setSlug(?string $slug = null): void
    {
        $this->set('slug', $slug);
    }

    /**
     * Set File Description To Form
     *
     * @param string|null $description File Description
     */
    public function setDescription(?string $description = null): void
    {
        $this->set('description', $description);
    }

    /**
     * Set File Path To Form
     *
     * @param string|null $filePath File Path
     */
    public function setFilePath(?string $filePath = null): void
    {
        $this->set('file_path', $filePath);
    }

    /**
     * Check File Title
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
     * Check File Short Title
     *
     * @return bool Is Short Title Has Correct Format
     */
    private function _checkShortTitle(): bool
    {
        $shortTitle = $this->getShortTitle();

        if (empty($shortTitle)) {
            $shortTitle = $this->getTitle();
        }

        if (empty($shortTitle)) {
            $this->setError(static::ERROR_SHORT_TITLE_NOT_SET);

            return false;
        }

        if (strlen($shortTitle) < static::SHORT_TITLE_MIN_LENGTH) {
            $this->setError(static::ERROR_SHORT_TITLE_TOO_SHORT);

            return false;
        }

        if (strlen($shortTitle) > static::SHORT_TITLE_MAX_LENGTH) {
            $this->setError(static::ERROR_SHORT_TITLE_TOO_LONG);

            return false;
        }

        return true;
    }

    /**
     * Check File Category
     *
     * @return bool Is Category ID Has Correct Format
     */
    private function _checkCategory(): bool
    {
        $categoryId = $this->getCategoryId();

        if (empty($categoryId)) {
            $this->setError(static::ERROR_CATEGORY_NOT_SET);

            return false;
        }

        return true;
    }

    /**
     * Check File Slug From Form
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

    /**
     * Check File Description From Form
     *
     * @return bool Is Description Has Correct Format
     */
    private function _checkDescription(): bool
    {
        $description = $this->getDescription();

        if (empty($description)) {
            return false;
        }

        if (strlen($description) > static::DESCRIPTION_MAX_LENGTH) {
            $this->setError(static::ERROR_DESCRIPTION_TOO_LONG);

            return false;
        }

        return true;
    }
}
