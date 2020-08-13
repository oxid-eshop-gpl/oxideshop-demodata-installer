<?php
/**
 * This file is part of OXID eSales Demo Data Installer.
 *
 * OXID eSales Demo Data Installer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales Demo Data Installer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales Demo Data Installer.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

namespace OxidEsales\DemoDataInstaller\Tests\Integration;

use org\bovigo\vfs\vfsStream;
use Webmozart\PathUtil\Path;
use Symfony\Component\Filesystem\Filesystem;
use OxidEsales\DemoDataInstaller\DemoDataPathSelector;
use OxidEsales\DemoDataInstaller\DemoDataInstaller;

class DemoDataInstallerTest extends \PHPUnit\Framework\TestCase
{
    private $temporaryPath;
    private $vendorPath;
    private $targetPath;

    public function setUp(): void
    {
        $this->temporaryPath = Path::join(__DIR__, '..', 'tmp');
        $this->vendorPath = Path::join(__DIR__, 'Fixtures');
        $this->targetPath = Path::join(__DIR__, '..', 'tmp', 'testTarget');
    }

    public function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->temporaryPath);
    }

    public function testExecuteDemoDataInstaller(): void
    {
        $demoDataInstaller = $this->buildDemoDataInstaller();

        $this->assertSame(0, $demoDataInstaller->execute());
        $this->assertSame(4, $this->countFiles($this->targetPath));
    }

    /**
     * @return DemoDataInstaller
     */
    private function buildDemoDataInstaller()
    {
        $facts = $this->getMockBuilder('Facts')
            ->setMethods(['getVendorPath', 'getOutPath'])
            ->getMock();
        $facts->expects($this->any())->method('getVendorPath')->willReturn($this->vendorPath);
        $facts->expects($this->any())->method('getOutPath')->willReturn($this->targetPath);

        $edition = 'CE';
        $demoDataPathSelector = new DemoDataPathSelector($facts, $edition);

        $filesystem = new Filesystem();

        return new DemoDataInstaller($facts, $demoDataPathSelector, $filesystem);
    }

    /**
     * @param $path
     *
     * @return int|void
     */
    private function countFiles($path)
    {
        return count(array_diff(scandir($path), ['.', '..']));
    }
}
