<?php 
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use OpencastApi\OpenCast;

class OcSerivesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $config = \Tests\DataProvider\SetupDataProvider::getConfig();
        $ocRestApi = new OpenCast($config);
        $this->ocServices = $ocRestApi->services;
    }

    /**
     * @test
     */
    public function get_services(): void
    {
        $response = $this->ocServices->getServiceJSON(
            'org.opencastproject.ingest'
        );
        $this->assertSame(200, $response['code'], 'Failure to get services list');
    }
}
?>