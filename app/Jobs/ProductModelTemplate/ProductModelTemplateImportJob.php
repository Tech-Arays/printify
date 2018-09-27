<?php

namespace App\Jobs\ProductModelTemplate;

use Artisan;
use Cache;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Job;

class ProductModelTemplateImportJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('kz:template-import');
        $output = Artisan::output();
        Cache::forever('ProductModelTemplateImportJob_result', $output);
        Cache::forget('ProductModelTemplateImportJob_processing');
    }
}
