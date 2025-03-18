<?php

namespace App\Repository;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class BaseDocumentRepository extends ServiceDocumentRepository
{
    protected DocumentManager $documentManager;

    public function __construct(ManagerRegistry $managerRegistry, DocumentManager $documentManager)
    {
        parent::__construct($managerRegistry, $this->documentName);
        $this->documentManager = $documentManager;
    }
}
