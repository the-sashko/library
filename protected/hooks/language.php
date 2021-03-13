<?php
class LanguageHook extends HookCore
{
    public function translatePagePath(): void
    {
        if ($this->hasEntityParam('pagePath')) {
            $pagePath = $this->getEntityParam('pagePath');

            if (is_array($pagePath)) {
            	foreach ($pagePath as $pageUrl => $pageTitle) {
            		$pagePath[$pageUrl] = __t($pageTitle);
            	}
            }

            $this->setEntityParam('pagePath', $pagePath);
        }
    }
}
