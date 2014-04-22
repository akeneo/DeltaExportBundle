<?php

namespace Pim\Bundle\DeltaExportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductExport
 */
class ProductExport
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var \Pim\Bundle\CatalogBundle\Model\Product
     */
    private $product;

    /**
     * @var \Akeneo\Bundle\BatchBundle\Entity\JobInstance
     */
    private $jobInstance;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ProductExport
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set product
     *
     * @param \Pim\Bundle\CatalogBundle\Model\Product $product
     *
     * @return ProductExport
     */
    public function setProduct(\Pim\Bundle\CatalogBundle\Model\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Pim\Bundle\CatalogBundle\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set jobInstance
     *
     * @param \Akeneo\Bundle\BatchBundle\Entity\JobInstance $jobInstance
     *
     * @return ProductExport
     */
    public function setJobInstance(\Akeneo\Bundle\BatchBundle\Entity\JobInstance $jobInstance = null)
    {
        $this->jobInstance = $jobInstance;

        return $this;
    }

    /**
     * Get jobInstance
     *
     * @return \Akeneo\Bundle\BatchBundle\Entity\JobInstance
     */
    public function getJobInstance()
    {
        return $this->jobInstance;
    }
}
