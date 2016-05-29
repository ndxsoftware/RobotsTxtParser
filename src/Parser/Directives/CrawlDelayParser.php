<?php
namespace vipnytt\RobotsTxtParser\Parser\Directives;

use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class CrawlDelayParser
 *
 * @package vipnytt\RobotsTxtParser\Parser\Directives
 */
class CrawlDelayParser implements ParserInterface, RobotsTxtInterface
{
    use DirectiveParserCommons;

    /**
     * Directive alternatives
     */
    const DIRECTIVE = [
        self::DIRECTIVE_CACHE_DELAY,
        self::DIRECTIVE_CRAWL_DELAY,
    ];

    /**
     * Directive
     */
    private $directive = self::DIRECTIVE_CRAWL_DELAY;

    /**
     * Delay
     * @var float|int
     */
    private $value;

    /**
     * CrawlDelay constructor.
     * @param string $directive
     */
    public function __construct($directive = self::DIRECTIVE_CRAWL_DELAY)
    {
        $this->directive = $this->validateDirective($directive, self::DIRECTIVE);
    }

    /**
     * Add
     *
     * @param float|int|string $line
     * @return bool
     */
    public function add($line)
    {
        if (
            !is_numeric($line) ||
            (
                isset($this->value) &&
                $this->value > 0
            )
        ) {
            return false;
        }
        // PHP hack to convert numeric string to float or int
        // http://stackoverflow.com/questions/16606364/php-cast-string-to-either-int-or-float
        $this->value = $line + 0;
        return true;
    }

    /**
     * Export rules
     *
     * @return float[]|int[]|string[]
     */
    public function export()
    {
        return empty($this->value) ? [] : [$this->directive => $this->value];
    }

    /**
     * Render
     *
     * @return string[]
     */
    public function render()
    {
        if (!empty($this->value)) {
            return [$this->directive . ':' . $this->value];
        }
        return [];
    }
}