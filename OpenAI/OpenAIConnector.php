<?php

final class OpenAIConnector extends Connector
{
    public function __construct(String $connectionString = null, String $key = null)
    {
        $connectionString = $connectionString ?? config('config.openAI.baseURL');
        $key              = $key ?? config('config.openAI.apiKey');
        parent::__construct($connectionString, $key);
    }

    /**
     * Function that returns a chat completion
     *
     * @return Resource
     **/
    public function completions()
    {
        return new Resource(
            $this,
            "chat/completions"
        );
    }

    /**
     * Function that returns an audio file from the input text.
     *
     * @return Resource
     **/
    public function speech()
    {
        return new Resource(
            $this,
            "audio/speech"
        );
    }

    /**
     * Function that returns a transcription resource
     *
     * @return Resource
     **/
    public function transcriptions()
    {
        return new Resource(
            $this,
            "audio/transcriptions"
        );
    }

    /**
     * Function that translates audio into English.
     *
     * @return Resource
     **/
    public function translations()
    {
        return new Resource(
            $this,
            "audio/translations"
        );
    }

}
