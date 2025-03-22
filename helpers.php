<?php
/**
     * Retrieve configuration values from config.php.
     *
     * @param string|null $key The configuration key in dot notation (e.g., 'database.host')
     * @param mixed $default The default value to return if the key is not found
     * @return mixed The configuration value, or the entire config array if no key is provided
     */
    function config($key = null, $default = null)
    {
        static $config = null;

        if ($config === null) {
            $configFile = __DIR__ . '/config.php';
            if (!file_exists($configFile)) {
                throw new Exception("Configuration file not found: $configFile");
            }
            $config = include $configFile;
        }

        if ($key === null) {
            return $config;
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }

/**
 * Function that converts a json to a remote csv file
 *
 * @param String $json Description
 * @param String $filePath Description
 * @return void
 * @throws Exception
 **/
function jsonToCsvRemoteFile(String $json, String $filepath)
{
    try {
        $array = json_decode($json, true);
        $f = fopen($filepath, 'w');
    
        $header = [];

        foreach ($array as $line) {
            $header = array_merge($header, array_keys($line));
        }
    
        $header = array_values(array_unique($header));
        fputcsv($f, $header);
    
        foreach ($array as $line) {
            $rowData = [];
            foreach ($header as $field) {
                $value = $line[$field];
                if (!empty($value)) {
                    if (is_array($value)) {
                        $rowData[] = implode(', ', $value);
                    } else {
                        $rowData[] = $value;
                    }
                } else {
                    //necessary because fputcsv treats false as empty
                    $rowData[] = !is_bool($value) ? "" : "0";
                }
            }
            fputcsv($f, $rowData);
        }
    
        fclose($f);
    } catch (Throwable $th) {
        throw new Exception("Error occurred while parsing the json to csv. Error code XH001");
    }

}

/**
 * Helper function to encode error messages.
 *
 * @param int $httpErrorCode
 * @param mixed $message
 * @return mixed
 **/
function setErrorResponse($httpErrorCode, $message) {
    if(isset($message['message']) && isset($message['custom'][0]['message'])) {
        $message = $message['custom'][0]['message'].' '.$message['custom'][0]['description'];

        header("HTTP/1.1 $httpErrorCode " . $message);
        echo "Status code: {$httpErrorCode}, Message: {$message}";
        die;
    }

    if(isset($message['provider'])) {
        $message = $message['message'];
        header("HTTP/1.1 $httpErrorCode " . $message);
        echo "Status code: {$httpErrorCode}, Message: {$message}";
        die;
    }

    if(isset($message['fault']) && isset($message['fault']['faultstring'])) {
        $message = $message['fault']['faultstring'];
        header("HTTP/1.1 $httpErrorCode " . $message);

        echo "Status code: {$httpErrorCode}, Message: {$message}";
        die;
    }

    if(isset($message['errors'][0]['message'])) {

        $ret = array(
            'error' => $message['errors'][0]['message']
        );

        header("HTTP/1.1 $httpErrorCode " . $ret);
        echo "Status code: {$httpErrorCode}, Message: {$message['errors'][0]['message']}";
        die;
    }

    header("HTTP/1.1 $httpErrorCode " . mb_convert_encoding($message, 'SJIS','UTF-8'));
    echo "Status code: {$httpErrorCode}, Message: {$message}";
    die;
}
function sendErrorResponse($res)
{
    if(isset($res['errors'][0]['message'])) {

        $ret = array(
            'error' => $res['errors'][0]['message']
        );

        header('Content-Type: application/json');
        echo json_encode($ret, JSON_UNESCAPED_SLASHES);
        die;
    }


    if(isset($res['message']) && isset($res['custom'][0]['message'])) {

        $ret = array(
            'error' => $res['custom'][0]['message'].' '.$res['custom'][0]['description']
        );

        header('Content-Type: application/json');
        echo json_encode($ret, JSON_UNESCAPED_SLASHES);
        die;
    }


    header('Content-Type: application/json');
    echo json_encode($res, JSON_UNESCAPED_SLASHES);
    die;
}

/**
 * Function that converts a json to a csv file
 *
 * @param String $json Description
 * @throws Exception
 **/
function jsonToCsv(String $json)
{
    try {
        $array = json_decode($json, true);
    
        $header = [];

        foreach ($array as $line) {
            $header = array_merge($header, array_keys($line));
        }
    
        $header = array_values(array_unique($header));
        $res = [];
        $res[] = implode(',', $header);
        foreach ($array as $line) {
                $res[] = implode(',', str_replace(',', ';', $line));
        }
        return $res;
    } catch (Throwable $th) {
        throw new Exception("Error occurred while parsing the json to csv. Error code XH001");
    }
}

    /**
     * Function that given a key value pair array formats params of an uri  into a string
     *
     * @param array $params
     * @return string
     **/
    function formatParams($params)
    {
        $paramsString = '';
        if (!empty($params)) {
            $paramsString = '';
            foreach ($params as $field => $value) {
                if (!empty($paramsString)) {
                    $paramsString .= '&';
                }
                $paramsString .= "{$field}={$value}";
            }
        }
        return $paramsString;
    }

    /**
     * Function that checks if string follows Kanji nomenclature
     *
     * @param array $params
     * @return string
     **/
    function isKanji($str) {
        return preg_match('/[\x{4E00}-\x{9FBF}]/u', $str) > 0;
    }
    
    function isKatakana($str) {
        return preg_match('/[\x{30A0}-\x{30FF}]/u', $str) > 0;
    }
    
    function isJapanese($str) {
        return isKanji($str) || isKatakana($str);
    }
