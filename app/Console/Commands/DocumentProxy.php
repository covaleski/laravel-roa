<?php

namespace App\Console\Commands;

use Covaleski\LaravelRoa\Traits\ParsesDocComments;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

#[Signature('document:proxy {class}')]
#[Description('Outputs proxied properties and methods from ResourceCache.')]
class DocumentProxy extends Command
{
    use ParsesDocComments;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class = $this->argument('class');
        $reflection = new ReflectionClass($class);
        $this->context = $this->makeContext($class, $reflection->getFileName());
        $this->docBlockFactory = DocBlockFactory::createInstance();
        $this->line('/**');
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $this->line(' * ' . $this->formatProperty($property));
        }
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $this->line(' * ' . $this->formatMethod($method));
        }
        $this->line(' *');
        $this->line(" * @uses {$class} to proxy its members.");
        $this->line(' */');
    }

    /**
     * Format a class method as a phpDocumentor `@method` tag.
     */
    protected function formatMethod(ReflectionMethod $method): string
    {
        $doc_block = $this->parseDocComment($method->getDocComment());
        /** @var Param[] */
        $param_tags = $doc_block->getTagsByName('param');
        $parameters = [];
        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (Str::startsWith($name, '__')) continue;
            $filter = fn ($t) => $t->getName() === $name;
            $param_tag = array_filter($param_tags, $filter)[0] ?? null;
            $type = $param_tag?->getType() ?? $parameter->getType();
            $parameters[] = "{$type} \${$name}";
        }
        $name = $method->getName();
        /** @var Return_ */
        $return_tag = $doc_block->getTagsByName('return')[0] ?? null;
        $type = $return_tag?->getType() ?? $method->getReturnType();
        $parameters = implode(', ', $parameters);
        $description = $doc_block->getSummary();
        return "@method {$type} {$name}({$parameters}) {$description}";
    }

    /**
     * Format a class method as a phpDocumentor `@property` tag.
     */
    protected function formatProperty(ReflectionProperty $property): string
    {
        $doc_block = $this->parseDocComment($property->getDocComment());
        $name = $property->getName();
        /** @var ?Var_ */
        $var_tag = $doc_block->getTagsByName('var')[0] ?? null;
        $type = $var_tag?->getType() ?? $property->getType();
        $description = $doc_block->getSummary();
        return "@property {$type} \${$name} {$description}";
    }
}
