<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

/**
 * Class PinsTest.
 */
class PinsTest extends ProviderTest
{
    /**
     * @var Pins
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Pins::class;

    /** @test */
    public function it_should_like_pins()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->like(1111));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->like(1111));
    }

    /** @test */
    public function it_should_unlike_pins()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->unLike(1111));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->unLike(1111));
    }

    /** @test */
    public function it_should_create_comments_for_pin()
    {
        $this->setSuccessResponse();
        $this->assertNotEmpty($this->provider->comment(1111, 'comment text'));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->comment(1111, 'comment text'));
    }

    /** @test */
    public function it_should_delete_comments_for_pin()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->deleteComment(1111, 1111));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->deleteComment(1111, 1111));
    }

    /** @test */
    public function it_should_create_new_pin()
    {
        $response = $this->createPinCreationResponse();
        $this->setResponse($response);

        $pinSource = 'http://example.com/image.jpg';
        $pinDescription = 'Pin Description';
        $boardId = 1;
        $this->assertNotFalse($this->provider->create($pinSource, $boardId, $pinDescription));

        $this->setResponse(null);
        $this->assertFalse($this->provider->create($pinSource, $boardId, $pinDescription));
    }

    /** @test */
    public function it_should_upload_images_when_creating_pin_with_local_image()
    {
        $image = 'image.jpg';
        $this->requestMock
            ->shouldReceive('upload')
            ->withArgs([$image, UrlHelper::IMAGE_UPLOAD]);

        $response = $this->createPinCreationResponse();
        $this->setResponse($response);
        $this->provider->create($image, 1, 'test');
    }

    /** @test */
    public function it_should_create_repin()
    {
        $response = $this->createPinCreationResponse();
        $this->setResponse($response);

        $boardId = 1;
        $repinId = 11;
        $pinDescription = 'Pin Description';

        $this->assertNotFalse($this->provider->repin($repinId, $boardId, $pinDescription));
        
        $this->setErrorResponse();
        $this->assertFalse($this->provider->repin($repinId, $boardId, $pinDescription));
    }

    /** @test */
    public function it_should_edit_pins()
    {
        $response = $this->createApiResponse();
        $this->setResponse($response);
        $this->assertNotFalse($this->provider->edit(1, 'new', 'changed'));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->edit(1, 'new', 'changed'));
    }

    /** @test */
    public function it_should_delete_pin()
    {
        $response = $this->createApiResponse();
        $this->setResponse($response);
        $this->assertNotFalse($this->provider->delete(1));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->delete(1));
    }

    /** @test */
    public function it_should_return_pin_info()
    {
        $response = $this->createApiResponse();
        $this->setResponse($response);
        $this->assertNotNull($this->provider->info(1));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->info(1));
    }

    /** @test */
    public function it_should_return_iterator_when_searching()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $this->setResponse($response, 2);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res);
    }

    /** @test */
    public function it_should_move_pins_between_boards()
    {
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->moveToBoard(1111, 1));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->moveToBoard(1111, 1));
    }

    /** @test */
    public function it_should_return_iterator_with_pins_for_specific_site()
    {
        $response = $this->createPaginatedResponse();
        $this->setResponse($response);
        $this->setResourceResponseData([]);

        $pins = $this->provider->fromSource('flickr.ru');
        $this->assertCount(2, iterator_to_array($pins));
    }

    /** @test */
    public function it_should_return_iterator_with_pin_activity()
    {
        $response = $this->createApiResponse(
            ['data' => ['aggregated_pin_data' => ['id' => 1]]]
        );
        $this->setResponse($response);

        $this->setResponse($this->createPaginatedResponse());
        $this->setResourceResponseData([]);

        $this->assertCount(2, iterator_to_array($this->provider->activity(1)));
    }

    /** @test */
    public function it_should_return_null_for_empty_activity()
    {
        $this->setResponse($this->createApiResponse());
        $this->assertNull($this->provider->activity(1));
    }

    /**
     * Creates a pin creation response from Pinterest.
     *
     * @return array
     */
    protected function createPinCreationResponse()
    {
        $data = ['data' => ['id' => 1]];

        return $this->createApiResponse($data);
    }

    /**
     * Creates a response from Pinterest.
     *
     * @param array $data
     *
     * @return array
     */
    protected function createApiResponse($data = ['data' => 'success'])
    {
        return parent::createApiResponse($data);
    }
}
