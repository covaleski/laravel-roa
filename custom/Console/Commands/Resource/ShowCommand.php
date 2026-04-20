<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('resource:show {resource}')]
#[Description('Compile and show the details of the specified resource.')]
class ShowCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $accessor = Resource::get($this->argument('resource'));
        $accessor->compile();
        $resource = $accessor->get();
        $flags = JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;
        $this->line(json_encode($resource, $flags));
    }
}
