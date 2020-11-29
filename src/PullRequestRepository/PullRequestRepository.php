<?php

declare(strict_types=1);


namespace Smike\Statement\PullRequestRepository;

use Github\Api\PullRequest;
use Github\Client;
use Iterator;

final class PullRequestRepository
{
    private PullRequest $pullRequestApi;

    private function __construct(PullRequest $pullRequestApi)
    {
        $this->pullRequestApi = $pullRequestApi;
    }

    public static function create(string $token): self
    {
        $client = new Client();
        $client->authenticate($token, null, Client::AUTH_ACCESS_TOKEN);
        return new self($client->api('pulls'));
    }

    /**
     * @return PullRequestDto[]|Iterator
     */
    public function fetch(PullRequestFilter $filter): Iterator
    {
        $company = $filter->getCompany();
        $repositories = $filter->getRepositories();
        $year = $filter->getYear() ?? (int)date('Y');
        $month = $filter->getMonth() ?? (int)date('m');
        $startDate = $this->createStartDate($year, $month);
        $endDate = $this->createEndDate($startDate);
        $author = $filter->getAuthor();

        foreach ($repositories as $repository) {
            foreach ($this->fetchFromRepository($company, $repository, $author, $startDate, $endDate) as $pull) {
                yield new PullRequestDto($pull['html_url'], $pull['title'], $this->createJiraUrl($pull['title']));
            }
        }
    }

    private function createJiraUrl(string $title): ?string
    {
        $title = explode(' ', $title)[0];
        $title = explode(']', $title)[0];
        $title = str_replace(['[', ']'], '', $title);
        if (!preg_match('/\\d/', $title)) {
            return null;
        }
        return 'https://ifxtech.atlassian.net/browse/' . $title;
    }

    private function fetchFromRepository(
        string $company,
        string $repository,
        string $author,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate
    ): Iterator {
        $page = 1;
        while ($pulls = $this->fetchPage($company, $repository, $page)) {
            if (empty($pulls)) {
                break;
            }
            foreach ($pulls as $pull) {
                $date = new \DateTimeImmutable($pull['merged_at'] ?? $pull['created_at'] ?? $pull['closed_at']);
                if ($date < $startDate) {
                    break 2;
                }
                if ($date > $endDate) {
                    continue;
                }
                if ($pull['user']['login'] !== $author) {
                    continue;
                }
                yield $pull;
            }
            $page++;
        }
    }

    private function fetchPage(string $company, string $repository, int $page, int $pageSize = 100): array
    {
        return $this->pullRequestApi->all(
            $company,
            $repository, [
            'sort' => 'created',
            'state' => 'all',
            'page' => $page,
            'per_page' => $pageSize,
            'direction' => 'desc'
        ]);
    }

    private function createStartDate(int $year, int $month): \DateTimeImmutable
    {
        return new \DateTimeImmutable(sprintf('%s-%s-01 00:00:00', $year, $month));
    }

    private function createEndDate(\DateTimeImmutable $startDate): \DateTimeImmutable
    {
        return new \DateTimeImmutable(date("Y-m-t H:i:s", $startDate->getTimestamp()));
    }

}
