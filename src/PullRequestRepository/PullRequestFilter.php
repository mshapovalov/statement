<?php

declare(strict_types=1);


namespace Smike\Statement\PullRequestRepository;

final class PullRequestFilter
{
    /** @var array  */
    private array $repositories;

    private string $company;

    private string $author;

    private ?int $month = null;

    private ?int $year = null;

    public function __construct(string $company, string $author, array $repositories)
    {
        $this->repositories = $repositories;
        $this->company = $company;
        $this->author = $author;
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function setRepositories(array $repositories): self
    {
        $this->repositories = $repositories;
        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): self
    {
        $this->month = $month;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;
        return $this;
    }
}
