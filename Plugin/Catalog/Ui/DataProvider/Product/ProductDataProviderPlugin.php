<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Plugin\Catalog\Ui\DataProvider\Product;

use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Framework\Data\Collection;

class ProductDataProviderPlugin
{
    /**
     * Modify the product collection to include products where is_sample = 0 or is_sample is not assigned
     *
     * @param ProductDataProvider $subject
     * @param Collection $collection
     * @return Collection
     */
    public function afterGetCollection(ProductDataProvider $subject, Collection $collection)
    {
        $collection->addAttributeToFilter(
            [
                ['attribute' => 'is_sample', 'null' => true],
                ['attribute' => 'is_sample', 'eq' => 0]
            ]
        );
        return $collection;
    }
}
