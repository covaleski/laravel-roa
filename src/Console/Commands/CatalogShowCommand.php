<?php

namespace Covaleski\Laravel\Catalog\Console\Commands;

use Covaleski\Laravel\Catalog\Facades\Catalog;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('catalog:show {model} {--cached}')]
#[Description('Show details of the specified model.')]
class CatalogShowCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get arguments
        $class_name = $this->argument('model');
        $show_cache = $this->option('cached');
        // Check if model exists
        if (Catalog::exists($class_name)) {
            $accessor = Catalog::get($class_name);
        } else {
            $this->warn("No model named '{$class_name}'.");
            return;
        }
        // Get data
        $this->info("Retrieving '{$class_name}' model...");
        if ($show_cache) {
            $this->line('Retrieving cached data...');
            if (!$accessor->isCached()) {
                $this->warn('Model not cached yet.');
                return;
            }
            $accessor->load();
        } else {
            $this->line('Compiling model...');
            $accessor->compile();

        }
        // Show data
        $model = $accessor->get();
        $flags = JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;
        $this->newLine();
        $this->line(json_encode($model, $flags));
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
