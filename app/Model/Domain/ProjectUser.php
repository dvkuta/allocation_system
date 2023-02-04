<?php
namespace App\Model\Domain;

use App\Model\DTO\ProjectUserDTO;

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

    public function createProjectUser(ProjectUserDTO $projectUserDTO)
    {
        return new ProjectUser($projectUserDTO->getUserId(), $projectUserDTO->getProjectId());
    }

    public function toDTO()
    {
        return new ProjectUserDTO($this->getUserId(), $this->getProjectId());
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