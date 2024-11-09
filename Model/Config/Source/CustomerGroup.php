<?php
/**
 * Coditron
 *
 * @category  Coditron
 * @package   Coditron_SampleProduct
 * @author    Coditron
 * @copyright Copyright (c) Coditron (https://coditron.com)
 */
namespace Coditron\SampleProduct\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\Collection as GroupCollection;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var GroupCollection
     */
    protected $groupCollection;

    /**
     * Constructor
     *
     * @param GroupCollection $groupCollection
     */
    public function __construct(GroupCollection $groupCollection)
    {
        $this->groupCollection = $groupCollection;
    }

    /**
     * Return array of customer groups as options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->groupCollection->toOptionArray() as $group) {
            $options[] = [
                'value' => $group['value'],
                'label' => $group['label']
            ];
        }
        return $options;
    }
}
