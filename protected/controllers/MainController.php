<?php
/**
 * Main Controller Class
 */
class MainController extends ControllerCore
{
    /**
     * Display Static Page
     */
    public function actionPage(): void
    {
        $this->displayStaticPage();
    }

    /**
     * Display Error Page
     */
    public function actionError(): void
    {
        $this->displayErrorPage();
    }
}
