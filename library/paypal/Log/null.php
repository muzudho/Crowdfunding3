<?php
/**
 * $Header: /home/ppcvs/paypal_php_sdk/Log/null.php,v 1.1 2006/02/19 08:22:29 dennis Exp $
 *
 * @version $Revision: 1.1 $
 * @package Log
 */

/**
 * The Log_null class is a concrete implementation of the Log:: abstract
 * class.  It simply consumes log events.
 *
 * @author  Jon Parise <jon@php.net>
 * @since   Log 1.8.2
 * @package Log
 *
 * @example null.php    Using the null handler.
 */
class Log_null extends Log
{
    /**
     * Constructs a new Log_null object.
     *
     * @param string $name     Ignored.
     * @param string $ident    The identity string.
     * @param array  $conf     The configuration array.
     * @param int    $level    Log messages up to and including this level.
     * @access public
     */
    function Log_null($name, $ident = '', $conf = array(),
					  $level = P_LOG_DEBUG)
    {
        $this->_id = md5(microtime());
        $this->_ident = $ident;
        $this->_mask = Log::UPTO($level);
    }

    /**
     * Simply consumes the log event.  The message will still be passed
     * along to any Log_observer instances that are observing this Log.
     *
     * @param mixed  $message    String or object containing the message to log.
     * @param string $priority The priority of the message.  Valid
     *                  values are:   P_LOG_EMERG,     P_LOG_ALERT,
     *                  P_LOG_CRIT  , P_LOG_ERR  ,     P_LOG_WARNING,
     *                  P_LOG_NOTICE, P_LOG_INFO , and P_LOG_DEBUG.
     * @return boolean  True on success or false on failure.
     * @access public
     */
    function log($message, $priority = null)
    {
        /* If a priority hasn't been specified, use the default value. */
        if ($priority === null) {
            $priority = $this->_priority;
        }

        /* Abort early if the priority is above the maximum logging level. */
        if (!$this->_isMasked($priority)) {
            return false;
        }

        $this->_announce(array('priority' => $priority, 'message' => $message));

        return true;
    }

}
