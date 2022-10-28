<?php

namespace App\Twig;

use Doctrine\SqlFormatter\SqlFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_duration', [$this, 'formatDuration']),
            new TwigFilter('format_sql', [$this, 'formatSQL']),
            new TwigFilter('highlight_sql', [$this, 'highlightSQL']),
        ];
    }

    public function formatDuration(int $seconds): string
    {
        $timeFrom = new \DateTime('@0');
        $timeTo = new \DateTime("@$seconds");

        return $timeFrom->diff($timeTo)->format('%a days %h hours %i minutes %s seconds');
    }

    public function formatSQL(string $query): string
    {
        return (new SqlFormatter())->format($query);
    }
    public function highlightSQL(string $query): string
    {
        return (new SqlFormatter())->highlight($query);
    }
}
