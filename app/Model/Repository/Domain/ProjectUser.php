<?php
namespace App\Model\Repository\Domain;

/**
 * Data value object pro přiřazení uživatele k projektu. Slouží pro snazší předávání dat mezi metodami
 */
class ProjectUser
{

    private int $userId;
    private ?int $projectId;

    public function __construct(int $userId, ?int $projectId)
    {
        $this->userId = $userId;
        $this->projectId = $projectId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return ?int
     */
    public function getProjectId(): ?int
    {
        return $this->projectId;
    }


}