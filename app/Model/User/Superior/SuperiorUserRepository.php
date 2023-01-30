<?php

namespace App\Model\User\Superior;


use App\Model\Repository\Base\BaseRepository;

use App\Model\Repository\Base\ISuperiorUserRepository;
use Nette\Database\Explorer;

/**
 * Přístup k datům z tabulky superior_user
 */
class SuperiorUserRepository extends BaseRepository implements ISuperiorUserRepository
{

    public const TABLE_NAME = 'superior_user';

    public const COL_ID = 'id';
    public const COL_SUPERIOR_ID = 'superior_id';
    public const COL_WORKER_ID = 'worker_id';

    protected $tableName = self::TABLE_NAME;


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);
    }

    /**
     * Vrati vsechny podrizene k nadrizenemu
     * @param int $superiorId
     * @return array ve tvaru [id => workerId(id podrizeneho uzivatele)]
     */
    public function getAllSubordinates(int $superiorId): array
    {
        $by = [self::COL_SUPERIOR_ID => $superiorId];
        return $this->findBy($by)->fetchPairs(self::COL_ID, self::COL_WORKER_ID);
    }

    public function isSuperiorOfWorker(int $superiorId, int $workerId): bool
    {
        $by = [self::COL_SUPERIOR_ID => $superiorId,
            self::COL_WORKER_ID => $workerId];

        $count = $this->findBy($by)->count('id');

        return $count > 0;
    }

    /**
     * ulozi data do db
     * @param int $superiorId
     * @param int $workerId
     * @return void
     */
    public function saveData(int $superiorId, int $workerId): void
    {
        $data = [self::COL_SUPERIOR_ID => $superiorId,
            self::COL_WORKER_ID => $workerId];

        $this->save($data);

    }


}