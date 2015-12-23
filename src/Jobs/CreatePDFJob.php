<?php

namespace Eboost\PDFCreator\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CreatePDFJob implements ShouldQueue
{
    use Queueable, DispatchesJobs;

    /**
     * @var
     */
    private $html;

    /**
     * @var
     */
    private $fileName;

    /**
     * @var array
     */
    private $options;
    /**
     * @var array
     */
    private $S3Options;

    /**
     * CreatePDFJob constructor.
     * @param $html
     * @param $fileName
     * @param array $options
     * @param array $S3Options
     */
    public function __construct($html, $fileName, array $options, array $S3Options = null)
    {
        $this->html = $html;
        $this->fileName = $fileName;
        $this->options = $options;
        $this->S3Options = $S3Options ?: \App::getFacadeApplication()['config']['filesystems.disks.s3'];
    }

    public function handle()
    {
        $pdf = \PDF::loadHTML($this->html)->setOptions($this->options);

        $fileSystem = \Storage::createS3Driver($this->S3Options);
        $fileSystem->put($this->fileName, $pdf->output());
    }
}
