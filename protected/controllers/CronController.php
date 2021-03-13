<?php
/**
 * Cron Controller Class
 */
class CronController extends CronControllerCore
{
    const UPLOADS_PATH = __DIR__.'/../res/uploads/files';

    /**
     * Clean Old Files In Uploads
     */
    public function jobUploads(): void
    {
        $date = new \DateTime();
        $date->modify('-1 day');

        $uploadsPath = sprintf(
            '%s/%s',
            static::UPLOADS_PATH,
            $date->format('Y/m/d')
        );

        $this->_removeDirectory($uploadsPath);
    }

    /**
     * Share New Files
     */
    public function jobShare(): void
    {
        // To-Do
    }

    /**
     * Genegeate Translations
     */
    public function jobTranslations(): void
    {
        $this->getPlugin('language')->generateDictionaries();
    }

    /**
     * Recursive Removing Directory
     *
     * @param string|null $path Path To Directory Or File
     *
     * @return bool Is File Or Directory Successfully Removed
     */
    private function _removeDirectory(?string $path = null): bool
    {
        if (empty($path) || !file_exists($path)) {
            return false;
        }

        if (!is_dir($path)) {
            unlink($path);

            return true;
        }

        foreach (scandir($path) as $fileItem) { 
            if ($fileItem == '.' || $fileItem == '..') {
                continue;
            }

            $fileItem = sprintf('%s/%s', $path, $fileItem);

            $this->_removeDirectory($fileItem);
        }

        rmdir($path);

        return true;
    }
}
