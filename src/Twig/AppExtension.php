<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_duration', [$this, 'formatDuration']),
        ];
    }

    public function formatDuration(int $seconds): string
    {
        $timeFrom = new \DateTime('@0');
        $timeTo = new \DateTime("@$seconds");

        return $timeFrom->diff($timeTo)->format('%a days %h hours %i minutes %s seconds');
    }
}
