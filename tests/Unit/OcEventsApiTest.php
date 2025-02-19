<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use OpencastApi\Opencast;

class OcEventsApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $config = \Tests\DataProvider\SetupDataProvider::getConfig();
        $ocRestApi = new Opencast($config, [], false);
        $this->ocEventsApi = $ocRestApi->eventsApi;
    }

    /**
     * @test
     * @dataProvider \Tests\DataProvider\EventsDataProvider::getAllCases()
     */
    public function get_all_events($params): void
    {
        $response =  $this->ocEventsApi->getAll($params);

        $this->assertSame(200, $response['code'], 'Failure to get event list');
    }

    /**
     * @test
     * @dataProvider \Tests\DataProvider\EventsDataProvider::getBySeriesCases()
     */
    public function get_event_by_series($seriesIdentifier): void
    {
        $response = $this->ocEventsApi->getBySeries($seriesIdentifier);
        $this->assertSame(200, $response['code'], 'Failure to get event list by series id');
    }

    /**
     * @test
     */
    public function empty_random_event_id(): string
    {
        $identifier = '';
        $this->assertEmpty($identifier);

        return $identifier;
    }

    /**
     * @test
     * @depends empty_random_event_id
     */
    public function get_single_event(string $identifier): string
    {
        $responseAll = $this->ocEventsApi->getAll(['withacl' => true, 'includeInternalPublication' => true]);
        $this->assertSame(200, $responseAll['code'], 'Failure to get event list');
        $events = $responseAll['body'];
        if (!empty($events)) {
            $event = $events[array_rand($events)];
            $response = $this->ocEventsApi->get($event->identifier);
            $this->assertSame(200, $response['code'], 'Failure to get event');
            $event = $response['body'];
            $this->assertNotEmpty($event);
            $identifier = $event->identifier;
        } else {
            $this->markTestIncomplete('No event to complete the test!');
        }
        $this->assertNotEmpty($identifier);
        return $identifier;
    }

    /**
     * @test
     */
    public function empty_created_id(): string
    {
        $createdEventIdentifier = '';
        $this->assertEmpty($createdEventIdentifier);

        return $createdEventIdentifier;
    }

    /**
     * @test
     * @depends empty_created_id
     */
    public function create_and_update_event(string $createdEventIdentifier): string
    {
        $responseCreate = $this->ocEventsApi->create(
            \Tests\DataProvider\EventsDataProvider::getAcls(),
            \Tests\DataProvider\EventsDataProvider::getMetadata('presenter'),
            \Tests\DataProvider\EventsDataProvider::getProcessing(),
            '',
            \Tests\DataProvider\EventsDataProvider::getPresenterFile(),
            \Tests\DataProvider\EventsDataProvider::getPresentationFile(),
            \Tests\DataProvider\EventsDataProvider::getAudioFile(),
            array($this, 'progressCallback')
        );
        $this->assertContains($responseCreate['code'], [200, 201], 'Failure to create an event');
        $createdEventIdentifier = $responseCreate['body']->identifier;
        $this->assertNotEmpty($createdEventIdentifier);
        sleep(5);

        $metadata = str_replace(
            '{update_replace}',
            'UPDATE ON: ' . strtotime('now'),
            \Tests\DataProvider\EventsDataProvider::getMetadata('presenter')
        );
        $responseUpdate = $this->ocEventsApi->update($createdEventIdentifier, '', $metadata);
        $this->assertSame(204, $responseUpdate['code'], 'Failure to update an event');

        return $createdEventIdentifier;
    }

    public function progressCallback($downloadSize, $downloaded, $uploadSize, $uploaded)
	{
        set_time_limit(0);// Reset time limit for big files
        static $previous_progress = 0;
		$progress = 0;
        if($uploadSize > 0) {
			$progress = round(($uploaded / $uploadSize)  * 100);
		}
        if ($progress > $previous_progress) {
			$previous_progress = $progress;
            file_put_contents(__DIR__ . '/../Results/progress.txt', $progress);
		}
    }

    /**
     * @test
     * @depends create_and_update_event
     */
    public function delete_events(string $createdEventIdentifier): void
    {
        $response = $this->ocEventsApi->delete($createdEventIdentifier);
        $this->assertContains($response['code'], [202, 204], 'Failure to delete an event');
    }

    /**
     * @test
     * @depends get_single_event
     */
    public function add_tracks(string $identifier)
    {
        $flavor = 'captions/source';
        $tags = [
            'archive',
            'subtitle',
            'type:subtitle',
            'generator-type:manual',
        ];
        $langde = ['lang:de'];
        $langen = ['lang:en'];

        $tags_de = array_merge($tags, $langde);
        $tags_en = array_merge($tags, $langen);
        // Adding first Track in DE language!
        $response1 = $this->ocEventsApi->addTrack(
            $identifier,
            $flavor,
            \Tests\DataProvider\EventsDataProvider::getVttFile('de'),
            false,
            $tags_de
        );
        $this->assertSame(200, $response1['code'], 'Failure to add Track DE');

        // Adding second Track in EN language!
        $response2 = $this->ocEventsApi->addTrack(
            $identifier,
            $flavor,
            \Tests\DataProvider\EventsDataProvider::getVttFile('en'),
            false,
            $tags_en
        );
        $this->assertSame(200, $response2['code'], 'Failure to add Track EN');

        // Overwrite the existing track DE!
        $response3 = $this->ocEventsApi->addTrack(
            $identifier,
            $flavor,
            \Tests\DataProvider\EventsDataProvider::getVttFile('de', true),
            true,
            $tags_de
        );
        $this->assertSame(200, $response3['code'], 'Failure to override the DE language subtitle');

        // Remove EN subtitle track!
        $response4 = $this->ocEventsApi->addTrack(
            $identifier,
            $flavor,
            null,
            true,
            $tags_en
        );
        $this->assertSame(200, $response4['code'], 'Failure to remove subtitle track EN');

        return $identifier;
    }

    /**
     * @test
     * @depends add_tracks
     */
    public function get_update_delete_acls(string $identifier): string
    {
        // Get ACL.
        $response1 = $this->ocEventsApi->getAcl($identifier);
        $this->assertSame(200, $response1['code'], 'Failure to get ACLs of an event');

        $acls = $response1['body'];
        if (empty($acls)) {
            $response1_1 = $this->ocEventsApi->addSingleAcl($identifier, 'write', 'ROLE_PHPUNIT_TESTING_USER_0');
            $this->assertSame(204, $response1_1['code'], 'Failure to set single ACL for an event');

            $response1_2 = $this->ocEventsApi->getAcl($identifier);
            $this->assertSame(200, $response1_2['code'], 'Failure to get ACLs of an event');

            $acls = $response1_2['body'];
        }

        $this->assertNotEmpty($acls);

        // Delete all acls.
        $response2 =  $this->ocEventsApi->emptyAcl($identifier);
        $this->assertSame(204, $response2['code'], 'Failure to delete ACLs of an event');

        // Prepare to update acls.
        $response3 = $this->ocEventsApi->updateAcl($identifier, $acls);
        $this->assertSame(204, $response3['code'], 'Failure to update ACLs of an event');

        // Set Single ACL
        $response3 = $this->ocEventsApi->addSingleAcl($identifier, 'write', 'ROLE_PHPUNIT_TESTING_USER_2');
        $this->assertSame(204, $response3['code'], 'Failure to set single ACL for an event');

        // Delete single acl.
        $response4 = $this->ocEventsApi->deleteSingleACL($identifier, 'write', 'ROLE_PHPUNIT_TESTING_USER_2');
        $this->assertSame(204, $response4['code'], 'Failure to delete single ACL for an event.');

        $this->assertNotEmpty($identifier);
        return $identifier;
    }

    /**
     * @test
     * @depends get_update_delete_acls
     */
    public function get_media(string $identifier): string
    {
        $response = $this->ocEventsApi->getMedia($identifier);
        $this->assertSame(200, $response['code'], 'Failure to get media of an event');

        $this->assertNotEmpty($identifier);
        return $identifier;
    }

    /**
     * @test
     * @depends get_media
     */
    public function get_update_delete_metadata(string $identifier): string
    {
        $response1 = $this->ocEventsApi->getMetadata($identifier);
        $this->assertSame(200, $response1['code'], 'Failure to get metadata of an event');

        $type = 'dublincore/episode';
        $response2 = $this->ocEventsApi->getMetadata($identifier, $type);
        $this->assertSame(200, $response2['code'], 'Failure to get type metadata of an event');

        $metadata = $response2['body'];
        $metadata[0]->value .= ' (PHPUNIT UPDATED)';
        $this->assertNotEmpty($metadata);

        $response3 = $this->ocEventsApi->updateMetadata($identifier, $type, $metadata);
        $this->assertSame(204, $response3['code'], 'Failure to update type metadata of an event');


        $response4 = $this->ocEventsApi->deleteMetadata($identifier, $type);
        $this->assertSame(403, $response4['code'], 'Failure to delete type metadata of an event');

        $this->assertNotEmpty($identifier);
        return $identifier;
    }

    /**
     * @test
     * @depends get_update_delete_metadata
     */
    public function get_publications(string $identifier): string
    {
        $response1 = $this->ocEventsApi->getPublications($identifier, true, true);
        $this->assertSame(200, $response1['code'], 'Failure to get publications of an event');

        $publications = $response1['body'];
        if (!empty($publications)) {
            $publication = is_array($publications) ? $publications[0] : $publications;
            $response2 = $this->ocEventsApi->getSinglePublication($identifier, $publication->id, true);
            $this->assertSame(200, $response2['code'], 'Failure to get single publication of an event');

            $publication = $response2['body'];
            $this->assertNotEmpty($publication);
        } else {
            $this->markTestIncomplete('No publication to complete the test!');
        }

        $this->assertNotEmpty($identifier);
        return $identifier;
    }

    /**
     * @test
     * @depends get_publications
     */
    public function get_update_scheduling(string $identifier): void
    {
        $response1 = $this->ocEventsApi->getScheduling($identifier);
        $this->assertContains($response1['code'], [200, 204], 'Failure to get scheduling of an event');

        $schedulings = $response1['body'];
        if (!empty($schedulings)) {
            $response2 = $this->ocEventsApi->updateScheduling($identifier, $schedulings);
            $this->assertSame(204, $response2['code'], 'Failure to update scheduling of an event');
        } else {
            $this->markTestIncomplete('No scheduling to complete the test!');
        }
    }
}
?>
