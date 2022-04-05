<?php
/*
CSV Parser version 1.0.0
Coded by Kaveh Taher https://github.com/thel0ner
*/
require_once __DIR__ . '/classes/message-processor.class.php';
require_once __DIR__ . '/classes/csv-parser.class.php';
$message_processor = new MessageProcessor(true);
$files = $message_processor->processArgv($argv);
if (is_null($files)) exit();
$parser = new CSVParser($files['filePath'], $files['uniqueCombination']);
$gen = $parser->parser();
foreach ($gen as $response) {
    $message_processor->showCustomMessage($response['type'], $response['message']);
}
