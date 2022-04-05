<?php
require_once __DIR__ . '/csv-file-manager.class.php';
require __DIR__ . '/terminal.class.php';

/**
 * CSV PARSER
 * parses and creates a new CSV filebased on provided CSDV file!!
 */
class CSVParser extends CSVFileManager
{
    private $responseInArry = array(); //collected results from analyzing the target file

    /**
     * checks wether current row is iterated or not.
     */
    private function isAlreadyInArray($arr, $mainArray): bool
    {
        $response = false;
        foreach ($mainArray as $main) {
            if (serialize($main) == serialize($arr)) {
                $response = true;
                break;
            }
        }
        return $response;
    }

    /**
     * looks for duplicated rows through entire CSV file.
     */
    private function lookForDuplicatedArrays()
    {
        $terminal = new Terminal(); // for showing process in terminal
        $totalRows = $this->getCSVFileData(); // currently loaded data of CSV file.
        $carry = array(); //already iterated material
        $counter = 0;
        $counterForTerminal = 0; //another counter just for progress bar
        $response = array(); //gathered responses
        foreach ($totalRows as $inArr) {
            if (!$this->isAlreadyInArray($inArr, $carry)) {
                $temp = $inArr;
                $temp[] = 0;
                $iterator = 0;
                array_push($response, $temp);
                foreach ($totalRows as $row) {
                    if ($row == $inArr) {
                        $response[$counter][array_key_last($response[$counter])] += 1;
                        $carry[] = $inArr;
                        unset($totalRows[$iterator]);
                    }
                    $iterator++;
                }
                $counter++;
            }
            $counterForTerminal++;
            $terminal->show_status($counterForTerminal, count($this->getCSVFileData()));
        }
        $this->responseInArry = $response;
    }

    /**
     * Checks wether required columns -defined in Config - exist in CSV file.
     */
    private function doRequiredColumnsExist(): bool
    {
        $temp = array_map(function ($item) {
            return array_search($item, $this->getCSVHeaders()) !== false;
        }, $this->requiredFileds);
        return array_reduce($temp, function ($prev, $next) {
            return $prev && $next;
        }, true);
    }

    /**
     * prepares headers to put in CSV file
     */
    private function prepareHeaders()
    {
        $tempHeaders = $this->getCSVHeaders();
        $tempHeaders[] = 'count';
        $this->setCSVHeaders($tempHeaders);
    }

    /**
     * initializing...
     */
    public function __construct(string $input_path, string $output_path)
    {
        $this->setCsvFile($input_path);
        $this->setCSVOutPutFilePath($output_path);
    }

    /**
     * and now is the parser!
     */
    public function parser(): iterable
    {
        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'checking if file exists'
        ));
        if (!$this->checkIfFileExists()) {
            yield (array(
                'type' => MessageTypes::Error,
                'message' => 'could not find file'
            ));
        }
        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'checking if type is allowed'
        ));
        if (!$this->checkIfFileIsAllowed()) {
            yield (array(
                'type' => MessageTypes::Error,
                'message' => 'file type is not allowed'
            ));
        }
        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'opening file'
        ));
        $this->openFile();
        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'detecting delimiter'
        ));
        $this->detectDelimiter();
        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'getting headers'
        ));
        $this->detectCSVHeaders();

        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'checking if required columns exist'
        ));
        if (!$this->doRequiredColumnsExist()) {
            yield (array(
                'type' => MessageTypes::Error,
                'message' => 'required fields do not exist'
            ));
        }

        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'reading the file'
        ));
        $this->getCsvContent();
        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'analyzing the file - this may take time'
        ));
        $this->lookForDuplicatedArrays();

        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'preparing analyzed data...'
        ));
        $this->prepareHeaders();

        yield (array(
            'type' => MessageTypes::Normal,
            'message' => 'creating new CSV file'
        ));

        if(!$this->createNewCSVFile($this->getCSVHeaders(), $this->responseInArry)){
            yield (array(
                'type' => MessageTypes::Error,
                'message' => 'could not create demanded CSV file'
            ));
        }

        yield (array(
            'type' => MessageTypes::Success,
            'message' => 'closing file handler'
        ));

        $this->closeFileHandler();
    }
}
