<?php

abstract class Items
{
    protected array $items = [];

    public function getFirst()
    {
        return $this->items[0] ?? null;
    }

    public function getList(): array
    {
        return $this->items;
    }

    public function filtered(callable $callback): array
    {
        return array_values(array_filter($this->items, $callback));
    }

    protected function applyFilter(callable $callback): void
    {
        $this->items = $this->filtered($callback);
    }
}

class MT_ProductManager extends Items
{
    public function __construct(array $items = [])
    {
        $this->items = $items;

        $this->applyFilter(function ($item) {
            return !str_ends_with($item['slug'] ?? '', '-fee');
        });
    }
}

class MT_MarketTypeManager extends Items
{
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
}

class MT_AccountTypeManager extends Items
{
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
}

class MT_SizeManager extends Items
{
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
}

class MT_PRICESManager
{
    private MT_ProductManager $productInstance;
    private MT_MarketTypeManager $marketTypesInstance;
    private MT_AccountTypeManager $accountTypesInstance;
    private MT_SizeManager $sizeManagerInstance;

    public function __construct(
        $products = [],
        $marketTypes = [],
        $accountTypes = [],
        $sizes = [],
        $platforms = [],
    )
    {
        $this->productInstance = new MT_ProductManager($products);
        $this->marketTypesInstance = new MT_MarketTypeManager($marketTypes);
        $this->accountTypesInstance = new MT_AccountTypeManager($accountTypes);
        $this->sizeManagerInstance = new MT_SizeManager($sizes);

    }

    public function accountTypesByMarketType(string $marketTypeSlug): array
    {
        $products = $this->productInstance->getList();

        return $this->accountTypesInstance->filtered(function ($accountType) use ($products, $marketTypeSlug) {
            $accountTypeSlug = $accountType['slug'] ?? null;

            $found = array_values(array_filter($products, function ($item) use ($accountTypeSlug, $marketTypeSlug) {
                return $item['tree_map']['market-type'] == $marketTypeSlug && $item['tree_map']['account-types'] == $accountTypeSlug;
            }));

            return count($found) > 0;
        });
    }

    public function accountSizesByAccountType($marketTypeSlug, $accountTypeSlug): array
    {
        $products = $this->productInstance->getList();

        return $this->sizeManagerInstance->getList();
    }

    public function marketTypes(): MT_MarketTypeManager
    {
        return $this->marketTypesInstance;
    }

    public function accountTypes(): MT_AccountTypeManager
    {
        return $this->accountTypesInstance;
    }

    public function products(): MT_ProductManager
    {
        return $this->productInstance;
    }
}