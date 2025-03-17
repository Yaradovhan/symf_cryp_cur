<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Throwable;

class BaseDocumentRepository extends ServiceDocumentRepository
{
    protected DocumentManager $documentManager;

    public function __construct(ManagerRegistry $managerRegistry, DocumentManager $documentManager)
    {
        parent::__construct($managerRegistry, $this->documentName);
        $this->documentManager = $documentManager;
    }

    public function loadBy(array $fields)
    {
        try{
            $qB = $this->getDocumentManager()->createQueryBuilder($this->documentName);

            foreach ($fields as $field => $value) {
                $qB->field($field)->equals($value);
            }

            $document = $qB->getQuery()->execute()->current();
        } catch (Throwable $e) {
            $document = null;
        }

        return is_object($document) ? $document : null;
    }

}
