<?php

declare(strict_types=1);


namespace Smike\Statement\PullRequestRepository;

final class PullRequestDto
{
    private string $url;

    private string $title;

    private ?string $jiraUrl;

    public function __construct(
        string $url,
        string $title,
        ?string $jiraUrl
    ) {
        $this->url = $url;
        $this->title = $title;
        $this->jiraUrl = $jiraUrl;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getJiraUrl(): ?string
    {
        return $this->jiraUrl;
    }
}
