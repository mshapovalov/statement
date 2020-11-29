<?php

declare(strict_types=1);


namespace Smike\Statement;

use Github\Api\PullRequest;
use Github\Client;
use Smike\Statement\PullRequestRepository\PullRequestFilter;
use Smike\Statement\PullRequestRepository\PullRequestRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommand extends Command
{
    public const NAME = 'generate';
    public const ARG_YEAR = 'year';
    public const ARG_MONTH = 'month';

    private string $author;
    private string $token;
    private string $company;
    private array $repositories;
    private Client $client;
    private PullRequest $pullsApi;

    public function __construct(string $company, array $repositories, string $author, string $token)
    {
        parent::__construct(self::NAME);

        $this->company = $company;
        $this->repositories = $repositories;
        $this->author = $author;
        $this->token = $token;
        $this->client = $client = new Client();
        $this->client->authenticate($this->token, null, Client::AUTH_ACCESS_TOKEN);
        $this->pullsApi = $this->client->api('pulls');
    }

    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Generates statement')
            ->addArgument(self::ARG_MONTH, InputArgument::OPTIONAL, '', date('m'))
            ->addArgument(self::ARG_YEAR, InputArgument::OPTIONAL, '', date('Y'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $year = (int)$input->getArgument(self::ARG_YEAR);
        $month = (int)$input->getArgument(self::ARG_MONTH);
        $filter = new PullRequestFilter($this->company, $this->author, $this->repositories);
        $filter->setYear($year)->setMonth($month);
        $pulls = iterator_to_array(PullRequestRepository::create($this->token)->fetch($filter));
//        $startDate = new \DateTimeImmutable(sprintf('%s-%s-01 00:00:00', $year, $month));
//        $endDate = new \DateTimeImmutable(date("Y-m-t H:i:s", $startDate->getTimestamp()));
//        $result = [];
//        foreach ($this->repositories as $repository) {
//            foreach ($this->getPulls($repository, $startDate, $endDate) as $pull) {
//                if($pull['user']['login'] !== $this->author){
//                    continue;
//                }
//                $result[] = [
//                    'title' => $pull['title'],
//                    'url' => $pull['html_url'],
//                    'jira_url' => $this->extractJiraUrl($pull['title']),
//                    'created_at' => $pull['created_at'],
//                    'merged_at' => $pull['merged_at'],
//                    'closed_at' => $pull['closed_at'],
//                    'author' => $pull['user']['login']
//                ];
//            }
//        }
//
//        print_r($result);
        return 0;
    }

    private function extractJiraUrl($title): ?string
    {
        $arr = explode(']', $title);
        if (empty($arr)) {
            return null;
        }
        return 'https://ifxtech.atlassian.net/browse/' . substr($arr[0], 1);
    }

    private function getPulls(string $repository, \DateTimeImmutable $startDate, \DateTimeImmutable $endDate): \Iterator
    {
        $page = 1;
        while (true) {
            $pulls = $this->getPullsPage($repository, $page);
            if (empty($pulls)) {
                break;
            }
            foreach ($pulls as $pr) {
                $date = new \DateTimeImmutable($pr['merged_at'] ?? $pr['created_at'] ?? $pr['closed_at']);
                if ($date < $startDate) {
                    break 2;
                }
                if ($date > $endDate) {
                    continue;
                }
                yield $pr;
            }
            $page++;
        }
    }

    private function getPullsPage(string $repository, int $page, int $pageSize = 100): array
    {
        return $this->pullsApi->all(
            $this->company,
            $repository, [
            'sort' => 'created',
            'state' => 'all',
            'page' => $page,
            'per_page' => $pageSize,
            'direction' => 'desc'
        ]);
    }
}
