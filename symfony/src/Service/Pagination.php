<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class Pagination
{


    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    public function get(QueryBuilder $query, int $pageNumber, int $limit = 10): PaginationInterface
    {

        return $this->paginator->paginate(
            $query, /* query NOT result */
            $pageNumber/*page number*/,
            $limit/*limit per page*/
        );
    }
}