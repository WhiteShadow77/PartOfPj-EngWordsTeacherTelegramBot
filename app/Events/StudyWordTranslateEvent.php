<?php

namespace App\Events;

use App\Models\EnglishWord;
use App\Traits\LoggerTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudyWordTranslateEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    use LoggerTrait;

    public EnglishWord $englishWordModel;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EnglishWord $englishWordModel)
    {
        $this->writeInfoLog('Study word translate event');
        $this->englishWordModel = $englishWordModel;
    }
}
