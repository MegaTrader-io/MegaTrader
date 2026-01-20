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

    public function extractPlatformsFromProduct(array $product, array $accountSizes): array
    {
        $treeMap = $product['tree_map'] ?? [];

        if (!isset($treeMap['platform'])) {
            return [];
        }

        $indexAttribute = array_search('platform', array_keys($treeMap), true);

        if ($indexAttribute === false) {
            return [];
        }

        $productSlug = $product['slug'];
        $variationData = $product[$productSlug] ?? [];

        $platformsFound = [];

        foreach ($accountSizes as $accountSize) {
            $sizeSlug = $accountSize['slug'] ?? null;

            if (!$sizeSlug || !isset($variationData[$sizeSlug])) {
                continue;
            }

            $level = 1;
            $properties = [];

            while ($level <= $indexAttribute) {
                $properties = empty($properties)
                    ? array_values($variationData[$sizeSlug])[0]
                    : array_values($properties)[0];

                if ($indexAttribute - 1 === $level) {
                    $platformSlug = array_key_first($properties);

                    if ($platformSlug && !in_array($platformSlug, $platformsFound, true)) {
                        $platformsFound[] = $platformSlug;
                    }
                }

                $level++;
            }
        }

        return $platformsFound;
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

class MT_PlatformManager extends Items
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
    private MT_PlatformManager $platforms;

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
        $this->platforms = new MT_PlatformManager($platforms);
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

    public function platformsByMarketTypeAccountTypeAndSizes(
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

        $sizes = $this->accountSizesByMarketTypeAndAccountType(
            $marketTypeSlug,
            $accountTypeSlug
        );

        $platformsKeys = $this->products->extractPlatformsFromProduct($product, $sizes);

        return $this->platforms->filtered(function ($platform) use ($platformsKeys) {
            return in_array($platform['slug'], $platformsKeys, true);
        });
    }

    public function toJSON(): array
    {
        $result = [];

        foreach ($this->marketTypes->getList() as $marketType) {
            $marketTypeSlug = $marketType['slug'] ?? null;

            if (!$marketTypeSlug) {
                continue;
            }

            $accountTypes = $this->accountTypesByMarketType($marketTypeSlug);

            if (empty($accountTypes)) {
                continue;
            }

            $accountTypeData = [];

            foreach ($accountTypes as $accountType) {
                $accountTypeSlug = $accountType['slug'] ?? null;

                if (!$accountTypeSlug) {
                    continue;
                }

                $sizes = $this->accountSizesByMarketTypeAndAccountType(
                    $marketTypeSlug,
                    $accountTypeSlug
                );

                if (empty($sizes)) {
                    continue;
                }

                $accountTypeData[] = [
                    'slug' => $accountType['slug'],
                    'name' => $accountType['name'] ?? '',
                    'sizes' => array_map(function ($size) {
                        return [
                            'slug' => $size['slug'],
                            'name' => '$' . $size['slug'],
                        ];
                    }, $sizes),
                ];
            }

            if (empty($accountTypeData)) {
                continue;
            }

            $result[] = [
                'slug' => $marketType['slug'],
                'name' => $marketType['name'] ?? '',
                'accountTypes' => $accountTypeData,
            ];
        }

        return [
            'marketTypes' => $result,
        ];
    }
}
