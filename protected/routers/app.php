<?php
/**
 * Class For Routing HTTP Requests
 */
class Router
{
    /**
     * Perform Redirect Rules
     *
     * @param string|null $url HTTP Request URL
     */
    public function routeRedirect(?string $url = null): void
    {
        if (empty($url)) {
            $url = '/';
        }

        if (!preg_match('/^(.*?)\/$/su', $url)) {
            header(sprintf('Location: %s/', $url), true, 301);
            exit(0);
        }

        if (preg_match('/^\/ua\/(.*?)$/su', $url)) {
            $url = preg_replace('/^\/ua\/(.*?)\/$/su', '/$1/', $url);

            header(sprintf('Location: %s', $url), true, 301);
            exit(0);
        }

        if (preg_match('/^\/(.*?)\/page\-1\/$/su', $url)) {
            $url = preg_replace('/^\/(.*?)\/page\-1\/$/su', '/$1/', $url);

            header(sprintf('Location: %s', $url), true, 301);
            exit(0);
        }

        if (preg_match('/^\/main\/(.*?)\/$/su', $url)) {
            header('Location: /error/404/', true, 301);
            exit(0);
        }

        if (preg_match('/^\/catalog\/(.*?)\/$/su', $url)) {
            header('Location: /error/404/', true, 301);
            exit(0);
        }

        if (preg_match('/^\/user\/(.*?)\/$/su', $url)) {
            header('Location: /error/404/', true, 301);
            exit(0);
        }

        if ($url == '/user/') {
            header('Location: /error/404/', true, 301);
            exit(0);
        }

        if ($url == '/cat/') {
            header('Location: /', true, 301);
            exit(0);
        }

        if ($url == '/file/') {
            header('Location: /', true, 301);
            exit(0);
        }
    }

    /**
     * Perform Rewrite Rules
     *
     * @param string|null $url HTTP Request URL
     *
     * @return string Rewrited HTTP Request URL
     */
    public function routeRewrite(?string $url = null): string
    {
        if (empty($url)) {
            $url = '/';
        }

        if ($url == '/') {
            return '/ua/catalog/categories/';
        }

        if ($url == '/login/') {
            return '/ua/user/login/';
        }

        if ($url == '/logout/') {
            return '/ua/user/logout/';
        }

        if ($url == '/profile/') {
            return '/ua/user/profile/';
        }

        if ($url == '/cat/add/') {
            return '/ua/catalog/add/';
        }

        if ($url == '/file/add/') {
            return '/ua/file/add/';
        }

        if (preg_match('/^\/cat\/([a-z0-9\-]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/cat\/([a-z0-9\-]+)\/$/su',
                '/ua/catalog/category/?slug=$1',
                $url
            );
        }

        if (preg_match('/^\/cat\/ajax\/([0-9]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/cat\/ajax\/([0-9]+)\/$/su',
                '/ua/catalog/ajax/?id=$1',
                $url
            );
        }

        if (preg_match('/^\/cat\/edit\/([0-9]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/cat\/edit\/([0-9]+)\/$/su',
                '/ua/catalog/edit/?id=$1',
                $url
            );
        }

        if (preg_match('/^\/cat\/remove\/([0-9]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/cat\/remove\/([0-9]+)\/$/su',
                '/ua/catalog/remove/?id=$1',
                $url
            );
        }

        if (preg_match('/^\/page\/([a-z0-9\-]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/page\/([a-z0-9\-]+)\/$/su',
                '/ua/main/page/?slug=$1',
                $url
            );
        }

        if (preg_match('/^\/error\/([0-9]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/error\/([0-9]+)\/$/su',
                '/ua/main/error/?code=$1',
                $url
            );
        }

        if (preg_match('/^\/file\/([a-z0-9\-]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/file\/([a-z0-9\-]+)\/$/su',
                '/ua/file/single/?slug=$1',
                $url
            );
        }

        if (preg_match('/^\/file\/ajax\/([0-9]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/file\/ajax\/([0-9]+)\/$/su',
                '/ua/file/ajax/?id=$1',
                $url
            );
        }

        if (preg_match('/^\/file\/edit\/([0-9]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/file\/edit\/([0-9]+)\/$/su',
                '/ua/file/edit/?id=$1',
                $url
            );
        }

        if (preg_match('/^\/file\/remove\/([0-9]+)\/$/su', $url)) {
            return preg_replace(
                '/^\/file\/remove\/([0-9]+)\/$/su',
                '/ua/file/remove/?id=$1',
                $url
            );
        }

        return '/ua/main/error/?code=404';
    }
}
