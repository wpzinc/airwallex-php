<?php

declare(strict_types=1);

namespace WPZinc\Airwallex;

use WPZinc\Airwallex\HttpClient\Response;

/**
 * A page of results from a list endpoint.
 *
 * Airwallex uses two pagination styles, both handled by autoPagingIterator():
 * - offset: page_num / page_size, with has_more in the response
 * - cursor: page / page_size, with page_after / page_before in the response
 *
 * @template TItem of AirwallexObject
 */
class Collection extends AirwallexObject
{
    /** @var list<TItem> */
    protected array $items = [];

    /** @var array<string, mixed> */
    protected array $params = [];

    /** @var (\Closure(array<string, mixed>): Collection<TItem>)|null */
    protected ?\Closure $fetcher = null;

    /**
     * @param array<mixed>               $data      Decoded response body (object with items, or a plain list).
     * @param class-string<TItem>        $itemClass
     * @param array<string, mixed>       $params    Params used to fetch this page.
     * @param (\Closure(array<string, mixed>): Collection<TItem>)|null $fetcher
     */
    public static function fromResponse(
        array $data,
        string $itemClass,
        array $params = [],
        ?\Closure $fetcher = null,
        ?Response $response = null,
    ): static {
        $rawItems = array_is_list($data) ? $data : ($data['items'] ?? []);
        $meta     = array_is_list($data) ? [] : $data;
        unset($meta['items']);

        $collection          = static::constructFrom($meta, $response);
        $collection->params  = $params;
        $collection->fetcher = $fetcher;
        $collection->items   = [];

        foreach (is_array($rawItems) ? $rawItems : [] as $item) {
            $collection->items[] = is_array($item) ? $itemClass::constructFrom($item) : $item;
        }

        return $collection;
    }

    /**
     * @return list<TItem>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @return TItem|null
     */
    public function first(): ?AirwallexObject
    {
        return $this->items[0] ?? null;
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return \ArrayIterator<int, TItem>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $offset === 'items' ? $this->items : parent::offsetGet($offset);
    }

    public function __get(string $name): mixed
    {
        return $name === 'items' ? $this->items : parent::__get($name);
    }

    /**
     * Whether another page exists after this one.
     */
    public function hasMore(): bool
    {
        return $this->nextPageParams() !== null;
    }

    /**
     * Params for the next page, or null if this is the last page.
     *
     * @return array<string, mixed>|null
     */
    public function nextPageParams(): ?array
    {
        if ($this->items === []) {
            return null;
        }

        $after = $this->values['page_after'] ?? null;
        if (is_string($after) && $after !== '') {
            return array_merge($this->params, ['page' => $after]);
        }

        if (($this->values['has_more'] ?? false) === true) {
            return array_merge($this->params, ['page_num' => ((int) ($this->params['page_num'] ?? 0)) + 1]);
        }

        return null;
    }

    /**
     * Params for the previous page (cursor pagination only), or null.
     *
     * @return array<string, mixed>|null
     */
    public function previousPageParams(): ?array
    {
        $before = $this->values['page_before'] ?? null;
        if (is_string($before) && $before !== '') {
            return array_merge($this->params, ['page' => $before]);
        }

        $pageNum = (int) ($this->params['page_num'] ?? 0);
        if (array_key_exists('has_more', $this->values) && $pageNum > 0) {
            return array_merge($this->params, ['page_num' => $pageNum - 1]);
        }

        return null;
    }

    /**
     * Fetch the next page, or null if there isn't one.
     *
     * @return static|null
     */
    public function nextPage(): ?Collection
    {
        $params = $this->nextPageParams();

        return $params === null || $this->fetcher === null ? null : ($this->fetcher)($params);
    }

    /**
     * Fetch the previous page, or null if there isn't one.
     *
     * @return static|null
     */
    public function previousPage(): ?Collection
    {
        $params = $this->previousPageParams();

        return $params === null || $this->fetcher === null ? null : ($this->fetcher)($params);
    }

    /**
     * Iterate every item across all pages, fetching pages lazily.
     *
     * @return \Generator<int, TItem>
     */
    public function autoPagingIterator(): \Generator
    {
        $page = $this;
        while ($page !== null) {
            foreach ($page->items as $item) {
                yield $item;
            }
            $page = $page->nextPage();
        }
    }

    public function toArray(): array
    {
        return parent::toArray() + ['items' => array_map(static fn ($i) => $i instanceof AirwallexObject ? $i->toArray() : $i, $this->items)];
    }
}
