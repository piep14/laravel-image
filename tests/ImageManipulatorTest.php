<?php

use Folklore\Image\ImageManipulator;
use Folklore\Image\Filters\Rotate as RotateFilter;
use Folklore\Image\Filters\Thumbnail as ThumbnailFilter;
use Imagine\Image\ImageInterface;

/**
 * @coversDefaultClass Folklore\Image\ImageManipulator
 */
class ImageManipulatorTest extends TestCase
{
    protected $manipulator;

    public function setUp()
    {
        parent::setUp();

        $this->manipulator = new ImageManipulator(app('image'));
    }

    public function tearDown()
    {
        if (file_exists(public_path('image-test.jpg'))) {
            unlink(public_path('image-test.jpg'));
        }

        parent::tearDown();
    }

    /**
     * Test set and get source
     * @test
     * @covers ::setSource
     * @covers ::getSource
     */
    public function testGetSource()
    {
        $source = app('image.source')->driver('local');
        $this->manipulator->setSource($source);
        $this->assertEquals($source, $this->manipulator->getSource());
    }

    /**
     * Test open image
     * @test
     * @covers ::open
     */
    public function testOpen()
    {
        $this->manipulator->setSource(app('image.source')->driver('local'));

        $image = $this->manipulator->open('image.jpg');

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertEquals(public_path('image.jpg'), $image->metadata()->get('filepath'));
        $this->assertEquals(300, $image->getSize()->getWidth());
        $this->assertEquals(300, $image->getSize()->getHeight());
    }

    /**
     * Test getting the format of an image
     * @test
     * @covers ::format
     */
    public function testFormat()
    {
        $this->manipulator->setSource(app('image.source')->driver('local'));

        $format = $this->manipulator->format('image.jpg');
        $this->assertEquals('jpeg', $format);

        $format = $this->manipulator->format('image.png');
        $this->assertEquals('png', $format);
    }

    /**
     * Test making an image
     * @test
     * @covers ::make
     */
    public function testMake()
    {
        $this->manipulator->setSource(app('image.source')->driver('local'));

        $returnImage = $this->manipulator->open('image.jpg');
        $returnImage = with(new ThumbnailFilter())->apply($returnImage, [
            'width' => 100,
            'height' => 100,
            'crop' => false
        ]);

        $rotateFilterMock = $this->getMockBuilder(RotateFilter::class)
            ->setMethods(['apply'])
            ->getMock();

        $rotateFilterMock->expects($this->once())
            ->method('apply')
            ->willReturn($returnImage);

        $thumbnailFilterMock = $this->getMockBuilder(ThumbnailFilter::class)
            ->setMethods(['apply'])
            ->getMock();

        $thumbnailFilterMock->expects($this->once())
            ->method('apply')
            ->willReturn($returnImage);

        app('image')->filter('rotate', $rotateFilterMock);
        app('image')->filter('thumbnail', $thumbnailFilterMock);

        $this->manipulator->setSource(app('image.source')->driver('local'));

        $image = $this->manipulator->make('image.jpg', [
            'width' => 100,
            'height' => 100,
            'rotate' => 90
        ]);

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertEquals(public_path('image.jpg'), $image->metadata()->get('filepath'));
        $this->assertEquals(100, $image->getSize()->getWidth());
        $this->assertEquals(100, $image->getSize()->getHeight());
    }

    /**
     * Test saving an image
     * @test
     * @covers ::save
     */
    public function testSave()
    {
        $this->manipulator->setSource(app('image.source')->driver('local'));

        $image = $this->manipulator->open('image.jpg');
        $this->manipulator->save($image, 'image-test.jpg');

        $this->assertTrue(file_exists(public_path('image-test.jpg')));
        $imageTest = $this->manipulator->open('image-test.jpg');
        $this->assertEquals($imageTest->getSize()->getWidth(), $image->getSize()->getWidth());
        $this->assertEquals($imageTest->getSize()->getHeight(), $image->getSize()->getHeight());
    }
}