<?php

namespace Pim\Bundle\DeltaExportBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Pim\Bundle\CatalogBundle\Model\AbstractProduct;
use Akeneo\Bundle\BatchBundle\Entity\JobInstance;

/**
 * Repository for product export
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductExportRepository extends EntityRepository
{
    /**
     *
     */
    public function findProductExportAfterEdit(AbstractProduct $product, JobInstance $jobInstance, \DateTime $lastUpdate)
    {
        return $this->createQueryBuilder('pe')
            ->where('pe.product = :product')
            ->andWhere('pe.jobInstance = :jobInstance')
            ->andWhere('pe.date > :lastUpdate')
            ->setParameters(array('product' => $product, 'jobInstance' => $jobInstance, 'lastUpdate' => $lastUpdate))
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
