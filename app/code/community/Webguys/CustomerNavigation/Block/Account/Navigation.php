<?php 

class Webguys_CustomerNavigation_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
    /** @var array */
    protected $configuredLinks = array(
        'account',
        'account_edit',
        'address_book',
        'orders',
        'billing_agreements',
        'recurring_profiles',
        'reviews',
        'tags',
        'newsletter',
        'oauth_customer_tokens',
        'downloadable_products',
        'wishlist'
    );

    /**
     * @inheritDoc
     */
	public function getLinks()
    {
    	$preLinks = $this->_links;
    	$tmpLinks = array();
		$this->_links = array();

        $order = 150;
		foreach ($preLinks as $link) {
            $isConfiguredLink = $this->isConfiguredLink($link);
            // Add any core links configured to show in the module
			if ($isConfiguredLink && $this->isConfiguredToShow($link)) {
                $position = $this->getConfigPosition($link);
				$tmpLinks[$position] = $link;
			}
            // Add any custom links added via layout XML
            if (!$isConfiguredLink) {
                $tmpLinks[$order] = $link;
                $order += 10;
            }
		}

        // Sort the new list
		ksort($tmpLinks);

		foreach ($tmpLinks as $position => $link) {
            $isConfiguredLink = $this->isConfiguredLink($link);
            // Add any core links configured to show in the module
			if ($isConfiguredLink) {
				$this->addLink($link->getName(), $link->getPath(), $link->getLabel());
			}
            // Add any custom links added via layout XML
            else {
                $this->addLink($link->getName(), $link->getPath(), $link->getLabel());
            }
		}

		return $this->_links;
    }

    /**
     * Determine if the given link is configured to be shown.
     *
     * @param Varien_Object $link
     *
     * @return mixed
     */
    protected function isConfiguredToShow(Varien_Object $link)
    {
        return Mage::getStoreConfig(
            sprintf('customernavigation/settings/show_%s', $this->getFormattedName($link))
        );
    }

    /**
     * Get the configured position for the given link.
     *
     * @param Varien_Object $link
     *
     * @return mixed
     */
    protected function getConfigPosition(Varien_Object $link)
    {
        return Mage::getStoreConfig(
            sprintf('customernavigation/reorder/position_%s', $this->getFormattedName($link))
        );
    }

    /**
     * Determine if the given link is a "configured" link.
     *
     * @param Varien_Object $link
     *
     * @return bool
     */
    protected function isConfiguredLink(Varien_Object $link)
    {
        $needle = $this->getFormattedName($link);
        return (in_array($needle, $this->configuredLinks));
    }

    /**
     * Get the formatted name e.g. my_link_name.
     *
     * @param Varien_Object $link
     *
     * @return mixed
     */
    protected function getFormattedName(Varien_Object $link)
    {
        return str_replace(' ', '_', strtolower($link->getName()));
    }
}