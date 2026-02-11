<?php

namespace App\Jobs;

use App\Http\Controllers\AudioBookController;
use App\Models\AudioBook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

class GenerateDescriptionVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $audioBookId;
    public string $imagePath;
    public string $imageType;

    public function __construct(int $audioBookId, string $imagePath, string $imageType)
    {
        $this->audioBookId = $audioBookId;
        $this->imagePath = $imagePath;
        $this->imageType = $imageType;
    }

    public function handle(): void
    {
        $audioBook = AudioBook::find($this->audioBookId);
        if (!$audioBook) {
            return;
        }

        $controller = app(AudioBookController::class);
        $request = new Request([
            'image_path' => $this->imagePath,
            'image_type' => $this->imageType,
        ]);

        $controller->generateDescriptionVideo($request, $audioBook);
    }
}
