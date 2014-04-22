<?php

namespace Pim\Bundle\DeltaExportBundle\Manager;

use PDO;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Pim\Bundle\CatalogBundle\Model\AbstractProduct;
use Akeneo\Bundle\BatchBundle\Entity\JobInstance;
use Pim\Bundle\CatalogBundle\Repository\ReferableEntityRepositoryInterface;

/**
 * Product export manager to update and create product export entities
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductExportManager
{
    const MAX_FLUSH_COUNT = 500;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $productExportClass;

    /**
     * @var EntityRepository
     */
    protected $productExportRepository;


    /**
     * @var EntityRepository
     */
    protected $productRepository;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager      Entity manager for other entitites
     * @param string        $productExportClass ProductExport class
     */
    public function __construct(
        EntityManager $entityManager,
        $productExportClass,
        $productClass
    ) {
        $this->entityManager           = $entityManager;
        $this->productExportClass      = $productExportClass;
        $this->productExportRepository = $this->entityManager->getRepository($this->productExportClass);
        $this->productRepository       = $this->entityManager->getRepository($productClass);
    }

    public function updateProductExports($productIdentifiers, JobInstance $jobInstance)
    {
        foreach ($productIdentifiers as $productIdentifier) {
            $this->updateProductExport($productIdentifier, $jobInstance);
        }
    }

    public function updateProductExport($identifier, JobInstance $jobInstance)
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $product = $this->productRepository->findByReference((string) $identifier);

        if (null != $product) {
            $productExport = $this->productExportRepository->findOneBy(array(
                'product'     => $product,
                'jobInstance' => $jobInstance
            ));

            $conn = $this->entityManager->getConnection();

            $jobInstance->getId();
            $product->getId();

            if (null === $productExport) {
                $sql = '
                    INSERT INTO pim_delta_product_export
                    (product_id, job_instance_id, date)
                    VALUES (:product_id, :job_instance_id, :date)
                ';
            } else {
                $sql = '
                    UPDATE pim_delta_product_export
                    SET date = :date
                    WHERE product_id = :product_id AND job_instance_id = :job_instance_id
                ';
            }

            $q = $conn->prepare($sql);
            $date = $now->format('Y-m-d H:i:s');
            $productId = $product->getId();
            $jobInstanceId = $jobInstance->getId();

            $q->bindParam(':date', $date, PDO::PARAM_STR);
            $q->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $q->bindParam(':job_instance_id', $jobInstanceId, PDO::PARAM_INT);
            $q->execute();
        }
    }

    public function filterProducts($products, $jobInstance)
    {
        $productsToExport = array();

        foreach ($products as $product) {
            $product = $this->filterProduct($product, $jobInstance);

            if (null !== $product) {
                $productsToExport[] = $product;
            }
        }

        return $productsToExport;
    }

    public function filterProduct(AbstractProduct $product, JobInstance $jobInstance)
    {

        $productExport = $this->productExportRepository->findProductExportAfterEdit(
            $product,
            $jobInstance,
            $product->getUpdated()
        );

        if (0 === count($productExport)) {
            $product = $this->filterProductValues($product);
        } else {
            $product = null;
        }

        return $product;
    }

    public function filterProductValues(AbstractProduct $product)
    {
        $this->entityManager->detach($product);
        $productValues  = $product->getValues();
        $identifierType = $product->getIdentifier()->getAttribute()->getAttributeType();
        foreach ($productValues as $productValue) {
            if ($identifierType != $productValue->getAttribute()->getAttributeType() && (
                    null == $productValue->getUpdated() || (
                        null != $productValue->getUpdated() &&
                        $product->getUpdated()->getTimestamp() - $productValue->getUpdated()->getTimestamp() > 60
                    )
                )
            ) {
                $product->removeValue($productValue);
            } elseif ($productValue->getUpdated()) {
            }
        }

        return $product;
    }
}
