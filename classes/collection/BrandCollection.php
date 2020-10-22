<?php namespace Lovata\Shopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\Shopaholic\Classes\Item\BrandItem;
use Lovata\Shopaholic\Classes\Store\BrandListStore;
use Lovata\Shopaholic\Classes\Item\CategoryItem;

/**
 * Class BrandCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * Search for Shopaholic, Sphinx for Shopaholic
 * @method $this search(string $sSearch)
 */
class BrandCollection extends ElementCollection
{
    const ITEM_CLASS = BrandItem::class;

    /**
     * Apply sorting
     * @return $this
     */
    public function sort()
    {
        //Get sorting list
        $arResultIDList = BrandListStore::instance()->sorting->get();

        return $this->applySorting($arResultIDList);
    }

    /**
     * Apply filter by active brand list
     * @return $this
     */
    public function active()
    {
        $arResultIDList = BrandListStore::instance()->active->get();

        return $this->intersect($arResultIDList);
    }

    /**
     * Filter brand list by category ID
     * @param int $iCategoryID
     * @return $this
     */
    public function category($iCategoryID)
    {
        $obCategoryItem = CategoryItem::make($iCategoryID);
        $arResultIDList = BrandListStore::instance()->category->get($iCategoryID);

        if ($obCategoryItem->children->isNotEmpty()) {
            foreach ($obCategoryItem->children as $obChildCategoryItem) {
                $arResultIDList = array_merge($arResultIDList, BrandListStore::instance()->category->get($obChildCategoryItem->id));
                $arResultIDList = array_merge($arResultIDList, (array) $this->getBrandIDList($obChildCategoryItem->id));
            }
        }
        return $this->intersect($arResultIDList);
    }

    /**
     * Get brand ID list for children categories
     * @param int $iCategoryID
     * @return array
     */
    protected function getBrandIDList($iCategoryID)
    {
        $obCategoryItem = CategoryItem::make($iCategoryID);
        $arResultIDList = [];
        
        if ($obCategoryItem->children->isNotEmpty()) {
            foreach ($obCategoryItem->children as $obChildCategoryItem) {
                $arResultIDList = array_merge($arResultIDList, BrandListStore::instance()->category->get($obChildCategoryItem->id));
                $arResultIDList = array_merge($arResultIDList, (array) $this->getBrandIDList($obChildCategoryItem->id));
            }
        }
        return $arResultIDList;
    }

}
