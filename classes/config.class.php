<?php
/**
 * Configuration class where you can configue the script
 */
class Config
{
    
    protected $allowedTypes = array("csv"); // define allowed file types here
    
    /**
     * list of allowed delimiters
     * IMPORTANT: the output file will be built with the same delimiter found in target file
     */
    protected $allowedCSVDelimiters = array(
        ';' => 0,
        ',' => 0,
        "\t" => 0,
        "|" => 0
    );

    /**
     * Required fields.
     * if one of the following fields could not be found in header of CSV file,
     * script will throw an error
     */
    protected $requiredFileds = array(
        0 => 'brand_name',
        1 => 'model_name',
    );

    /**
     * based on capacity of your system, define how much RAM the script can use.
     */
    protected $requiredRAM = '8192M';
    
}
