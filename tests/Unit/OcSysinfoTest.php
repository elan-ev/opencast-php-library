<?php 
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use OpencastApi\OpenCast;

class OcSysinfoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $config = \Tests\DataProvider\SetupDataProvider::getConfig();
        $ocRestApi = new OpenCast($config);
        $this->ocSysinfo = $ocRestApi->sysinfo;
    }

    /**
     * @test
     */
    public function get_version(): void
    {
        $responseAll = $this->ocSysinfo->getVersion();
        $this->assertSame(200, $responseAll['code'], 'Failure to get version bundle');

        $responseOpencast = $this->ocSysinfo->getVersion('opencast');
        $this->assertSame(200, $responseOpencast['code'], 'Failure to get version bundle with opencast prefix');
    }
}
?>