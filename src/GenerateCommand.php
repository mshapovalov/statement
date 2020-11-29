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
        return 0;
    }
}
