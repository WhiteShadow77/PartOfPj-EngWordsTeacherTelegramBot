<?php

namespace App\Events;

use App\Services\ForeignService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnglishWordTranslateEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public ?Model $englishWordModel,
        public string $transcription,
        public array $translations,
        public array $pronunciationFileNamesAndDirs, //first item for uk pron., second item for us pron.
        public ForeignService $source,
        public ?string $englishWord = null
    ) {
    }
}
