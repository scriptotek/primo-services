<?php

namespace BCLib\XServices\Primo;

use BCLib\XServices;

class FullView extends PrimoRequest
{

    public function __construct(XServices\Translator $translator, $host='agama.bc.edu', $port = '1701')
    {
        parent::__construct($translator);
        $this->_setServiceUrl('search/full', $host, $port);
    }

    public function setDocumentID($document_id)
    {
        if ($this->_IDIsAlephNumber($document_id))
        {
            $document_id = 'bc_aleph' . $document_id;
        }
        $this->_addArgument('docId', $document_id);
        return $this;
    }

    private function _IDIsAlephNumber($document_id)
    {
        return (is_numeric($document_id[0]));
    }

}