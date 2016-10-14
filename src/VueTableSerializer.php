<?php

namespace DepSimon\FractalVueTableSerializer;

use InvalidArgumentException;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer;

class VueTableSerializer extends ArraySerializer
{

    /**
     * Serialize the paginator.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = (int) $paginator->getCurrentPage();
        $lastPage = (int) $paginator->getLastPage();
        $perPage = (int) $paginator->getPerPage();
        $count = (int) $paginator->getCount();
        $firstItem = $count === 0 ? 0 : ($currentPage - 1) * $perPage + 1;
        $lastItem = $count === 0 ? 0 : $firstItem + $count - 1;

        $pagination = [
            'from' => $firstItem,
            'to' => $lastItem,
            'total' => (int) $paginator->getTotal(),
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'per_page' => $perPage
        ];

        return ['pagination' => $pagination];
    }
}
