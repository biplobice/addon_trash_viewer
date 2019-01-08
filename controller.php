<?php
namespace Concrete\Package\TrashViewer;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    /**
     * Package handle.
     */
    protected $pkgHandle = 'trash_viewer';

    /**
     * Required concrete5 version.
     */
    protected $appVersionRequired = '8.1.0';

    /**
     * Package version.
     */
    protected $pkgVersion = '0.0.1';

    /**
     * Remove \Src from package namespace.
     */
    protected $pkgAutoloaderMapCoreExtensions = true;


    public function getPackageName()
    {
        return t('Trash Viewer');
    }

    public function getPackageDescription()
    {
        return t('View concrete5 pages in trash.');
    }

    public function install()
    {
        parent::install();
        $ci = new ContentImporter();
        $ci->importContentFile($this->getPackagePath() . '/install.xml');
    }
}