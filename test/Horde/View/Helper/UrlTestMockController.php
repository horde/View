<?php
namespace Horde\View\Helper;
use \Horde_Controller_Base as Base;
use \Horde_Controller_Request;
use \Horde_Controller_Response;

class UrlTestMockController extends Base
{
    public function processRequest(Horde_Controller_Request $request, Horde_Controller_Response $response)
    {
    }

    public function getUrlWriter()
    {
        return new Horde_Controller_UrlWriter(new Horde_Routes_Utils(new Horde_Routes_Mapper()));
    }
}
