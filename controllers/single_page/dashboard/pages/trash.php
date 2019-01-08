<?php
namespace Concrete\Package\TrashViewer\Controller\SinglePage\Dashboard\Pages;

use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Search\Pagination\PaginationFactory;
use Permissions;
use PermissionKey;

class Trash extends DashboardPageController
{
    public function view()
    {
        $list = $this->getTrashPageList();
        $factory = new PaginationFactory($this->request);
        $pagination = $factory->createPaginationObject($list, PaginationFactory::PERMISSIONED_PAGINATION_STYLE_PAGER);
        $this->set('list', $list);
        $this->set('pagination', $pagination);
    }

    public function restore($cID, $token)
    {
        //@TODO
        // Now concrete5 doesn't have restore functionality.
    }

    public function delete($cID, $token)
    {
        if ($cID < 1) {
            $this->error->add(t('Invalid page ID.'));
        }

        if (!$this->token->validate('delete', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $page = Page::getByID($cID);
            if ($page->getCollectionID()) {
                if ($this->deletePage($page) === false) {
                    $this->flash('error', t('Delete failed.'));
                }
                $this->flash('message', t('Deleted successfully.'));
            }
        }
        $this->view();
    }

    public function emptyTrash($token)
    {
        if (!$this->token->validate('emptyTrash', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }

        $pk = PermissionKey::getByHandle('empty_trash');
        if ($pk && !$pk->validate()) {
            $this->error->add(t('Access Denied.'));
        }

        if (!$this->error->has()) {
            $list = $this->getTrashPageList();
            $pages = $list->getResults();
            if (is_array($pages) && count($pages) > 0) {
                foreach ($pages as $page) {
                    $this->deletePage($page);
                }
            }
        }

        $this->view();
    }

    protected function getTrashPageList()
    {
        $config = $this->app->make('config');
        $trash = $config->get('concrete.paths.trash');

        $list = new PageList();
        $list->includeSystemPages();
        $list->includeInactivePages();
        $list->setPageVersionToRetrieve(PageList::PAGE_VERSION_RECENT);
        $list->filterByPath($trash);

        return $list;
    }
    private function deletePage(Page $page)
    {
        $cp = new Permissions($page);
        if ($cp->canDeletePage()) {
            return $page->delete();
        }

        return false;
    }

    public function getPackageNameByID($pkgID)
    {
        $packageName = '';
        $package = $this->app->make(PackageService::class)->getByID($pkgID);
        if ($package) {
            $packageName = $package->getPackageName();
        }

        return $packageName;
    }
}