<?php
require_once __DIR__ . '/messages.class.php';

/**
 * Message Processor
 * to process args arrived from terminal
 */
class MessageProcessor extends Messages
{
    /**
     * Adds new line at the end of message in terminal
     * @param $msg the message
     */
    private function addNewLine(string $msg): string
    {
        return $msg . ($this->addline ? "\n" : "");
    }

    /**
     * shows message on terminal with required template
     * @param $messageType message type which should be MessageTypes
     * @param $message the message
     */
    private function showMessage(MessageTypes $messageType, $message)
    {
        $msg = $this->addNewLine($message);
        if ($messageType == MessageTypes::Error) {
            $msg = $this->prefixes['error'] . $msg;
        } elseif ($messageType == MessageTypes::Normal) {
            $msg = $this->prefixes['normal'] . $msg;
        } elseif ($messageType == MessageTypes::Input) {
            $msg = $this->prefixes['input'] . $msg;
        } else {
            $msg = $this->prefixes['success'] . $msg;
        }
        return $msg;
    }

    /**
     * finds required element in provided args in terminal by user
     * @param $args arguments arrived from terminal
     * @param $toFind element to look for
     */
    private function findInArgs($args, $toFind): string
    {
        $key = array_search($toFind, $args);
        if ($key == false) {
            return null;
        }
        $toSelect = $key + 1;
        if ($args[$toSelect]) {
            return $args[$toSelect];
        }
        return null;
    }

    /**
     * initializing...
     */
    public function __construct(bool $shouldAddline)
    {
        $this->addline = $shouldAddline;
    }

    /**
     * proccess arguments arrived from Terminal
     */
    public function processArgv($args)
    {
        if (count($args) <= 1) {
            echo $this->showMessage(MessageTypes::Error, $this->messages['no_args']);
            echo $this->showMessage(MessageTypes::Normal, $this->messages['help']);
            return null;
        }

        if (count($args) != 5) {
            if (in_array('--help', $args)) {
                echo $this->showMessage(MessageTypes::Normal, $this->messages['help']);
                return null;
            }
            echo $this->showMessage(MessageTypes::Error, $this->messages['invalid_args_detected']);
            echo $this->showMessage(MessageTypes::Normal, $this->messages['ask_for_help']);
            return null;
        }

        return array(
            'filePath' => $this->findInArgs($args, '--file'),
            'uniqueCombination' => $this->findInArgs($args, '--unique-combinations')
        );
    }

    public function showCustomMessage(MessageTypes $type, string $message)
    {
        echo $this->showMessage($type, $message);
        if ($type == MessageTypes::Error) exit();
    }
}
