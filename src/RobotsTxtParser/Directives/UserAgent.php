<?php
namespace vipnytt\RobotsTxtParser\Directives;

use vipnytt\RobotsTxtParser\ObjectTools;
use vipnytt\RobotsTxtParser\RobotsTxtInterface;

/**
 * Class UserAgent
 *
 * @package vipnytt\RobotsTxtParser\Directives
 */
class UserAgent implements DirectiveInterface, RobotsTxtInterface
{
    use ObjectTools;

    /**
     * Sub directives white list
     */
    const SUB_DIRECTIVES = [
        self::DIRECTIVE_ALLOW,
        self::DIRECTIVE_CACHE_DELAY,
        self::DIRECTIVE_CRAWL_DELAY,
        self::DIRECTIVE_DISALLOW,
    ];

    /**
     * Directive
     */
    const DIRECTIVE = 'User-agent';

    /**
     * Current User-agent(s)
     * @var array
     */
    protected $userAgent = [];

    /**
     * All User-agents declared
     * @var array
     */
    protected $userAgents = [];

    /**
     * Rule array
     * @var array
     */
    protected $array = [];

    /**
     * Sub-directive Allow
     * @var array
     */
    protected $allow = [];

    /**
     * Sub-directive Cache-delay
     * @var array
     */
    protected $cacheDelay = [];

    /**
     * Sub-directive Crawl-delay
     * @var array
     */
    protected $crawlDelay = [];

    /**
     * Sub-directive Disallow
     * @var array
     */
    protected $disallow = [];

    /**
     * UserAgent constructor.
     *
     * @param null $parent
     */
    public function __construct($parent = null)
    {
        $this->set();
    }

    /**
     * Set new User-agent
     *
     * @param string $line
     * @param bool $append
     * @return bool
     */
    public function set($line = self::USER_AGENT, $append = false)
    {
        if (!$append) {
            $this->userAgent = [];
        }
        $this->userAgent[] = $line;
        if (!in_array($line, $this->userAgents)) {
            $this->allow[$line] = new DisAllow(self::DIRECTIVE_ALLOW);
            $this->cacheDelay[$line] = new CrawlDelay(self::DIRECTIVE_CACHE_DELAY);
            $this->crawlDelay[$line] = new CrawlDelay(self::DIRECTIVE_CRAWL_DELAY);
            $this->disallow[$line] = new DisAllow(self::DIRECTIVE_DISALLOW);
            $this->userAgents[] = $line;
        }
        return true;
    }

    /**
     * Add
     *
     * @param string $line
     * @return bool
     */
    public function add($line)
    {
        $result = false;
        $pair = $this->generateRulePair($line, self::SUB_DIRECTIVES);
        foreach ($this->userAgent as $userAgent) {
            switch ($pair['directive']) {
                case self::DIRECTIVE_ALLOW:
                    $result = $this->allow[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_CACHE_DELAY:
                    $result = $this->cacheDelay[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_CRAWL_DELAY:
                    $result = $this->crawlDelay[$userAgent]->add($pair['value']);
                    break;
                case self::DIRECTIVE_DISALLOW:
                    $result = $this->disallow[$userAgent]->add($pair['value']);
                    break;
            }
        }
        return isset($result) ? $result : false;
    }

    /**
     * Check
     *
     * @param  string $url - URL to check
     * @param  string $type - directive to check
     * @return bool
     */
    public function check($url, $type)
    {
        $result = ($type === self::DIRECTIVE_ALLOW);
        foreach ([self::DIRECTIVE_DISALLOW, self::DIRECTIVE_ALLOW] as $directive) {
            if ($this->$directive->check($url)) {
                $result = ($type === $directive);
            }
        }
        return $result;
    }

    /**
     * Export
     *
     * @return array
     */
    public function export()
    {
        $result = [];
        foreach ($this->userAgents as $userAgent) {
            $current = $this->allow[$userAgent]->export()
                + $this->cacheDelay[$userAgent]->export()
                + $this->crawlDelay[$userAgent]->export()
                + $this->disallow[$userAgent]->export();
            if (!empty($current)) {
                $result[$userAgent] = $current;
            }
        }
        return empty($result) ? [] : [self::DIRECTIVE => $result];
    }
}