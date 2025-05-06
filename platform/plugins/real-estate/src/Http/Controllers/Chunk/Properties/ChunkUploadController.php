<?php

namespace Botble\RealEstate\Http\Controllers\Chunk\Properties;

use Botble\Media\Chunks\Handler\DropZoneUploadHandler;
use Botble\Media\Chunks\Receiver\FileReceiver;
use Botble\RealEstate\Http\Controllers\Chunk\ChunkController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChunkUploadController extends ChunkController
{
    public function __invoke(Request $request)
    {
        $receiver = new FileReceiver('file', $request, DropZoneUploadHandler::class);
        $sessionId = $request->input('dzuuid', Str::uuid());

        return $this->uploadFile($receiver, $sessionId, 'app/property-import');
    }
}
