<?php

namespace App\Events;

use App\Models\EnglishWord;
use App\Models\StudyWord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UntranslatedWordEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $engWordId;
    public string $word;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EnglishWord|StudyWord $englishWord)
    {
        $this->engWordId = $englishWord->id;
        $this->word = $englishWord->word;
    }

//    /**
//     * Get the channels the event should broadcast on.
//     *
//     * @return \Illuminate\Broadcasting\Channel|array
//     */
//    public function broadcastOn()
//    {
//        return new PrivateChannel('channel-name');
//    }
}
