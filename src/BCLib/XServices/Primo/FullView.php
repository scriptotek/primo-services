<?php

namespace BCLib\XServices\Primo;

use BCLib\XServices;

class FullView extends PrimoRequest
{

    public function __construct(XServices\Translator $translator, $host='bc-primo.hosted.exlibrisgroup.com', $port = '')
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/full', $host, $port);
    }

    public function setDocumentID($document_id)
    {
        $this->_addArgument('docId', $document_id);
        return $this;
    }

    private function _IDIsAlephNumber($document_id)
    {
        return (is_numeric($document_id[0]));
    }

}