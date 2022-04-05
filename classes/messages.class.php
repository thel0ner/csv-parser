<?php
require_once __DIR__ . '/messages.enum.php';
/**
 * contents and templates associated with message types :/
 */
class Messages
{
    protected $messages = array(
        'no_args' => "no arguments found!",
        'invalid_args_detected' => "invalid args detected",
        'ask_for_help' => "use --help in order to see list of arguments",
        'help' => "help:" .
            "\n --file : Full address of the csv file (required)" .
            "\n --unique-combinations : The unique combination file (required)" .
            "\n --help : Shows this message",
    );
    protected $addline = false;
    protected $prefixes = array(
        'error' => "[!] Error! ",
        'normal' => "[?] ",
        'input' => "[>] ",
        'success' => "[*] Success: "
    );

    public function getHelpMessage()
    {
        return $this->messages['help'] . ($this->addline ? "\n" : "");
    }
}
