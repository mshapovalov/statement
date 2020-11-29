<?php


use Smike\Statement\PullRequestRepository\PullRequestDto;
use Smike\Statement\PullRequestRepository\PullRequestFilter;
use Smike\Statement\PullRequestRepository\PullRequestRepository;

require __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/../config.php';
if($_GET['secret'] !== $config['secret']){
    return;
}
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');
$author = $_GET['author'];

$formatter = new IntlDateFormatter(
    'pl_PL',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE
);
$formatter->setPattern('LLLL Y');

$date = ucfirst($formatter->format(new DateTime(sprintf('%s-%s-1', $year, $month))));

$filter = new PullRequestFilter($config['company'], $author, $config['repositories']);
$filter->setYear((int)$year)->setMonth((int)$month);
/** @var PullRequestDto[] $pulls */
$pulls = iterator_to_array(PullRequestRepository::create($config['token'])->fetch($filter));
?>

<style>

    table {
        border-collapse: collapse;
    }

    th {
        text-align: left;
    }

    td, th {
        border: 1px solid black;
        padding: 4px;
    }
</style>
<table>
    <tr>
        <th>Raport za miesiąc:</th>
        <td><?= $date ?></td>
    </tr>
    <tr>
        <th>Imię I nazwisko Autora:</th>
        <td><?= $author ?></td>
    </tr>
    <tr>
        <th>Stanowisko Autora:</th>
        <td>Software Engineer</td>
    </tr>
    <tr>
        <th>Imię I nazwisko przełożonego:</th>
        <td>Grzegorz Zawadzki</td>
    </tr>
    <tr>
        <th>Stanowisko przełożonego:</th>
        <td>Director, Head of Information Systems</td>
    </tr>
    <tr>
        <th colspan="5">SPECYFIKACJA STWORZONYCH UTWORÓW</th>
    </tr>
    <tr>
        <th>Lp.</th>
        <th>Tytuł/Nazwa utworu</th>
        <th>Współautor</th>
        <th>Sygnatura /Ścieżka dostępu (PR on GitHub)</th>
        <th>Link do JIRA</th>
    </tr>
    <?php foreach ($pulls as $i => $pull): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= $pull->getTitle() ?></td>
            <td>nie</td>
            <td><a href="<?= $pull->getUrl() ?>"> <?= $pull->getUrl() ?></a></td>
            <td><a href="<?= $pull->getJiraUrl() ?>"><?= $pull->getJiraUrl() ?></a></td>
        </tr>
    <?php endforeach; ?>

</table>
