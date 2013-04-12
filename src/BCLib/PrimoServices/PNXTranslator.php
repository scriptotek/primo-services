<?php

namespace BCLib\PrimoServices;

class PNXTranslator
{

    private $_bib_record_factory;
    private $_holding_factory;
    private $_person_factory;
    private $_bib_record_component_factory;

    public function __construct($bib_record_factory, $holding_factory,
                                $person_factory, $bib_record_component_factory)
    {
        $this->_bib_record_factory = $bib_record_factory;
        $this->_holding_factory = $holding_factory;
        $this->_person_factory = $person_factory;
        $this->_bib_record_component_factory = $bib_record_component_factory;
    }

    /**
     * @param \SimpleXMLElement $doc_xml
     *
     * @return BibRecord[]
     */
    public function translate(\SimpleXMLElement $doc_xml)
    {
        $xpath_to_primo_record = '//sear:DOC/PrimoNMBib/record';
        $xpath_to_pci_record = '//sear:DOC/prim:PrimoNMBib/prim:record';

        $doc_xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $doc_xml->registerXPathNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');
        $docs_xml = $doc_xml->xpath($xpath_to_primo_record . '|' . $xpath_to_pci_record);
        return \array_map(array($this, '_extractDoc'), $docs_xml);
    }

    private function _extractDoc(\SimpleXMLElement $record_xml)
    {
        /** @var $record BibRecord */
        $record = $this->_bib_record_factory->__invoke();

        $record->title = (string) $record_xml->display->title;
        $record->date = (string) $record_xml->display->creationdate;
        $record->publisher = (string) $record_xml->display->publisher;
        $record->abstract = (string) $record_xml->addata->abstract;
        $record->type = (string) $record_xml->display->type;
        $record->availability = (string) $record_xml->display->availpnx;
        $record->isbn = (string) $record_xml->search->isbn;
        $record->issn = (string) $record_xml->search->issn;
        $record->oclcid = (string) $record_xml->addata->oclcid;
        $record->reserves_info = (string) $record_xml->addata->lad05;
        $record->display_subject = (string) $record_xml->display->subject;
        $record->format = (string) $record_xml->display->format;

        return $record;
    }

}