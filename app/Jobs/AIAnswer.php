<?php

namespace App\Jobs;

use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class AIAnswer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $question)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AIService $AIService): void
    {
        $listOfThings = config('list-of-things');
        foreach ($listOfThings as $id => $thing) {
            if ($answer = $AIService->answerQuestion($this->question, $thing['text'])) {
                Config::set("list-of-things.{$id}.answer", $answer);
            }
        }
        $this->saveData();
    }

    /**
     * Save updated list of things.
     *
     * @return void
     */
    private function saveData(): void
    {
        // open config file for writing
        $fp = fopen(base_path() . '/config/list-of-things.php', 'w');

        // write updated runtime config to file
        fwrite($fp, '<?php return ' . var_export(config('list-of-things'), true) . ';');

        // close the file
        fclose($fp);
    }
}
