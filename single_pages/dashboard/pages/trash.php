<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
/** @var \Concrete\Core\Search\Pagination\Pagination $pagination */
if ($pagination) {
    $pages = $pagination->getCurrentPageResults();
}
?>

<?php if (is_array($pages) && count($pages) > 0): ?>
    <div class="ccm-dashboard-content-full">
        <div data-search-element="results">
            <div class="table-responsive">
                <table class="ccm-search-results-table selectable">
                    <thead>
                    <tr>
                        <th class="<?= $list->getSortClassName('cv.cvName'); ?>">
                            <a href="<?= $list->getSortURL('cv.cvName', 'asc'); ?>">
                                <?=t('Name')?>
                            </a>
                        </th>
                        <th><?php echo t('Path'); ?></th>
                        <th><?php echo t('Package'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pages as $page): ?>
                        <?php
                        $cp = new Permissions($page);
                        if ($page->getPackageID() > 0) {
                            $packageName = $controller->getPackageNameByID($page->getPackageID());
                        } else {
                            $packageName = t('None');
                        }
                        ?>
                        <tr>
                            <td><a href="<?=URL::to($page); ?>"><?php echo $page->getCollectionName(); ?></a></td>
                            <td><?php echo $page->getCollectionPath(); ?></td>
                            <td><?php echo $packageName; ?></td>
                            <?php if ($cp->canAdmin()): ?>
                                <td valign="top" style="text-align: center;"><a href="javascript:void(0)" class="btn btn-default btn-xs btn-danger" onclick="deletePage(<?= $page->getCollectionID(); ?>)"><?=t('Delete')?></a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pagination): ?>
                <div class="ccm-search-results-pagination">
                    <?= $pagination->renderDefaultView();?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="ccm-dashboard-header-buttons">
        <a href="javascript:void(0)" class="btn btn-default btn-danger" onclick="emptyTrash()"><?= t('Empty Trash'); ?></a>
    </div>

    <script>
        emptyTrash = function() {
            ConcreteAlert.confirm(
                <?= json_encode(t('Are you sure you want to delete all pages in trash?')); ?>,
                function() {
                    location.href = "<?= $controller->action('emptyTrash', $token->generate('emptyTrash')); ?>";
                },
                'btn-danger',
                <?= json_encode(t('Delete')); ?>
            );
        };

        deletePage = function(cID) {
            ConcreteAlert.confirm(
                <?= json_encode(t('Are you sure you want to delete this page?')); ?>,
                function() {
                    location.href = "<?= $controller->action('delete'); ?>/" + cID + "/<?= $token->generate('delete'); ?>";
                },
                'btn-danger',
                <?= json_encode(t('Delete')); ?>
            );
        };
    </script>

<?php else: ?>

    <p><?=t('There are no pages in trash.')?></p>

<?php endif; ?>