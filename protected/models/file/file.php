<?php
/**
 * ModelCore Class For File Model
 */
class File extends ModelCore
{
    const EMPTY_SLUG_VALUE = 'empty';

    const FORBIDDEN_SLUGS = [
        'add',
        'all',
        'edit',
        'remove'
    ];

    /**
     * Create File
     *
     * @param array $inputData Input Data
     *
     * @return FileFormObject File Form Object
     */
    public function add(array $inputData): FileFormObject
    {
        $formObject = new FileFormObject($inputData);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $slug        = $this->_getSlugFromFormObject($formObject);
        $title       = $formObject->getTitle();
        $shortTitle  = $formObject->getShortTitle();
        $description = $formObject->getDescription();
        $categoryId  = $formObject->getCategoryId();

        $markupPlugin = $this->getPlugin('markup');

        if (!empty($title)) {
            $title = $markupPlugin->normalizeText($title);
        }

        if (!empty($shortTitle)) {
            $shortTitle = $markupPlugin->normalizeText($shortTitle);
        }

        if (!empty($description)) {
            $description = $markupPlugin->normalizeText($description);
        }

        $title      = preg_replace('/\s+/su', ' ', $title);
        $shortTitle = preg_replace('/\s+/su', ' ', $shortTitle);

        $formObject->setTitle($title);
        $formObject->setShortTitle($shortTitle);
        $formObject->setSlug($slug);
        $formObject->setDescription($description);

        if (!empty($this->getVOByTitle($title))) {
            $formObject->setError(FileFormObject::ERROR_TITLE_ALREADY_EXISTS);
        }

        if (!empty($this->getVOByShortTitle($shortTitle))) {
            $formObject->setError(
                FileFormObject::ERROR_SHORT_TITLE_ALREADY_EXISTS
            );
        }

        if (empty($this->getModel('category')->getVOById($categoryId))) {
            $formObject->setError(FileFormObject::ERROR_INVALID_CATEGORY);
        }

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $row = [
            'title'       => $title,
            'short_title' => $shortTitle,
            'slug'        => $slug,
            'id_category' => $categoryId
        ];

        if (!empty($description)) {
            $row['description'] = $description;
        }

        $filePath = $this->_uploadFile($formObject);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        if (empty($filePath)) {
            $formObject->setError(
                FileFormObject::ERROR_CAN_NOT_UPLOAD_FILE
            );

            return $formObject;
        }

        $formObject->setFilePath($filePath);

        $row['file_path']    = $filePath;
        $row['qr_path']      = $this->_createQrFile($formObject);
        $row['preview_path'] = $this->_getPreviewFile($formObject);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        if (empty($row['qr_path'])) {
            $formObject->setError(
                FileFormObject::ERROR_CAN_NOT_CREATE_QR_FILE
            );

            return $formObject;
        }

        if ($this->store->insertFile($row)) {
            $formObject->setSuccess();
        }

        return $formObject;
    }

    /**
     * Edit File
     *
     * @param int|null $id        File ID
     * @param array    $inputData Input Data
     *
     * @return FileFormObject File Form Object
     */
    public function edit(?int $id = null, array $inputData): FileFormObject
    {
        $formObject = new FileFormObject($inputData);

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        if (empty($id)) {
            return $formObject;
        }

        $slug        = $this->_getSlugFromFormObject($formObject, $id);
        $title       = $formObject->getTitle();
        $shortTitle  = $formObject->getShortTitle();
        $description = $formObject->getDescription();
        $categoryId  = $formObject->getCategoryId();

        $markupPlugin = $this->getPlugin('markup');

        if (!empty($title)) {
            $title = $markupPlugin->normalizeText($title);
        }

        if (!empty($shortTitle)) {
            $shortTitle = $markupPlugin->normalizeText($shortTitle);
        }

        if (!empty($description)) {
            $description = $markupPlugin->normalizeText($description);
        }

        $title      = preg_replace('/\s+/su', ' ', $title);
        $shortTitle = preg_replace('/\s+/su', ' ', $shortTitle);

        $formObject->setTitle($title);
        $formObject->setShortTitle($shortTitle);
        $formObject->setSlug($slug);
        $formObject->setDescription($description);

        $file = $this->getVOByTitle($title);

        if (!empty($file) && $file->getId() != $id) {
            $formObject->setError(FileFormObject::ERROR_TITLE_ALREADY_EXISTS);
        }

        $file = $this->getVOByShortTitle($title);

        if (!empty($file) && $file->getId() != $id) {
            $formObject->setError(
                FileFormObject::ERROR_SHORT_TITLE_ALREADY_EXISTS
            );
        }

        if (empty($this->getModel('category')->getVOById($categoryId))) {
            $formObject->setError(FileFormObject::ERROR_INVALID_CATEGORY);
        }

        if ($formObject->hasErrors()) {
            return $formObject;
        }

        $row = [
            'title'       => $title,
            'short_title' => $shortTitle,
            'slug'        => $slug,
            'id_category' => $categoryId,
            'description' => $description,
            'mdate'       => date('Y-m-d H:i:s')
        ];

        if ($this->store->updateFileById($id, $row)) {
            $formObject->setSuccess();
        }

        return $formObject;
    }

