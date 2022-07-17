<?php
declare(strict_types=1);

namespace App\Service\Manager;

use App\Form\FormHandler;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use function array_key_exists;

abstract class AbstractManager
{

    public const ITEMS_IN_PAGE = 10;

    protected EntityManagerInterface $em;
    protected PaginatorInterface     $paginator;
    protected FormHandler            $formHandler;

    /**
     * @required
     */
    public function init(
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        FormHandler $formHandler
    ) {
        $this->em          = $em;
        $this->paginator   = $paginator;
        $this->formHandler = $formHandler;
    }

    protected function getConnection(): Connection
    {
        return $this->em->getConnection();
    }


    public function query(array $filter = []): array
    {
        $sql    = '';
        $params = [];

        return [$sql, $params];
    }

    public function all(array $filter = []): array
    {
        [$sql, $params] = $this->query($filter);

        $stmt = $this->getConnection()->prepare($sql);

        return $stmt->executeQuery($params)->fetchAllAssociativeIndexed();
    }

    public function choices($fields, array $filter = []): array
    {
        $all     = $this->all($filter);
        $choices = [];
        foreach ($all as $item) {
            if (is_array($fields)) {
                $options = [];
                foreach ($fields as $field) {
                    $options[] = $item[$field] ?? null;
                }

                $option           = $options ? implode(' ', $options) : $item['id'];
                $choices[$option] = $item['id'];
                continue;
            }

            $option           = $item[$fields] ?? $item['id'];
            $choices[$option] = $item['id'];
        }

        return $choices;
    }

    public function paginate(array $filter = []): PaginationInterface
    {
        $p = array_key_exists('p', $filter) ? (int)$filter['p'] : 1;
        [$sql, $params] = $this->query($filter);

        $stmt = $this->getConnection()->prepare($sql);
        $rows = $stmt->executeQuery($params)->fetchAllAssociativeIndexed();

        return $this->paginator->paginate($rows, $p, self::ITEMS_IN_PAGE);
    }

    abstract public function create(array $data): int;

    abstract public function update(int $id, array $data): int;

    public function get(int $id): ?array
    {
        return null;
    }

    public function delete(int $id): void
    {
    }

    public function form(string $type, array $data = [], array $options = []): FormInterface
    {
        return $this->formHandler->createForm($type, $data, $options);
    }

    public function handleForm(FormInterface $form, Request $request): array
    {
        return $this->formHandler->handleForm($form, $request);
    }


}
