<?php

namespace Covaleski\LaravelRoa\Console\Commands\Resource;

use Covaleski\LaravelRoa\Facades\Resource;
use Covaleski\LaravelRoa\Interfaces\ResourceAttributeInterface;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

#[Signature('resource:list')]
#[Description('List all currently mapped models.')]
class ListCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->table(
            [
                'Name',
                'Model',
                'Attributes',
            ],
            Arr::map(Resource::all(), fn ($resource) => [
                $resource->name,
                $resource->model,
                collect($resource->attributes)
                    ->map(fn ($v) => $this->formatAttribute($v))
                    ->join("\n"),
            ]),
        );
    }

    /**
     * Format a resource attribute as a string.
     */
    protected function formatAttribute(ResourceAttributeInterface $attribute): string
    {
        $flags = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT;
        return $attribute::class . ' ' . json_encode($attribute, $flags);
    }
}
