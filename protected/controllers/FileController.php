<?php
/**
 * File Controller Class
 */
class FileController extends ControllerCore
{
    const SEO_DESCRIPTION_MAX_LENGTH = 300;

    /**
     * Display Single File
     */
    public function actionSingle(): void
    {
        $slug = $this->getValueFromUrl('slug');

        $fileModel     = $this->getModel('file');
        $categoryModel = $this->getModel('category');

        if (empty($slug)) {
            $this->redirect('/error/404/', true);
        }

        $file = $fileModel->getVOBySlug($slug);

        if (empty($file)) {
            $this->redirect('/error/404/');
        }

        $fileModel->view($file);

        $pagePath = $categoryModel->getHierarchy($file->getCategoryId());

        $pagePath[$file->getLink()] = $file->getShortTitle();

        $meta = [
            'image' => $file->getPreviewPath()
        ];

        if (!empty($file->getSeoDescription())) {
            $meta['description'] = $file->getSeoDescription();
        }

        $this->render(
            'file/single',
            [
                'file'     => $file,
                'seoTitle' => $file->getTitle(),
                'pagePath' => $pagePath,
                'meta'     => $meta
            ]
        );
    }

    /**
     * Display Single File In AJAX Mode
     */
    public function actionAjax(): void
    {
        $id = (int) $this->getValueFromUrl('id');

        if (empty($id)) {
            $this->redirect('/error/404/', true);
        }

        $file = $this->getModel('file')->getVOById($id);

        if (empty($file)) {
            $this->redirect('/error/404/');
        }

        $this->render(
            'file/ajax',
            [
                'file' => $file,
                'ajax' => true
            ]
        );
    }

    /**
     * Remove File
     */
    public function actionRemove(): void
    {
        if (
            !array_key_exists('isSignedIn', $this->commonData) ||
            !(bool) $this->commonData['isSignedIn']
        ) {
            $this->redirect('/login/');
        }

        $id = (int) $this->getValueFromUrl('id');

        if (empty($id)) {
            $this->redirect('/error/404/', true);
        }

        $fileModel = $this->getModel('file');

        $file = $fileModel->getVOById($id);

        if (empty($file)) {
            $this->redirect('/error/404/');
        }

        $idCategory = (int) $file->getCategoryId();

        if (empty($idCategory)) {
            $this->redirect('/error/404/');
        }

        $category = $this->getModel('category')->getVOById($idCategory);

        if (empty($category)) {
            $this->redirect('/error/404/');
        }

        $fileModel->removeById($id);

        $this->redirect($category->getLink());
    }

    /**
     * Add File
     */
    public function actionAdd(): void
    {
        if (
            !array_key_exists('isSignedIn', $this->commonData) ||
            !(bool) $this->commonData['isSignedIn']
        ) {
            $this->redirect('/login/');
        }

        $formErrors = null;

        $fileModel     = $this->getModel('file');
        $categoryModel = $this->getModel('category');

        $formData = [
            'errors'      => null,
            'title'       => null,
            'short_title' => null,
            'slug'        => null,
            'id_category' => null,
            'description' => null
        ];

        $pagePath = [
            '/file/add/' => __t('Add New File')
        ];

//        var_dump($this->post); die();

        if (empty($this->post)) {
            $this->render(
                'file/form',
                [
                    'formAction' => FileValuesObject::ADD_LINK,
                    'formData'   => $formData,
                    'categories' => $categoryModel->getAll(),
                    'pagePath'   => $pagePath
                ]
            );
        }

        $formObject = $fileModel->add($this->post);

        if ($formObject->isStatusSuccess()) {
            $url = sprintf('/file/%s/', $formObject->getSlug());

            $this->redirect($url);
        }

        $formData           = array_merge($formData, $formObject->exportRow());
        $formData['errors'] = $formObject->getErrors();

        $this->render(
            'file/form',
            [
                'formAction' => FileValuesObject::ADD_LINK,
                'formData'   => $formData,
                'categories' => $categoryModel->getAll(),
                'pagePath'   => $pagePath
            ]
        );
    }

    /**
     * Add File
     */
    public function actionEdit(): void
    {
        if (
            !array_key_exists('isSignedIn', $this->commonData) ||
            !(bool) $this->commonData['isSignedIn']
        ) {
            $this->redirect('/login/');
        }

        $id = (int) $this->getValueFromUrl('id');

        if (empty($id)) {
            $this->redirect('/error/404/', true);
        }

        $fileModel     = $this->getModel('file');
        $categoryModel = $this->getModel('category');

        $file = $fileModel->getVOById($id);

        if (empty($file)) {
            $this->redirect('/error/404/');
        }

        $formErrors = null;

        $pagePath = $categoryModel->getHierarchy($file->getCategoryId());

        $pagePath[$file->getLink()]     = $file->getShortTitle();
        $pagePath[$file->getEditLink()] = __t('Edit File');

        $formData = [
            'errors'      => null,
            'title'       => $file->getTitle(),
            'short_title' => $file->getShortTitle(),
            'slug'        => $file->getSlug(),
            'id_category' => $file->getCategoryId(),
            'description' => $file->getDescriptionPlainText()
        ];

        if (empty($this->post)) {
            $this->render(
                'file/form',
                [
                    'formAction' => $file->getEditLink(),
                    'formData'   => $formData,
                    'filePath'   => $file->getFilePath(),
                    'categories' => $categoryModel->getAll(),
                    'pagePath'   => $pagePath
                ]
            );
        }

        $formObject = $fileModel->edit($id, $this->post);

        if ($formObject->isStatusSuccess()) {
            $url = sprintf('/file/%s/', $formObject->getSlug());

            $this->redirect($url);
        }

        $formData           = $formObject->exportRow();
        $formData['errors'] = $formObject->getErrors();

        $this->render(
            'file/form',
            [
                'formAction' => $file->getEditLink(),
                'formData'   => $formData,
                'filePath'   => $file->getFilePath(),
                'categories' => $categoryModel->getAll(),
                'pagePath'   => $pagePath
            ]
        );
    }
}
