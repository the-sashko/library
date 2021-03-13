<?php
/**
 * Catalog Controller Class
 */
class CatalogController extends ControllerCore
{
    /**
     * List Of Categories
     */
    public function actionCategories(): void
    {
        $categoryModel = $this->getModel('category');

        $this->render(
            'category/list',
            [
                'categories' => $categoryModel->getTop(),
                'isCatalog'  => true,
                'isMainPage' => true
            ]
        );
    }

    /**
     * List Of Categories
     */
    public function actionCategory(): void
    {
        $slug = $this->getValueFromUrl('slug');

        if (empty($slug)) {
            $this->redirect('/error/404/', true);
        }

        $categoryModel = $this->getModel('category');
        $category      = $categoryModel->getVOBySlug($slug);

        if (empty($category)) {
            $this->redirect('/error/404/');
        }

        $pagePath = $categoryModel->getHierarchy($category->getId());

        $this->render(
            'category/list',
            [
                'currentCategory' => $category,
                'categories'      => $category->getChildren(),
                'files'           => $category->getFiles(),
                'pagePath'        => $pagePath,
                'isCatalog'       => true,
                'seoTitle'        => $category->getTitle(),
                'meta'            => [
                    'description' => $category->getSeoDescription()
                ]
            ]
        );
    }

    /**
     * Ajax View Of Category
     */
    public function actionAjax(): void
    {
        $id = (int) $this->getValueFromUrl('id');

        if (empty($id)) {
            $this->redirect('/error/404/', true);
        }

        $category = $this->getModel('category')->getVOById($id);

        if (empty($category)) {
            $this->redirect('/error/404/');
        }

        $this->render(
            'category/ajax',
            [
                'category' => $category,
                'ajax'     => true
            ]
        );
    }

    /**
     * Remove Category
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

        $categoryModel = $this->getModel('category');

        $category = $categoryModel->getVOById($id);

        if (empty($category)) {
            $this->redirect('/error/404/');
        }

        $categoryModel->removeById($id);

        $this->redirect($category->getParentLink());
    }

    /**
     * Add Category
     */
    public function actionAdd(): void
    {
        if (
            !array_key_exists('isSignedIn', $this->commonData) ||
            !(bool) $this->commonData['isSignedIn']
        ) {
            $this->redirect('/');
        }

        $formErrors    = null;
        $categoryModel = $this->getModel('category');

        $formData = [
            'errors'      => null,
            'title'       => null,
            'slug'        => null,
            'id_parent'   => null
        ];

        $pagePath = [
            '/cat/add/' => __t('Create New Category')
        ];

        if (empty($this->post)) {
            $this->render(
                'category/form',
                [
                    'formAction' => CategoryValuesObject::ADD_LINK,
                    'formData'   => $formData,
                    'categories' => $categoryModel->getAll(),
                    'pagePath'   => $pagePath
                ]
            );
        }

        $formObject = $categoryModel->add($this->post);

        if ($formObject->isStatusSuccess()) {
            $url = sprintf('/cat/%s/', $formObject->getSlug());

            $this->redirect($url);
        }

        $formData           = array_merge($formData, $formObject->exportRow());
        $formData['errors'] = $formObject->getErrors();

        $this->render(
            'category/form',
            [
                'formAction' => CategoryValuesObject::ADD_LINK,
                'formData'   => $formData,
                'categories' => $categoryModel->getAll(),
                'pagePath'   => $pagePath
            ]
        );
    }

    /**
     * Edit Category
     */
    public function actionEdit(): void
    {
        if (
            !array_key_exists('isSignedIn', $this->commonData) ||
            !(bool) $this->commonData['isSignedIn']
        ) {
            $this->redirect('/');
        }

        $id = (int) $this->getValueFromUrl('id');

        if (empty($id)) {
            $this->redirect('/error/404/');
        }

        $formErrors    = null;
        $categoryModel = $this->getModel('category');
        $category      = $categoryModel->getVOById($id);

        if (empty($category)) {
            $this->redirect('/error/404/');
        }

        $formData = [
            'errors'      => null,
            'title'       => $category->getTitle(),
            'slug'        => $category->getSlug(),
            'id_parent'   => $category->getParentId()
        ];

        $pagePath = $categoryModel->getHierarchy($category->getId());

        $pagePath[$category->getEditLink()] = __t('Edit Category');

        if (empty($this->post)) {
            $this->render(
                'category/form',
                [
                    'formAction'        => $category->getEditLink(),
                    'formData'          => $formData,
                    'categories'        => $categoryModel->getAll(),
                    'currentCategoryId' => $id,
                    'pagePath'          => $pagePath
                ]
            );
        }

        $formObject = $categoryModel->edit($id, $this->post);

        if ($formObject->isStatusSuccess()) {
            $url = sprintf('/cat/%s/', $formObject->getSlug());

            $this->redirect($url);
        }

        $formData           = $formObject->exportRow();
        $formData['errors'] = $formObject->getErrors();

        $this->render(
            'category/form',
            [
                'formAction'        => $category->getEditLink(),
                'formData'          => $formData,
                'categories'        => $categoryModel->getAll(),
                'currentCategoryId' => $id,
                'pagePath'          => $pagePath
            ]
        );
    }
}
