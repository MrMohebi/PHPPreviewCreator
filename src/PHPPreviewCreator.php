<?php


namespace MrMohebi;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Intervention\Image\ImageManagerStatic as Image;


class PHPPreviewCreator
{
    private string $source;
    private string $filename;
    private string $destinationPath;
    private string $mime;
    private bool $isVideo;
    private bool $isImage;

    public function __construct(string $source,string $destinationPath = null)
    {
        Image::configure(['driver' => 'imagick']);

        $this->source = $source;
        $sourceInfo = pathinfo($this->source);

        $this->destinationPath = $destinationPath ? rtrim($destinationPath, '/') : $sourceInfo['dirname'];
        $this->filename =  $sourceInfo['basename'];
        $this->mime =  mime_content_type($this->source);

        $type = explode("/",$this->mime)[0];
        $this->isVideo = $type === "video";
        $this->isImage = $type === "image";

    }

    public function videoPreview(string $destinationPath = null, string $resultName = null, int $second = 1, $height=640, $width=640):string|null{
        $resultPath = $destinationPath ?? $this->destinationPath . "/" . $resultName ?? ($this->filename . "_preview");

        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($this->source);
        $frame = $video->frame(TimeCode::fromSeconds($second));
        $frame->save($resultPath);
        return $resultPath;
    }
}