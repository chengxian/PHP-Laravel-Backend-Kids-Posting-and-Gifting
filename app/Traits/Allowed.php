<?php
/**
 * @author: chengxian
 * Date: 5/1/16
 * @copyright Cheng Xian Lim
 */


namespace App\Traits;


use App\Exceptions\MustCheckAllowed;
use Exception;

trait Allowed
{
    /** @var int  */
    protected $countAllowedChecks = 0;

    /** @var \Exception[] */
    protected $disallowedExceptions = [];

    protected function checkAllowed() {

        // Calling class must check allowability before calling certain methods
        if ($this->countAllowedChecks == 0) {
            $callingMethod = debug_backtrace()[1]['function'];
            $className = get_class($this);
            throw new MustCheckAllowed("Calling $callingMethod on $className must have availability checked first");
        }

        // Assume checked appropriatly. Reset count
        $this->countAllowedChecks = 0;
        return true;
    }

    /**
     * @param Exception $e
     */
    protected function addDisallowedException(Exception $e)
    {
        $this->disallowedExceptions []= $e;
    }

    /**
     * @param \Exception[] $exceptions
     */
    protected function addDisallowedExceptions($exceptions)
    {
        $this->disallowedExceptions = array_merge($this->disallowedExceptions, $exceptions);
    }

    /**
     * @return \Exception[]
     */
    public function getDisallowedExceptions()
    {
        return $this->disallowedExceptions;
    }

    /**
     * @return string[]
     */
    public function getDisallowedMessages()
    {
        $messages = [];
        foreach ($this->disallowedExceptions as $e) {
            $messages []= $e->getMessage();
        }

        return $messages;
    }

    /**
     * @return string
     */
    public function getDisallowedMessagesAsString()
    {
        $messages = $this->getDisallowedMessages();

        return implode("\n", $messages);
    }
}