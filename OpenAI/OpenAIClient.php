<?php
class OpenAIClient {
    private static $open_ai_key = config('config.openAI.OPEN_AI_KEY');
    private static $open_ai_url = config('config.openAI.OPEN_AI_URL'); //current version of the API endpoint

    /**
     * Function that fetches all the venues
     *
     * @param OpenAIConnector $connector
     * @param string $translatee string to be translated.
     * @return mixed
     * @throws Throwable
     **/
    public static function translateFromText(OpenAIConnector $connector, $translatee)
    {
        $response = $connector->send('POST', 'chat/completions', ['body' => json_encode([
            "model"    => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role"    => "user",
                    "content" => "On the context of a hospitality services for events, translate the following: {$translatee}"
                ]
            ]
        ]),
        'headers' => ["Accept Encoding: UTF-8"]
        ]
        );

        return $response;
    }

    /**
     * Uploads a file to OpenAI (normally a training file for fine tuning an existing model).
     */
    public static function uploadFile($filePath, $purpose='fine-tune') {
        $message = [];
        
        $message['file'] = new CURLFile($filePath);
        $message['purpose'] = $purpose;
        
        $result = self::_sendMessage('/files', $message, 'post', 'multipart/form-data');
        
        return $result;
    }
    
    /**
     * Create a new fine-tuned model, based on an existing model, using an uploaded training file.
     */
    public static function createFineTune($trainingFile, $model = 'gpt-3.5-turbo-1106', $validationFile='') {
        $message = new stdClass();
        $message -> training_file = $trainingFile;
        $message -> model = $model;
        
        if($validationFile) {
            $message -> validation_file = $validationFile;
        }
        
        $result = self::_sendMessage('/fine_tuning/jobs', json_encode($message));
        
        return $result;
    }
    
    /**
     * Get a list of all existing fine-tunes for my account.
     */
    public static function getFineTunes() {
        $result = self::_sendMessage('/fine_tuning/jobs', '', 'get');
        return $result;
    }
    
    /**
     * Doc: https://platform.openai.com/docs/api-reference/chat/create
     * @param array $messages (each item must have "role" and "content" elements, for )
     * @param int $maxTokens maximum tokens for the response in ChatGPT (1000 is the limit for gpt-3.5-turbo)
     * @param string $model valid options are "gpt-3.5-turbo", "gpt-4", and in the future probably "gpt-5"
     * @param int $responseVariants how many response to come up with (normally we just want one)
     * @param float $frequencyPenalty between -2.0 and 2.0, penalize new tokens based on their existing frequency in the answer
     * @param int $presencePenalty between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the conversation so far, increasing the AI's chances to write on new topics.
     * @param int $temperature default is 1, between 0 and 2, higher value makes the model more random in its discussion (going on tangents).
     * @param string $user if you have distinct app users, you can send a user ID here, and OpenAI will look to prevent common abuses or attacks
     */
    public static function chat($messages = [], $maxTokens=100, $model='gpt-3.5-turbo', $responseVariants=1, $frequencyPenalty=0, $presencePenalty=0, $temperature=1, $user='') {
        $message = new stdClass();
        $message -> messages = $messages;
        $message -> model = $model;
        $message -> n = $responseVariants;
        $message -> frequency_penalty = $frequencyPenalty;
        $message -> presence_penalty = $presencePenalty;
        $message -> temperature = $temperature;
        
        if($user) {
            $message -> user = $user;
        }
        
        $result = self::_sendMessage('/chat/completions', json_encode($message));
        
        return $result;
    }
    
    
    private static function _sendMessage($endpoint, $data = '', $method = 'post', $contentType = 'application/json') {
        $apiEndpoint = self::$open_ai_url.$endpoint;
        
        $curl = curl_init();
        
        if($method == 'post') {
            $params = array(
                CURLOPT_URL => $apiEndpoint,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_NOBODY => false,
                CURLOPT_HTTPHEADER => array(
                  "content-type: ".$contentType,
                  "accept: application/json",
                  "authorization: Bearer ".self::$open_ai_key
                )
            );
            curl_setopt_array($curl, $params);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else if($method == 'get') {
            $params = array(
                CURLOPT_URL =>  $apiEndpoint . ($data!=''?('?'.$data):''),
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_NOBODY => false,
                CURLOPT_HTTPHEADER => array(
                  "content-type: ".$contentType,
                  "accept: application/json",
                  "authorization: Bearer ".self::$open_ai_key
                )
            );
            curl_setopt_array($curl, $params);
        }
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        $data = json_decode($response, true);
        if(!is_array($data)) return array();
        
        return $data;
    }
}
