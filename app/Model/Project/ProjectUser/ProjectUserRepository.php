<?php

namespace App\Model\Project\ProjectUser;


use App\Model\DTO\ProjectUserDTO;
use App\Model\Repository\Base\BaseRepository;
use App\Model\User\UserRepository;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

/**
 * Přístup k datům z tabulky project_user
 */
class ProjectUserRepository extends BaseRepository
{

    public const TABLE_NAME = 'project_user';

    public const COL_ID = 'id';
    public const COL_USER_ID = 'user_id';
    public const COL_PROJECT_ID = 'project_id';


    protected $tableName = self::TABLE_NAME;


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);

    }

    /**
     * Přiřadí uživatele k projektu
     * @param ProjectUserDTO $projectUserDTO
     * @return void
     */
    public function saveUserToProject(ProjectUserDTO $projectUserDTO): void
    {
        $data = [
            self::COL_USER_ID => $projectUserDTO->getUserId(),
            self::COL_PROJECT_ID => $projectUserDTO->getProjectId(),
        ];

        $this->saveFiltered($data);
    }

    /**
     * Vrati pole uzivatelu v roli pracovnik, kteri momentalne nepracuji na danem projektu
     * @param int $projectId
     * @return array pole ve tvaru [id => cele_jmeno]
     */
    public function getAllUsersThatDoesNotWorkOnProject(int $projectId): array
    {
        $users = $this->explorer->query('select user.id, CONCAT_WS(" ", firstname, lastname) as fullName
            from user
            where  user.id not in
            (select user_id
            from project_user
            where project_id = ? )
            AND user.id IN ( select user_id from user_role where user_role.role_id = 1 )

;', $projectId)->fetchPairs('id', 'fullName');

        return $users;

    }

    /**
     * Vrati selekci pro grid, ktera ukazuje vsechny projekty uzivatele
     * @param int $userId
     * @return Selection
     */
    public function getAllUserProjectGridSelection(int $userId): Selection
    {
        $by = [self::COL_USER_ID => $userId];
        return $this->findBy($by);
    }

    /**
     * Vrati pole vsech identifikatoru clenstvi useru na projektech
     * @param array $userIds - pole id uzivatelu ve formatu [id, id1, id2, ...]
     * @return array pole ve formátu [idCl => idCl, idCl1 => idCl1,...]
     */
    public function getAllProjectsMembershipsOfUserIds(array $userIds): array
    {
        $by = [self::COL_USER_ID => $userIds];
        return $this->findBy($by)->select('id')->fetchPairs(self::COL_ID, self::COL_ID);
    }

    /**
     * Vrati vsechny clenstvi v projektu daneho usera s $userId
     * @param int $userId
     * @return array c
     */
    public function getAllProjectMembershipIds(int $userId): array
    {
        return $this->getAllUserProjectGridSelection($userId)
            ->select(self::COL_ID)
            ->fetchAssoc(self::COL_ID);
    }

    /**
     * Vrati selekci pro grid, kde najde vsechny uzivatele(pracovniky) na projektu
     * @param int $projectId
     * @return Selection
     */
    public function getAllUsersOnProjectGridSelection(int $projectId): Selection
    {   $by = [self::COL_PROJECT_ID => $projectId];
        return $this->findBy($by);

    }

    /**
     * Vrati pole id zaznamu, ktere reprezentuji clenstvi useru v konkretnim projektu s projektem $projectId
     * @param int $projectId
     * @return array idCl => idCl
     */
    public function getAllUsersOnProjectIds(int $projectId): array
    {

        return $this->getAllUsersOnProjectGridSelection($projectId)->select(self::COL_ID)->fetchAssoc(self::COL_ID);

    }

    /**
     * Vrati informace o vsech pracovnikach na projektu
     * @param int $projectId
     * @return array
     */
    public function getAllUsersInfoOnProject(int $projectId): array
    {
        $data = $this->getAllUsersOnProjectGridSelection($projectId)
            ->joinWhere(UserRepository::TABLE_NAME, 'user.id = user_id')
            ->select('user.id, CONCAT_WS(" ", firstname, lastname) AS fullName')
            ->fetchPairs('id','fullName');

        return $data;

    }

    /**
     * Overi, jestli uzivatel pracuje na projektu
     * @param int $userId
     * @param int $projectId
     * @return int pokud ano, tak vrati id jeho clenstvi, pokud ne, vraci -1
     */
    public function isUserOnProject(int $userId, int $projectId): int
    {
        $by = [self::COL_USER_ID => $userId, self::COL_PROJECT_ID => $projectId];
        $data = $this->findBy($by)->fetch();
        if($data)
        {
            return $data[self::COL_ID];
        }
        else
        {
            return -1;
        }
    }



}