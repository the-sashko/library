<?php
/**
 * ValuesObject Class For File Model
 */
class FileValuesObject extends ValuesObject
{
    const LINK_FORMAT = '/file/%s/';

    const AJAX_LINK_FORMAT = '/file/ajax/%d/';

    const EDIT_LINK_FORMAT = '/file/edit/%d/';

    const REMOVE_LINK_FORMAT = '/file/remove/%d/';

    const ADD_LINK = '/file/add/';

    const FILE_TYPES_DIR = '/assets/img/types';

    const FILE_TYPES_DIR_PATH = __DIR__.'/../../../public/assets/img/types';

    const DEFAULT_PREVIEW = '/assets/img/types/default.png';
    const DEFAULT_PDF_PREVIEW = '/assets/img/types/pdf.png';

    const PREVIEW_FILE_WIDTH = 300;

    const EXTENSIONS = [
        'pdf',
        'rtf',
        'doc',
        'docx',
        'ppt',
        'pptx',
        'txt',
        'htm',
        'html',
        'djvu',
        'djv'
    ];

    const MAX_FILE_SIZE = 128 * 1024 * 1024;

    const UPLOAD_DIR = 'files';

    const QR_DIR = 'qr';

    const PREVIEW_DIR = 'thumbs';

    const SEO_DESCRIPTION_MAX_LENGTH = 300;

    /**
     * Get File ID
     *
     * @return int File ID
     */
    public function getId(): int
    {
        return (int) $this->get('id');
    }

    /**
     * Get File Title
     *
     * @return File Title
     */
    public function getTitle(): string
    {
        return (string) $this->get('title');
    }

    /**
     * Get File Short Title
     *
     * @return File Short Title
     */
    public function getShortTitle(): string
    {
        return (string) $this->get('short_title');
    }

    /**
     * Get File Slug
     *
     * @return string Slug
     */
    public function getSlug(): string
    {
        return (string) $this->get('slug');
    }

    /**
     * Get File Description
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
     * Get File Description In Plain Text Format
     *
     * @return string Description In Plain Text Format
     */
    public function getDescriptionPlainText(): ?string
    {
        $description = $this->get('description');

        if ($this->has('description_plain_text')) {
            $description = $this->get('description_plain_text');
        }

        if (empty($description)) {
            return null;
        }

        return (string) $description;
    }

    /**
     * Get File Description For SEO
     *
     * @return string SEO Description In Plain Text Format
     */
    public function getSeoDescription(): ?string
    {
        $description = $this->getDescriptionPlainText();

        if (empty($description)) {
            return null;
        }

        if (strlen($description) <= static::SEO_DESCRIPTION_MAX_LENGTH - 1) {
            return $description;
        }

        $description = substr(
            $description,
            0,
            static::SEO_DESCRIPTION_MAX_LENGTH - 1
        );

        $description = preg_replace('/\s+/su', ' ', $description);

        $description = preg_replace(
            '/^(.*?)\s([^\s]+)$/su',
            '$1',
            $description
        );

        return sprintf('%s…', $description);
    }

    /**
     * Get File Category ID
     *
     * @return int|null Category ID
     */
    public function getCategoryId(): ?int
    {
        $categoryId = $this->get('id_category');

        if (empty($categoryId)) {
            return null;
        }

        return (int) $categoryId;
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
     * Get File Category
     *
     * @return CategoryValuesObject|null File Category
     */
    public function getCategory(): ?CategoryValuesObject
    {
        if (!$this->has('category')) {
            return null;
        }

        $category = $this->get('category');

        if (empty($category)) {
            return null;
        }

        return $category;
    }

    /**
     * Get File Link
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
     * Get File Path
     *
     * @return string File Path
     */
    public function getFilePath(): string
    {
        $filePath = $this->get('file_path');
        $filePath = empty($filePath) ? '#' : $filePath;

        return $filePath;
    }

    /**
     * Get Views
     *
     * @return int File Views
     */
    public function getViews(): int
    {
        return (int) $this->get('views');
    }

    /**
     * Get QR File Path
     *
     * @return string|null QR File Path
     */
    public function getQrPath(): ?string
    {
        return $this->get('qr_path');
    }

    /**
     * Get Preview File Path
     *
     * @return string|null Preview File Path
     */
    public function getPreviewPath(): ?string
    {
        if (!$this->has('preview_path')) {
            return static::DEFAULT_PREVIEW;
        }

        return $this->get('preview_path');
    }

    /**
     * Set File Title
     *
     * @param string|null $title Category Title
     */
    public function setTitle(?string $title = null): void
    {
        $this->set('title', $title);
    }

    /**
     * Set File Short Title
     *
     * @param string|null $shortTitle File Short Title
     */
    public function setShortTitle(?string $shortTitle = null): void
    {
        $this->set('short_title', $shortTitle);
    }

    /**
     * Set File Slug
     *
     * @param string|null $slug File Slug
     */
    public function setSlug(?string $slug = null): void
    {
        $this->set('slug', $slug);
    }

    /**
     * Set File Description
     *
     * @param string|null $description File Description
     */
    public function setDescription(?string $description = null): void
    {
        $this->set('description', $description);
    }

    /**
     * Set File Description In Plain Text Format
     *
     * @param string  $description Description In Plain Text Format
     */
    public function setDescriptionPlainText(?string $description = null): void
    {
        $this->set('description_plain_text', $description);
    }

    /**
     * Set File Category ID
     *
     * @param int|null $categoryId Category ID
     */
    public function setCategoryId(?string $categoryId = null): void
    {
        $this->set('id_category', $categoryId);
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
     * Set File Category
     *
     * @param CategoryValuesObject|null $category Category
     */
    public function setCategory(?CategoryValuesObject $category = null): void
    {
        $this->set('category', $category);
    }

    /**
     * Set File Views
     */
    public function setViews(): void
    {
        $views = $this->getViews();
        $views++;

        $this->set('views', $views);
    }

    /**
     * Set QR File Path
     *
     * @param string|null $qrPath QR File Path
     */
    public function setQrPath(?string $qrPath = null): ?string
    {
        return $this->set('qr_path', $qrPath);
    }
}
