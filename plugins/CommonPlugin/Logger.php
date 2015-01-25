<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2012 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */

/**
 * This class extends KLogger to provide configuration through config.php entries.
 * It over-rides the log() method to include the calling class/method/line number
 * 
 */ 
use Psr\Log\LogLevel;

class CommonPlugin_Logger extends Katzgrau\KLogger\Logger
{
    private static $instance;
    private $threshold;
    private $classes;
    private $logLevels = array(
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7,
    );

    /*
     *    Public methods
     */

    /**
     * Creates a configured instance using entries from config.php
     *
     * @param string  $logDirectory File path to the logging directory
     * @param string $severity     One of the pre-defined PSR severity constants
     * @return CommonPlugin_Logger
     */
    static public function instance($logDirectory = false, $severity = false)
    {
        global $log_options;
        global $tmpdir;

        if (isset(self::$instance))
            return self::$instance;

        if ($logDirectory) {
            $dir = $logDirectory;
        } elseif (isset($log_options['dir'])) {
            $dir = $log_options['dir'];
        } elseif (isset($tmpdir)) {
            $dir = $tmpdir;
        } else {
            $dir = '/var/tmp';
        }

        if ($severity) {
            $threshold = $severity;
        } elseif (isset($log_options['level']) && defined($log_options['level'])) {
            $threshold = constant($log_options['level']);
        } else {
            $threshold = LogLevel::EMERGENCY;
        }

        if (isset($_GET['pi'])) {
            $pi = preg_replace('/\W/', '', $_GET['pi']);
            $dir .= '/' . $pi;
        }
        $logger = new self($dir, $threshold);
        $logger->setDateFormat('D d M Y H:i:s');
        self::$instance = $logger;
        return $logger;
    }

    public function __construct($dir, $threshold)
    {
        global $log_options;

        $this->classes = isset($log_options['classes']) ? $log_options['classes'] : array();
        $this->threshold = $threshold;
        parent::__construct($dir, $threshold);
    }

    public function log($level, $message, array $context = array())
    {
        if ($this->logLevels[$this->threshold] < $this->logLevels[$level]) {
            return;
        }

        $trace = debug_backtrace(false);

        if (!empty($this->classes[$trace[1]['class']])) {
            $i = 1;
        } elseif (!empty($this->classes[$trace[2]['class']])) {
            $i = 2;
        } else {
            return;
        }

        $message = 
            "{$trace[$i]['class']}::{$trace[$i]['function']}, line {$trace[$i - 1]['line']} "
            . $message;
        parent::log($level, $message, $context);
    }
}
