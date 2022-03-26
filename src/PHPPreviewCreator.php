<?php


namespace MrMohebi;

use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Intervention\Image\ImageManagerStatic as Image;


class PHPPreviewCreator
{
    private string $source;
    private string $filename;
    private string $extension;
    private string $destinationPath;
    private string $mime;
    private bool $isVideo;
    private bool $isImage;

    /**
     * @param string $source
     * @param string|null $destinationPath
     * @throws Exception
     */
    public function __construct(string $source, string $destinationPath = null)
    {
        Image::configure(['driver' => 'imagick']);

        $this->source = $source;
        $sourceInfo = pathinfo($this->source);

        $this->destinationPath = $destinationPath ? rtrim($destinationPath, '/') : $sourceInfo['dirname'];
        $this->filename =  $sourceInfo['filename'];
        $this->extension =  $sourceInfo['extension'];
        $this->mime =  mime_content_type($this->source);
        $type = explode("/",$this->mime)[0];
        $this->isVideo = $type === "video";
        $this->isImage = $type === "image";

        if(!($this->isImage || $this->isVideo)){
            throw new Exception("file is neither image nor video");
        }

    }

    public function preview(string $destinationPath = null, string $resultName = null, int $height=720, int $width=640):string|null{
        $resultPath = ($destinationPath ?? $this->destinationPath) . "/";
        if($this->isVideo){
            $resultPath .= ( $resultName ?? ($this->filename . "_preview.jpeg"));
            $this->videoPreview($resultPath, 1, $height, $width);
        }elseif ($this->isImage){
            $resultPath .= ( $resultName ?? ($this->filename . "_preview." . $this->extension));
            $this->imagePreview($resultPath, $height, $width);
        }

        return $resultPath;
    }


    private function imagePreview(string $resultPath, int $height, int $width):void{
        Image::make($this->source)->fit($width, $height)->save($resultPath, 10);
    }

    private function videoPreview(string $resultPath, int $second , int $height, int $width):void{
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($this->source);
        $frame = $video->frame(TimeCode::fromSeconds($second));
        $frame->save($resultPath);

        Image::make($resultPath)->fit($width, $height)->save(null, 10);
    }
}