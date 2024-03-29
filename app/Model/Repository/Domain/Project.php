<?php

namespace App\Model\Repository\Domain;

use DateTime;

/**
 * Reprezentace projektu, pro repository
 */
class Project
{

    /**
     * @var int|null je vyplnen, pouze pokud jde o editaci projektu
     */
    private ?int $id;
    private string $name;
    private int $project_manager_id;
    private string $project_manager_name;
    /**
     * @var DateTime musi byt pred $to
     */
    private DateTime $from;
    /**
     * @var DateTime|null je null, pokud projekt nikdy nekonci, jinak musi byt za $from
     */
    private ?DateTime $to;
    private string $description;

    /**
     * @param int|null $id
     * @param string $name
     * @param int $project_manager_id
     * @param string $project_manager_name
     * @param DateTime $from
     * @param DateTime|null $to
     * @param string $description
     */
    public function __construct(
        ?int $id,
        string $name,
        int $project_manager_id,
        string $project_manager_name,
        DateTime $from,
        ?DateTime $to,
        string $description
    )
    {

        $this->id = $id;
        $this->name = $name;
        $this->project_manager_id = $project_manager_id;
        $this->project_manager_name = $project_manager_name;
        $this->from = $from;
        $this->to = $to;
        $this->description = $description;
    }

    /**
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getProjectManagerId(): int
    {
        return $this->project_manager_id;
    }

    /**
     * @return string
     */
    public function getProjectManagerName(): string
    {
        return $this->project_manager_name;
    }

    /**
     * @return DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @return DateTime|null
     */
    public function getTo(): ?DateTime
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

}