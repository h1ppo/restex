<?php

namespace H1ppo\Controller;

class Index extends AbstractRest
{
    public function read()
    {

    }

    /**
     * An array of content types currently supported by this controller.
     * @return array | null
     */
    protected function getValidContentTypes()
    {
        return [
            'application/vnd.com.myservice.v3+json' => 'application/vnd.com.myservice.v3+json',
            'application/vnd.com.myservice.v2+json' => 'application/vnd.com.myservice.v2+json',
            'application/json' => 'application/json',
        ];
    }

    /**
     * An array of content types which will be deprecated soon.
     * @return array | null
     */
    protected function getToBedeprecatedContentTypes()
    {
        return [
            'application/vnd.com.myservice.v2+json' => 'application/vnd.com.myservice.v2+json',
        ];
    }

    /**
     * An array of content type which have been deprecated.
     * @return array | null
     */
    protected function getDeprecatedContentTypes()
    {
        return [
            'application/vnd.com.myservice.v1+json' => 'application/vnd.com.myservice.v1+json',
        ];
    }

    /**
     * A sorted array of lanuguages currently supported by this controller.
     * @return array | null
     */
    protected function getValidLanguages()
    {
        return [
            'en_GB' => 'en_GB',
            'en' => 'en',
        ];
    }
}
