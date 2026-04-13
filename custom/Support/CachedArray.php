<?php

namespace Covaleski\LaravelRoa\Support;

use ArrayAccess;
use Illuminate\Contracts\Filesystem\Filesystem;
use Iterator;

class CachedArray implements ArrayAccess, Iterator
{
    /**
     * Current data.
     *
     * @var array<string, mixed>
     */
    protected array $data;

    /**
     * Create the cached array instance.
     */
    public function __construct(
        /**
         * Directory disk instance.
         */
        protected Filesystem $disk,

        /**
         * File path.
         */
        protected string $path,

        /**
         * Function that generates the initial content.
         *
         * @var callable(): array
         */
        protected mixed $callback,
    ) {
        //
    }

    /**
     * Return the current element.
     */
    public function current(): mixed
    {
        isset($this->data) or $this->load();
        return current($this->data);
    }

    /**
     * Return the key of the current element.
     */
    public function key(): mixed
    {
        isset($this->data) or $this->load();
        return key($this->data);
    }

    /**
     * Move forward to next element.
     */
    public function next(): void
    {
        isset($this->data) or $this->load();
        next($this->data);
    }

    /**
     * Check whether an offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        isset($this->data) or $this->load();
        return array_key_exists($offset, $this->data);
    }

    /**
     * Retrieve an offset.
     */
    public function offsetGet(mixed $offset): mixed
    {
        isset($this->data) or $this->load();
        return $this->data[$offset];
    }

    /**
     * Set an offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        isset($this->data) or $this->load();
        $this->data[$offset] = $value;
    }

    /**
     * Unset an offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        isset($this->data) or $this->load();
        unset($this->data[$offset]);
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind(): void
    {
        isset($this->data) or $this->load();
        reset($this->data);
    }

    /**
     * Checks if current position is valid.
     */
    public function valid(): bool
    {
        isset($this->data) or $this->load();
        return key($this->data) !== null;
    }

    /**
     * Generates the initial contents of the array.
     */
    protected function generate(): array
    {
        return call_user_func($this->callback);
    }

    /**
     * Loads the initial contents of the array.
     */
    protected function load(): void
    {
        if ($this->disk->exists($this->path)) {
            $this->data = unserialize($this->disk->get($this->path));
        } else {
            $this->data = $this->generate();
            $this->disk->put($this->path, serialize($this->data));
        }
    }
}
