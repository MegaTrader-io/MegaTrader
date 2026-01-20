<?php
/**
 * MT Prices Manager
 * Single-file domain layer for subscription pricing.
 * WordPress-friendly, SOLID-oriented, no external dependencies.
 */

/*
|--------------------------------------------------------------------------
| Base Collection
|--------------------------------------------------------------------------
*/

abstract class Items
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function getFirst()
    {
        return $this->items[0] ?? null;
    }

    public function getList(): array
    {
        return $this->items;
    }

    // PURE: no mutation
    public function filtered(callable $predicate): array
    {
        return array_values(array_filter($this->items, $predicate));
    }

    // MUTABLE: explicit mutation
    protected function apply(callable $predicate): void
    {
        $this->items = $this->filtered($predicate);
    }
}

/*
|--------------------------------------------------------------------------
| Product Manager (product business rules live here)
|--------------------------------------------------------------------------
*/

class MT_ProductManager extends Items
{
    public function __construct(array $items = [])
    {
        parent::__construct($items);

        // Business rule: exclude fee products
        $this->apply(fn($item) => !str_ends_with($item['slug'] ?? '', '-fee')
        );
    }

    public function matchesMarketAndAccountType(
        array  $product,
        string $marketTypeSlug,
        string $accountTypeSlug
    ): bool
    {
        $tree = $product['tree_map'] ?? [];

        return
            ($tree['market-type'] ?? null) === $marketTypeSlug &&
            ($tree['account-types'] ?? null) === $accountTypeSlug;
    }

    public function findByMarketAndAccountType(
        string $marketTypeSlug,
        string $accountTypeSlug
    ): ?array
    {
        return array_find(
            $this->items,
            fn($product) => $this->matchesMarketAndAccountType(
                $product,
                $marketTypeSlug,
                $accountTypeSlug
            )
        );
    }
}

/*
|--------------------------------------------------------------------------
| Simple Managers (pure collections)
|--------------------------------------------------------------------------
*/

class MT_MarketTypeManager extends Items
{
}

class MT_AccountTypeManager extends Items
{
}

class MT_AccountSizeManager extends Items
{
}

/*
|--------------------------------------------------------------------------
| Prices Orchestrator (Facade)
|--------------------------------------------------------------------------
*/

class MT_PRICESManager
{
    private MT_ProductManager $products;
    private MT_MarketTypeManager $marketTypes;
    private MT_AccountTypeManager $accountTypes;
    private MT_AccountSizeManager $accountSizes;

    public function __construct(
        array $products = [],
        array $marketTypes = [],
        array $accountTypes = [],
        array $sizes = [],
        array $platforms = [],
    )
    {
        $this->products = new MT_ProductManager($products);
        $this->marketTypes = new MT_MarketTypeManager($marketTypes);
        $this->accountTypes = new MT_AccountTypeManager($accountTypes);
        $this->accountSizes = new MT_AccountSizeManager($sizes);
    }

    /*
    |--------------------------------------------------------------------------
    | Facade methods (used by templates)
    |--------------------------------------------------------------------------
    */

    public function marketTypes(): MT_MarketTypeManager
    {
        return $this->marketTypes;
    }

    public function products(): MT_ProductManager
    {
        return $this->products;
    }

    public function productByMarketTypeAndAccountType(
        string $marketTypeSlug,
        string $accountTypeSlug
    ): ?array
    {
        return $this->products->findByMarketAndAccountType(
            $marketTypeSlug,
            $accountTypeSlug
        );
    }

    public function accountTypesByMarketType(string $marketTypeSlug): array
    {
        return $this->accountTypes->filtered(function ($accountType) use ($marketTypeSlug) {
            $accountTypeSlug = $accountType['slug'] ?? null;

            if (!$accountTypeSlug) {
                return false;
            }

            return (bool)$this->products->findByMarketAndAccountType(
                $marketTypeSlug,
                $accountTypeSlug
            );
        });
    }

    public function accountSizesByMarketTypeAndAccountType(
        string $marketTypeSlug,
        string $accountTypeSlug
    ): array
    {
        $product = $this->productByMarketTypeAndAccountType(
            $marketTypeSlug,
            $accountTypeSlug
        );

        if (!$product) {
            return [];
        }

        $variants = $product[$product['slug']] ?? [];
        $allowedSizes = array_keys($variants);

        return $this->accountSizes->filtered(
            fn($size) => in_array($size['slug'] ?? null, $allowedSizes, true)
        );
    }
}
