<?php

namespace App\Model\Repository\Base;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\SmartObject;
use Traversable;

/**
 * Predek pro vsechny repository, ktere pracuji s Nette database
 */
class BaseRepository
{
    use SmartObject;

    /** @var string Table name - melo by byt nastaveno v kazdem repository! */
    protected $tableName;

    /** @var Explorer */
    protected $explorer;

    public function __construct(\Nette\Database\Explorer $explorer)
    {
        $this->explorer = $explorer;

    }

    /**
     * Vraci nazev tabulky
     * @return string
     */
    public function getTableName(): string
    {
        if (empty($this->tableName))
        {
            // název tabulky odvodíme z názvu třídy
            preg_match('#(\w+)Repository$#', get_class($this), $m);

            $this->tableName = strtolower($m[1]);
        }

        return $this->tableName;
    }

    /**
     * Vrací objekt reprezentující databázovou tabulku.
     * @return Selection
     */
    protected function getTable()
    {
        if (empty($this->tableName))
        {
            $this->getTableName();
        }

        return $this->explorer->table($this->tableName);
    }

    /**
     * Vrací všechny řádky z tabulky.
     * @return Selection
     */
    public function findAll(): Selection
    {
        return $this->getTable();
    }

    /**
     * Vrací řádky podle filtru, např. array('name' => 'John').
     * @param array $by
     * @return Selection
     */
    public function findBy(array $by): Selection
    {
        return $this->getTable()->where($by);
    }

    /**
     * Vrací záznamu podle INT primary key
     * @param mixed $id
     * @return ActiveRow|null
     */
    public function findRow($id): ?ActiveRow
    {
        return $this->getTable()->get((is_numeric($id) ? (int) $id : $id));
    }

    /**
     * Vkládá data do tabulky
     * @param array|Traversable|Selection $data
     * @return ActiveRow|int|bool
     */
    public function insert(iterable $data): bool|ActiveRow|int
    {
        return $this->getTable()->insert($data);
    }

    /**
     * Vymaže záznam podle primárního klíče
     * @param int|string $id
     * @return int
     */
    public function delete($id): ?int
    {
        $prim = $this->getTable()->getPrimary();

        if (is_string($prim))
        {
            $rows = $this->findBy(array($prim => (is_numeric($id) ? (int)$id : $id)));
            return $rows->delete();
        }

        return null;
    }

    /**
     * Ulozi nebo updatne zaznam
     * @param array|Traversable|Selection $data
     * @param mixed|null $id
     * @return ActiveRow|bool|int|null
     */
    public function save(iterable $data, $id = null)
    {
        if (null === $id)
        {
            $record = $this->insert($data);
        }
        else
        {
            $id = (is_numeric($id) ? (int) $id : $id);
            $record = $this->findRow($id);
            if ($record)
            {
                $record->update($data);
            }
        }

        return $record;
    }

    /**
     * @param string|null $tableName
     * @return array
     */
    public function getColumns($tableName = null): array
    {
        if (!$tableName)
        {
            $tableName = $this->getTableName();
        }

        $columns = $this->explorer->getConnection()->getDriver()->getColumns($tableName);

        $columnsResult = array();

        foreach ($columns as $column)
        {
            $columnsResult[] = $column['name'];
        }

        return $columnsResult;
    }

    /**
     * Vrati odfiltrovana data tak, ze obsahuji indexy jen existujicich sloupcu v tabulce.
     * @param array|Traversable|Selection $data
     * @return mixed
     */
    protected function getFilteredData(iterable $data)
    {
        $columns = $this->getColumns();

        foreach ($data as $key => $value)
        {
            if (!in_array($key, $columns))
            {
                unset($data[$key]); // @phpstan-ignore-line
            }
        }

        return $data;
    }

    /**
     * @param array|Traversable|Selection $data
     * @param int|null $id
     * @return int|ActiveRow|bool|array|Selection|null
     */
    public function saveFiltered(iterable $data, ?int $id = null): int|ActiveRow|bool|array|Selection|null
    {
        $data = (array) $data;
        $data = $this->getFilteredData($data);

        return $this->save($data, $id);
    }
}
