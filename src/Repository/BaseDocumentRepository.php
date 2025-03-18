<?php

namespace App\Repository;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class BaseDocumentRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $managerRegistry, protected readonly DocumentManager $documentManager)
    {
        parent::__construct($managerRegistry, $this->documentName);
    }
}