    /**
     * Get File Value Object Instance
     *
     * @param array|null $row List Of Values
     *
     * @return ValuesObject File Values Object Instance
     */
    public function getVO(?array $row = null): ValuesObject
    {
        $fileVO = parent::getVO($row);

        $description = $fileVO->getDescription();

        if (empty($description)) {
            return $fileVO;
        }

        $fileVO->setDescriptionPlainText($description);

        $markupPlugin = $this->getPlugin('markup');
        $description  = $markupPlugin->normalizeSyntax($description);
        $description  = $markupPlugin->markup2html($description);

        $fileVO->setDescription($description);

        return $fileVO;
    }

    /**
     * Get Files By Category ID
     *
     * @param int|null $categoryId Category ID
     *
     * @return array|null List Of Files
     */
    public function getByCategoryId(?int $categoryId = null): ?array
    {
        if (empty($categoryId)) {
            return null;
        }

        $rows = $this->store->getRowsByCategoryId($categoryId);

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * Get Files By Share Type
     *
     * @param string|null $type Share Type
     *
     * @return array|null List Of Files
     */
    public function getByShareType(?string $type = null): ?array
    {
        if (empty($type)) {
            return null;
        }

        $rows = $this->store->getRowsByShareType($type);

        if (empty($rows)) {
            return null;
        }

        return $this->getVOArray($rows);
    }

    /**
     * Save Shared File ID
     *
     * @param int|null    $id   File ID
     * @param string|null $type Share Type
     *
     * @return bool Is Shared File ID Successfully Saved
     */
    public function saveSharedId(?int $id = null, ?string $type = null): bool
    {
        if (empty($id)) {
            return null;
        }

        if (empty($type)) {
            return null;
        }

        return $this->store->insertSharedFileId($id, $type);
    }

    /**
     * Update File Views
     *
     * @param FileValuesObject|null $fileVO File Values Object
     *
     * @return bool Is File Views Successfully Updated
     */
    public function view(?FileValuesObject $file = null): bool
    {
        if (empty($file)) {
            return false;
        }

        $file->setViews();

        $row = [
            'views' => $file->getViews()
        ];

        return $this->store->updateFileById($file->getId(), $row);
    }

    /**
     * Get File By ID
     *
     * @param int|null $id ID
     *
     * @return FileValuesObject|null File
     */
    public function getVOById(?int $id = null): ?FileValuesObject
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
     * Get File By Slug
     *
     * @param string|null $slug Slug
     *
     * @return FileValuesObject|null File
     */
    public function getVOBySlug(?string $slug = null): ?FileValuesObject
    {
        if (empty($slug)) {
            return null;
        }

        $row = $this->store->getRowBySlug($slug);

        if (empty($row)) {
            return null;
        }

        $fileVO = $this->getVO($row);

        $categoryId = $fileVO->getCategoryId();
        $category   = $this->getModel('category')->getVOById($categoryId);

        if (!empty($category)) {
            $fileVO->setCategory($category);
        }
 
        return $fileVO;
    }

    /**
     * Get File By Title
     *
     * @param string|null $title Title
     *
     * @return FileValuesObject|null File
     */
    public function getVOByTitle(?string $title = null): ?FileValuesObject
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
     * Get File By Short Title
     *
     * @param string|null $shortTitle Short Title
     *
     * @return FileValuesObject|null File
     */
    public function getVOByShortTitle(
        ?string $shortTitle = null
    ): ?FileValuesObject
    {
        if (empty($shortTitle)) {
            return null;
        }

        $row = $this->store->getRowByShortTitle($shortTitle);

        if (empty($row)) {
            return null;
        }

        return $this->getVO($row);
    }

    /**
     * Soft Remove File By ID
     *
     * @param int|null $id File ID
     *
     * @return bool Is File Successfully Soft Removed
     */
    public function removeById(?int $id = null): bool
    {
        if (empty($id)) {
            return false;
        }

        $row = $this->store->getRowById($id);

        if (empty($row)) {
            return false;
        }

        $row = [
            'ddate'     => date('Y-m-d H:i:s'),
            'is_active' => 'f'
        ];

        return $this->store->updateFileById($id, $row);
    }

    /**
     * Soft Remove File By Category ID
     *
     * @param int|null $categoryId Category ID
     *
     * @return bool Is File Successfully Soft Removed
     */
    public function removeByCategoryId(?int $categoryId = null): bool
    {
        if (empty($categoryId)) {
            return false;
        }

        $rows = $this->store->getRowsByCategoryId($categoryId);

        if (empty($rows)) {
            return true;
        }

        $row = [
            'ddate'     => date('Y-m-d H:i:s'),
            'is_active' => 'f'
        ];

        return $this->store->updateFilesByCategoryId($categoryId, $row);
    }

    /**
     * Get File By Slug By Form Object
     *
     * @param FileFormObject &$formObject Form Object
     * @param int|null       $id          File ID
     *
     * @return string Slug
     */
    private function _getSlugFromFormObject(
        FileFormObject &$formObject,
        ?int           $id          = null
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

        if (strlen($uniqSlug) > FileFormObject::SLUG_MAX_LENGTH) {
            $formObject->setError(FileFormObject::ERROR_SLUG_ALREADY_EXISTS);
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

    /**
     * Upload File
     *
     * @param FileFormObject &$formObject File Form Object
     *
     * @return string|null Path To Uploaded File On Server 
     */
    private function _uploadFile(FileFormObject &$formObject): ?string
    {
        if (empty($_FILES)) {
            $formObject->setError(FileFormObject::ERROR_EMPTY_FILE);

            return null;
        }

        $uploadPlugin = $this->getPlugin('upload');

        $uploadPlugin->upload(
            FileValuesObject::EXTENSIONS,
            FileValuesObject::MAX_FILE_SIZE,
            FileValuesObject::UPLOAD_DIR
        );

        $uploadError = $uploadPlugin->getError();

        if (!empty($uploadError)) {
            $formObject->setError($uploadError);

            return null;
        }

        $uploadedFiles = $uploadPlugin->getFiles();

        if (
            empty($uploadedFiles) ||
            !array_key_exists(FileFormObject::UPLOAD_FILE_NAME, $uploadedFiles)
        ) {
            return null;
        }

        $uploadedFiles = $uploadedFiles[FileFormObject::UPLOAD_FILE_NAME];

        if (empty($uploadedFiles)) {
            return null;
        }

        $uploadedFilePath = array_shift($uploadedFiles);

        if (empty($uploadedFilePath)) {
            return null;
        }

        return $this->_moveUploadedFile($uploadedFilePath, $formObject);
    }

    /**
     * Move Upload File
     *
     * @param string         $uploadedFilePath Uploaded File Path
     * @param FileFormObject $formObject       File Form Object
     *
     * @return string|null Path To File For Public Access 
     */
    private function _moveUploadedFile(
        string         $uploadedFilePath,
        FileFormObject $formObject
    ): ?string
    {
        if (empty($uploadedFilePath)) {
            return null;
        }

        $fileExtension = preg_replace(
            '/^.*?\.([a-z]+)$/su',
            '$1',
            $uploadedFilePath
        );

        $fileExtension = mb_convert_case($fileExtension, MB_CASE_LOWER);

        if (!in_array($fileExtension, FileValuesObject::EXTENSIONS)) {
            return null;
        }

        $slug = $formObject->getSlug();

        if (empty($slug)) {
            return null;
        }

        $publicDir = date('Y/m/d/H/i/s');

        $publicDirPath = sprintf(
            '%s/%s/%s',
            __DIR__.'/../../../public',
            FileValuesObject::UPLOAD_DIR,
            $publicDir
        );

        if (!file_exists($publicDirPath) || !is_dir($publicDirPath)) {
            mkdir($publicDirPath, 0775, true);
        }

        $publicFile = sprintf(
            '/%s/%s/%s.%s',
            FileValuesObject::UPLOAD_DIR,
            $publicDir,
            $slug,
            $fileExtension
        );

        $publicFilePath = sprintf(
            '%s%s',
            __DIR__.'/../../../public',
            $publicFile
        );

        copy($uploadedFilePath, $publicFilePath);

        if (!file_exists($publicFilePath) || !is_file($publicFilePath)) {
            return null;
        }

        chmod($publicFilePath, 0775);

        return $publicFile;
    }

    /**
     * Create QR File
     *
     * @param FileFormObject &$formObject File Form Object
     *
     * @return string|null Path To QR File On Server 
     */
    private function _createQrFile(FileFormObject &$formObject): ?string
    {
        $url = sprintf('%s%s', $this->currentHost, $formObject->getFilePath());

        $fileName = sprintf('%s.png', $formObject->getSlug());
        $dirPath  = date('Y/m/d/H/i/s');
        $dirPath  = sprintf('%s/%s', FileValuesObject::QR_DIR, $dirPath);

        $qrFilePath = $this->getPlugin('qr')->create(
            $url,
            $dirPath,
            $fileName
        );

        if (!file_exists($qrFilePath) || !is_file($qrFilePath)) {
            return null;
        }

        $fileName = preg_replace(
            '/^.*?\/([^\/]+)\.png$/su',
            '$1.png',
            $qrFilePath
        );

        return sprintf('/%s/%s', $dirPath, $fileName);
    }

    /**
     * Get Preview File
     *
     * @param FileFormObject &$formObject File Form Object
     *
     * @return string Path To Preview File 
     */
    private function _getPreviewFile(FileFormObject &$formObject): string
    {
        $previewFile = FileValuesObject::DEFAULT_PREVIEW;
        $filePath    = $formObject->getFilePath();
        $fileSlug    = $formObject->getSlug();

        if (empty($filePath) || empty($fileSlug)) {
            return $previewFile;
        }

        $fileExtension = preg_replace(
            '/^(.*?)\.([0-9A-z]+)$/su',
            '$2',
            $filePath
        );

        $fileExtension = mb_convert_case($fileExtension, MB_CASE_LOWER);

        $fileExtensionPreviewPath = sprintf(
            '%s/%s.png',
            FileValuesObject::FILE_TYPES_DIR_PATH,
            $fileExtension
        );

        if (
            file_exists($fileExtensionPreviewPath) &&
            is_file($fileExtensionPreviewPath)
        ) {
            $previewFile = sprintf(
                '%s/%s.png',
                FileValuesObject::FILE_TYPES_DIR,
                $fileExtension
            );
        }

        if ($fileExtension == 'pdf') {
            $pdfFilePath = sprintf('%s/../../../public%s', __DIR__, $filePath);

            $previewFile = $this->_createPreviewFileFromPdf(
                $pdfFilePath,
                $fileSlug
            );
        }

        return $previewFile;
    }

    /**
     * Create Preview File From PDF File
     *
     * @param string|null $pdfFilePath Path To PDF File On Server
     * @param string|null $fileSlug Slug Of File
     *
     * @return string|null Path To Preview File 
     */
    private function _createPreviewFileFromPdf(
        ?string $pdfFilePath = null,
        ?string $fileSlug    = null
    ): string
    {
        $previewFile = FileValuesObject::DEFAULT_PDF_PREVIEW;

        if (empty($pdfFilePath) || empty($fileSlug)) {
            return $previewFile;
        }

        try {
            $imagickObject = new imagick(sprintf('%s[0]', $pdfFilePath));

            $width  = $imagickObject->getImageWidth();
            $length = $imagickObject->getImageLength();

            $length = $length * (
                FileValuesObject::PREVIEW_FILE_WIDTH / $width
            );

            $length = (int) round($length);

            $imagickObject->setImageFormat('png');
            $imagickObject->adaptiveResizeImage(
                FileValuesObject::PREVIEW_FILE_WIDTH,
                $length,
                true
            );

            $fileName   = sprintf('%s.png', $fileSlug);

            $previewDir = FileValuesObject::PREVIEW_DIR;
            $previewDir = sprintf('/%s/%s', $previewDir, date('Y/m/d/H/i/s'));

            $previewDirPath = sprintf(
                '%s/../../../public%s',
                __DIR__,
                $previewDir
            );

            if (!file_exists($previewDirPath) || !is_dir($previewDirPath)) {
                mkdir($previewDirPath, 0775, true);
            }

            $previewFilePath = sprintf('%s/%s', $previewDirPath, $fileName);

            if (file_exists($previewFilePath) && is_file($previewFilePath)) {
                return $previewFile;
            }

            $imagickObject->writeImage($previewFilePath);

            if (!file_exists($previewFilePath) || !is_file($previewFilePath)) {
                return $previewFile;
            }

            chmod($previewFilePath, 0775);

            return sprintf('%s/%s', $previewDir, $fileName);
        } catch (Exception $exp) {
            $this->getPlugin('logger')->logError($exp->getMessage(), 'file');

            return FileValuesObject::DEFAULT_PDF_PREVIEW;
        }
    }
}
