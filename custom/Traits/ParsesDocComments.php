<?php

namespace Covaleski\LaravelRoa\Traits;

use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;

trait ParsesDocComments
{
    /**
     * Context.
     */
    protected Context $context;

    /**
     * Doc block factory.
     */
    protected DocBlockFactory $docBlockFactory;

    /**
     * Create a phpDocumentor context for the specified class and file.
     */
    protected function createContext(string $class, string $filename): Context
    {
        $namespace = '\\' . ltrim(Str::beforeLast($class, '\\'), '\\');
        $contents = file_get_contents($filename);
        return (new ContextFactory())->createForNamespace($namespace, $contents);
    }

    /**
     * Parse a doc comment as a phpDocumentor doc block instance.
     */
    protected function parseDocComment(string $doc_comment): DocBlock
    {
        return $this->docBlockFactory->create($doc_comment, $this->context);
    }
}
