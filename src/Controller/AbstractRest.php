<?php

namespace H1ppo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractRest
{
    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;
    /**
     * @var Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    protected $useLatestVersionOnNoAcceptHeader = true;

    protected $useVersion;

    public $defaultLanguages = ['en' => 'en'];

    /**
     * @param HttpFoundation\Request  $request
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->init();
    }

    public function run()
    {
        switch (strtoupper($this->request->getMethod())) {
            case 'POST':
                $this->write();
            break;
            case 'GET':
                $this->read();
            break;
            case 'DELETE':
                $this->delete();
            break;
            case 'PATCH':
                $this->update();
            break;
            case 'PUT':
                $this->replace();
            break;
            case 'OPTIONS':
                $this->docs();
            break;
            case 'HEAD':
                $this->summary();
            break;
            case 'TRACE':
                $this->debug();
            break;
            default:
                // throw a 404?
            break;
        }

        return $this->response;
    }

    protected function init()
    {
        $this->validateAcceptHeader();
        $this->validateLanguage();
    }

    protected function validateAcceptHeader()
    {
        $acceptHeaders = $this->request->getAcceptableContentTypes();
        $validContentTypes = $this->getValidContentTypes();
        $toBedeprecatedContentTypes = $this->getToBedeprecatedContentTypes();
        $deprecatedContentTypes = $this->getDeprecatedContentTypes();
        if (!$acceptHeaders) {
            // no accept header provided.
            if (!$useLatestVersionOnNoAcceptHeader) {
                // @todo throw some kind of exception here about needing to supply a version
            }
            // Assume latest version is in index 0
            $this->useVersion = $validContentTypes ? $validContentTypes[0] : null;
            return;
        }
        $valid = [];
        $toBedeprecated = [];
        $deprecated = [];
        foreach ($acceptHeaders as $accept) {
            if ($validContentTypes && array_key_exists($accept, $validContentTypes)) {
                $valid[$accept] = $accept;
            }
            if ($toBedeprecatedContentTypes && array_key_exists($accept, $toBedeprecatedContentTypes)) {
                $toBedeprecated[$accept] = $accept;
            }
            if ($deprecatedContentTypes && array_key_exists($accept, $deprecatedContentTypes)) {
                $deprecated[$accept] = $accept;
            }
        }

        // if we have a valid accept header then use it
        if ($valid) {
            $this->setContentType(current($valid));
            if (isset($toBedeprecated[$this->useVersion])) {
                // set a flag to mark this request as using a deprecated version
            }
            return;
        }
        if ($deprecated) {
            // throw an expcetion about a deprecated version and set a
            // 426 Upgrade Required http response code
            throw new \Exception('Version ' . current($deprecated) . ' has been deprecated. Please upgrade.', Response::HTTP_UPGRADE_REQUIRED);
        }
    }

    protected function validateLanguage()
    {
        $acceptedLanguages = $this->request->getLanguages();
        if (!$acceptedLanguages) {
            $acceptedLanguages = $this->defaultLanguages;
        }
        $validLanguages = $this->getValidLanguages();
        foreach ($acceptedLanguages as $language) {
            if (isset($validLanguages[$language])) {
                $this->setConentLanguage($language);
                return;
            }
        }
        throw new \Exception('The requested languages are not supported: ' . implode(", ", $acceptedLanguages) . '.', Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * Set the language for the response
     * @param string $language
     */
    protected function setConentLanguage($language)
    {
        $this->response->headers->set('Content-Language', $language);
        $this->response->headers->set('Vary', 'Content-Language', false);
    }

    /**
     * Set the content type for the response
     * @param string $contentType
     */
    protected function setContentType($contentType)
    {
        $this->response->headers->set('Content-Type', $contentType);
        $this->response->headers->set('Vary', 'Content-Type', false);
    }

    /**
     * A sorted array of content types currently supported by this controller.
     * Index 0 is assumed to be the latest version of the API
     * @return array | null
     */
    protected function getValidContentTypes()
    {
        return null;
    }

    /**
     * An array of content types which will be deprecated soon.
     * @return array | null
     */
    protected function getToBedeprecatedContentTypes()
    {
        return null;
    }

    /**
     * An array of content type which have been deprecated.
     * @return array | null
     */
    protected function getDeprecatedContentTypes()
    {
        return null;
    }

    /**
     * A sorted array of lanuguages currently supported by this controller.
     * @return array | null
     */
    protected function getValidLanguages()
    {
        return null;
    }

    /**
     * GET
     * Return 1-n resources
     */
    public function read()
    {
        throw new Exception\NotImplemented;
    }

    /**
     * POST
     * Create a resource
     */
    public function write()
    {
        throw new Exception\NotImplemented;
    }

    /**
     * PUT
     * Replace a resource
     */
    public function replace()
    {
        throw new Exception\NotImplemented;
    }

    /**
     * PATCH
     * Update a resource
     */
    public function update()
    {
        throw new Exception\NotImplemented;
    }

    /**
     * DELETE
     * Delete a resource
     */
    public function delete()
    {
        throw new Exception\NotImplemented;
    }

    /**
     * HEAD
     * Get the summary for 1-n resources (minimal get)
     */
    public function summary()
    {
        throw new Exception\NotImplemented;
    }

    /**
     * OPTIONS
     * Get the available methods for a resource
     */
    public function doc()
    {
        throw new Exception\NotImplemented;
    }

    /**
     * TRACE
     * Returns the request back to the client as it was received
     * @link(http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.8)
     */
    public function debug()
    {
        $this->response->headers->set('Content-Type', 'message/http');
        return $this->request->__toString();
    }
}
