<?php

namespace App\Model\Repository\Base;

use Nette\Database\Table\ActiveRow;

trait NotDeletedTraitRepository
{
    /**
     * @return \Nette\Database\Table\Selection
     */
    public function findAll()
    {
        $rows = parent::findAll();
        $rows->where("`{$this->getTableName()}`.`not_deleted` = 1");
        return $rows;
    }

    /**
     * @param array<mixed> $by
     * @return \Nette\Database\Table\Selection
     */
    public function findBy(array $by)
    {
        $by["`{$this->getTableName()}`.`not_deleted`"] = 1;
        return parent::findBy($by);
    }

    /**
     * @param mixed $id
     * @return ActiveRow|null
     */
    public function findRow($id)
    {
        $row = parent::findRow($id);
        return (empty($row->not_deleted)) ? null : $row;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->findAll()->where('not_deleted = 1')->count("*");
    }

    /**
     * "Smazani" zaznamu.
     * @param int $id
     * @return bool
     */
    public function setNotDeletedNull(int $id): bool
    {
        $row = $this->findRow($id);

        if ($row)
        {
            $row->update(array("not_deleted" => null));
            return true;
        }

        return false;
    }
}
