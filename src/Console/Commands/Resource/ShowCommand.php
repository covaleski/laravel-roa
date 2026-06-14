<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('resource:show {resource} {--cached}')]
#[Description('Show details of the specified resource.')]
class ShowCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get arguments
        $name = $this->argument('resource');
        $show_cache = $this->option('cached');
        // Check if resource exists
        if (Resource::exists($name)) {
            $accessor = Resource::get($name);
        } else {
            $this->warn("No resource named '{$name}'.");
            return;
        }
        // Get data
        $this->info("Retrieving '{$name}' resource...");
        if ($show_cache) {
            $this->line('Retrieving cached data...');
            if (!$accessor->isCached()) {
                $this->warn('Resource not cached yet.');
                return;
            }
            $accessor->load();
        } else {
            $this->line('Compiling resource...');
            $accessor->compile();

        }
        // Show data
        $resource = $accessor->get();
        $flags = JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;
        $this->newLine();
        $this->line(json_encode($resource, $flags));
        $this->newLine();
        // Show timestamp
        if ($show_cache) {
            $timestamp = date('Y-m-d H:i:s', $accessor->getTimestamp());
            $this->line("Cache last modified at {$timestamp}.");
        } else {
            $timestamp = date('Y-m-d H:i:s');
            $this->line("Compiled at {$timestamp}.");
            $this->warn('Use the --cached option to see actual cached data.');
        }
    }
}
