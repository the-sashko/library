<?php
/**
 * Cron Controller Class
 */
class CronController extends CronControllerCore
{
    const UPLOADS_PATH = __DIR__.'/../res/uploads/files';

    const SHARE_MESSAGE = 'Нова література в хаті-читальні:';

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
        $fileModel = $this->getModel('file');
        $files     = (array) $fileModel->getByShareType('telegram');

        $message = mb_convert_case(static::SHARE_MESSAGE, MB_CASE_UPPER);

        $files = array_slice($files, 0, 5);

        foreach ($files as $file) {
            $fileModel->saveSharedId($file->getId(), 'telegram');

            $message = sprintf(
                '%s'."\n".'[%s](%s%s)',
                $message,
                $file->getShortTitle(),
                $this->currentHost,
                $file->getLink()
            );
        }

        if (!empty($files)) {
            $shareConfig = $this->getConfig('share');

            $telegramPlugin = $this->getPlugin('telegram');

            $telegramPlugin->setCredentials($shareConfig['telegram']);
            $telegramPlugin->send($message);
        }
    }

    /**
     * Generate Sitemaps
     */
    public function jobSitemap(): void
    {
        $sitemapPlugin = $this->getPlugin('sitemap');
        $categories    = $this->getModel('category')->getAll();

        $mainLinks = [];

        $host = $this->currentHost;

        $getFullLink = function ($vObject) use ($host) {
            return sprintf('%s%s', $host, $vObject->getLink());
        };

        $links = [
            sprintf('%s/', $host),
            sprintf('%s/page/about/', $host),
            sprintf('%s/page/contact/', $host)
        ];

        $sitemapPlugin->saveLinksToSitemap('main', $links);

        $mainLinks[] = 'main';

        foreach ($categories as $category) {
            $links = [];
            $files = (array) $category->getFiles();

            if (empty($files) && empty($category->getChildren())) {
                continue;
            }

            $links   = array_map($getFullLink, $files);
            $links[] = $getFullLink($category);

            $categorySlug = $category->getSlug();

            $sitemapPlugin->saveLinksToSitemap($categorySlug, $links);

            $mainLinks[] = $categorySlug;
        }

        $sitemapPlugin->saveSummarySitemap('sitemap', $mainLinks, $host);
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
