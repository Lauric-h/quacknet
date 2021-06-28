<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MessageExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            'auto_link' => new TwigFilter('auto_link', [$this, 'autoLink']),
        ];
    }

    public function autolink(string $string): string {
        $regexp = '/(<a.*?>)?(https?)?(:\/\/)?(\w+\.)?(\w+)\.([\w\/\-_.~&=?]+)(<\/a>)?/i';
        $anchor = '<a href="%s://%s" target="_blank">%s</a>';

        preg_match_all($regexp, $string, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (empty($match[1]) && empty($match[7])) {
                $protocol = $match[2] ? $match[2] : 'http';
                $replace = sprintf($anchor, $protocol, $match[0], $match[0]);
                $string = str_replace($match[0], $replace, $string);
            }
        }

        return $string;
    }
}