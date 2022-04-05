<?php
require_once __DIR__ . '/config.class.php';

/**
 * FILE MANAGER for CSV parser.
 * Anything related to playing with CSV file -not their data- is located here.
 */
class CSVFileManager extends Config
{
    private $handle; // file handler
    private $csvFile; // path of csv file
    private $outputFile; //path of output file
    private $csvFileData = array(); //data of CSV file
    private $csvHeaders = array(); // headers - or column names - found in the csv file
    private $delimiter; // detected delimiter in CSV file


    /**
     * simply detects the delimiter used in CSV file
     */
    protected function detectDelimiter()
    {
        $firstLine = fgets($this->handle);
        foreach ($this->allowedCSVDelimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        $this->delimiter = array_search(max($this->allowedCSVDelimiters), $this->allowedCSVDelimiters);
    }

    /**
     * collects CSV header - or column names - of the CSV file.
     */
    protected function detectCSVHeaders()
    {
        $fp = fopen($this->csvFile, 'r');
        $this->csvHeaders = fgetcsv($fp, 10000, $this->delimiter, '"');
        fclose($fp);
    }

    /**
     * loads contens of the CSV file in to the RAM for further processes.
     */
    protected function getCsvContent()
    {
        while (($raw_string = fgets($this->handle)) !== false) {
            $row = str_getcsv($raw_string, $this->delimiter);
            $this->csvFileData[] = $row;
        }
    }

    /**
     * creates new CSV file based on required citeria
     * @param $headers an array which encompasses column names of CSV file
     * @param $data the data retrived from CSV file.
     */
    protected function createNewCSVFile($headers, $data): bool
    {
        try {
            $fp = fopen($this->outputFile, 'w');
            fputcsv($fp, $headers);
            foreach ($data as $line) {
                fputcsv($fp, $line, $this->delimiter);
            }
            fclose($fp);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * checks if provided CSV file exist or not
     */
    protected function checkIfFileExists(): bool
    {
        return file_exists($this->csvFile);
    }

    /**
     * checks wether file has a permnited extension or not
     */
    protected function checkIfFileIsAllowed(): bool
    {
        $ext = pathinfo($this->csvFile, PATHINFO_EXTENSION);
        return in_array($ext, $this->allowedTypes);
    }

    /**
     * checks file size!
     */
    protected function checkFileSize(): int
    {
        clearstatcache();
        return filesize($this->csvFile);
    }

    /**
     * open the CSV file and initializes the file handler
     */
    protected function openFile()
    {
        $this->handle = fopen($this->csvFile, "r");
    }

    /**
     * closes file handler when work is finished.
     */
    protected function closeFileHandler()
    {
        fclose($this->handle);
    }

    public function __construct()
    {
        /**
         * setting memory limit based on Configuration class.
         */
        ini_set('memory_limit', $this->requiredRAM);
    }

    /**
     * just a setter for output file
     */
    public function setCSVOutPutFilePath(string $path)
    {
        $this->outputFile = $path;
    }

    /**
     * just a setter for CSV file
     */
    public function setCsvFile(string $path)
    {
        $this->csvFile = $path;
    }

    /**
     * just a setter for CSV file header names
     */
    public function setCSVHeaders($headers)
    {
        $this->csvHeaders = $headers;
    }

    /**
     * just a getter for CSV file's data
     */
    public function getCSVFileData()
    {
        return $this->csvFileData;
    }

    /**
     * just  a getter for CSV file header names
     */
    public function getCSVHeaders()
    {
        return $this->csvHeaders;
    }
}
