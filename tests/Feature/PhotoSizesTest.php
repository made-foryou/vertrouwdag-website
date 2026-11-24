<?php

use Illuminate\Http\UploadedFile;
use Made\CmsPackage\Content\Actions\UploadMedia;
use Made\CmsPackage\Content\Data\UploadedMedia;

test('an uploaded photo gets the narrower sizes a phone chooses from, up to the width of the photo', function () {
    echteUploads();
    $file = UploadedFile::fake()->image('ringen.jpg', 1920, 1280);

    $media = UploadMedia::run(new UploadedMedia(path: $file->getPathname(), filename: 'ringen.jpg'));

    expect(array_column($media->variants ?? [], 'width'))->toBe([320, 480, 640, 960, 1440])
        ->and($media->width)->toBe(1920);
});
