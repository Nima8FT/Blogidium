<?php

namespace Modules\Media\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Services\ImageUploadService;
use Tests\TestCase;

class MediaTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_image_uploads_successfully_and_returns_correct_path()
    {
        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('test_image.jpg');
        $service = new ImageUploadService;
        $result = $service->imageUpload($fakeImage, 'articles');
        $this->assertStringStartsWith('public/articles/', $result);
        $this->assertStringEndsWith('.jpg', $result);
        $relativePath = str_replace('public/', '', $result);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_image_upload_fails_with_invalid_file()
    {
        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->create('not_an_image.txt', 1, 'text/plain');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid image file.');
        $service = new ImageUploadService;
        $service->imageUpload($fakeFile, 'articles');
    }
}
