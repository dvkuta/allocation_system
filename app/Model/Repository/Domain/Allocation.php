<?php

namespace App\Model\Repository\Domain;

use App\Model\Project\ProjectUserAllocation\EState;
use DateTime;

/**
 * Reprezentace alokace, pro repository
 */
class Allocation
{
    /**
     * @var int|null je vyplnen, pouze pokud jde o editaci projektu
     */
    private ?int $id;

    /**
     * @var int|null ID clenstvi daneho pracovnika v projektu
     */
    private ?int $userProjectMembershipId;
    /**
     * @var int Alokace v hodinach
     */
    private int $allocation;
    /**
     * @var DateTime musi byt pred $to
     */
    private DateTime $from;

    /**
     * @var DateTime musi byt za $from
     */
    private DateTime $to;
    private string $description;

    /**
     * @var EState Stav alokace. Pouze alokace se stavem active se zapocitava do workloadu pracovníka
     */
    private EState $state;

    /**
     * @var int|null Id projektu, na kterem je alokace vytvorena
     */
    private ?int $currentProjectId;

    /**
     * @var string|null nazev projektu, na kterem je alokace vytvorena
     */
    private ?string $currentProjectName;

    /**
     * @var int|null id pracovnika, pro ktereho je alokace tvorena
     */
    private ?int $currentWorkerId;

    /**
     * @var string|null cele jmeno pracovnika, pro ktereho je alokace tvorena
     */
    private ?string $currentWorkerFullName;

    /**
     * @param int|null $id
     * @param int|null $userProjectMembershipId
     * @param int $allocation
     * @param DateTime $from
     * @param DateTime $to
     * @param string $description
     * @param EState $state
     * @param ?int $currentProjectId
     * @param ?string $currentProjectName
     * @param ?int $currentWorkerId
     * @param ?string $currentWorkerFullName
     */
    public function __construct(
        ?int $id,
        ?int $userProjectMembershipId,
        int $allocation,
        DateTime $from,
        DateTime $to,
        string $description,
        EState $state,
        ?int $currentProjectId = null,
        ?string $currentProjectName = null,
        ?int $currentWorkerId = null,
        ?string $currentWorkerFullName = null,
    )
    {
        $this->id = $id;
        $this->userProjectMembershipId = $userProjectMembershipId;
        $this->allocation = $allocation;
        $this->from = $from;
        $this->to = $to;
        $this->description = $description;
        $this->state = $state;
        $this->currentProjectId = $currentProjectId;
        $this->currentProjectName = $currentProjectName;
        $this->currentWorkerId = $currentWorkerId;
        $this->currentWorkerFullName = $currentWorkerFullName;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getUserProjectMembershipId(): ?int
    {
        return $this->userProjectMembershipId;
    }

    /**
     * @return int
     */
    public function getAllocation(): int
    {
        return $this->allocation;
    }

    /**
     * @return DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @return DateTime
     */
    public function getTo(): DateTime
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return EState
     */
    public function getState(): EState
    {
        return $this->state;
    }

    /**
     * @return int|null
     */
    public function getCurrentProjectId(): ?int
    {
        return $this->currentProjectId;
    }

    /**
     * @return string|null
     */
    public function getCurrentProjectName(): ?string
    {
        return $this->currentProjectName;
    }

    /**
     * @return int|null
     */
    public function getCurrentWorkerId(): ?int
    {
        return $this->currentWorkerId;
    }

    /**
     * @return string|null
     */
    public function getCurrentWorkerFullName(): ?string
    {
        return $this->currentWorkerFullName;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int|null $currentProjectId
     */
    public function setCurrentProjectId(?int $currentProjectId): void
    {
        $this->currentProjectId = $currentProjectId;
    }

    /**
     * @param string|null $currentProjectName
     */
    public function setCurrentProjectName(?string $currentProjectName): void
    {
        $this->currentProjectName = $currentProjectName;
    }

    /**
     * @param int|null $currentWorkerId
     */
    public function setCurrentWorkerId(?int $currentWorkerId): void
    {
        $this->currentWorkerId = $currentWorkerId;
    }

    /**
     * @param string|null $currentWorkerFullName
     */
    public function setCurrentWorkerFullName(?string $currentWorkerFullName): void
    {
        $this->currentWorkerFullName = $currentWorkerFullName;
    }

    /**
     * @param int|null $userProjectMembershipId
     */
    public function setUserProjectMembershipId(?int $userProjectMembershipId): void
    {
        $this->userProjectMembershipId = $userProjectMembershipId;
    }





}