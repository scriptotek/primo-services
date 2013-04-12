<?php

namespace BCLib\PrimoServices;

class PrimoServices extends \Pimple
{
    private $_host;

    public function __construct($host)
    {
        $this->_host = $host;

        parent::__construct();

        $this['holding'] = function ()
        {
            return new PhysicalHolding();
        };

        $this['holding_factory'] = $this->protect(function ()
        {
            return $this['holding'];
        });

        $this['person'] = function ()
        {
            return new Person();
        };

        $this['person_factory'] = $this->protect(function ()
        {
            return $this['person'];
        });

        $this['bib_record_component'] = function ()
        {
            return new BibRecordComponent();
        };

        $this['bib_record_component_factory'] = $this->protect(function ()
        {
            return $this['bib_record_component'];
        });

        $this['bib_record'] = function ()
        {
            return new BibRecord();
        };

        $this['bib_record_factory'] = $this->protect(function ()
        {
            return $this['bib_record'];
        });

        $this['pnx_translator'] = function ()
        {
            return new PNXTranslator($this['bib_record_factory'],
                $this['holding_factory'],
                $this['person_factory'],
                $this['bib_record_component_factory'],
                $this['cache']);
        };

        $this['cache'] = function()
        {

        };

        $this['facet_translator'] = function ()
        {
            return new FacetTranslator($this['facet_factory'], $this['facet_value_factory']);
        };

        $this['facet'] = function ()
        {
            return new Facet();
        };

        $this['facet_factory'] = $this->protect(function ()
        {
            return $this['facet'];
        });

        $this['facet_value'] = function ()
        {
            return new FacetValue();
        };

        $this['facet_value_factory'] = $this->protect(function ()
        {
            return $this['facet_value'];
        });

        $this['query'] = function ()
        {
            return new Query($this->_institution);
        };

        $this['query_term'] = function ()
        {
            return new QueryTerm();
        };

        $this['search_result'] = function ()
        {
            return new BriefSearchResult();
        };
    }

    public function ask(Query $query)
    {
        $url = 'http://' . $this->_host . '/PrimoWebServices/xservice/search/brief?' . $query;
        $curl_options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $url,
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $curl_options);
        $xml = curl_exec($curl);

        $xml_result = simplexml_load_string($xml);
        $xml_result->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $xml_result->registerXPathNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');

        /* @var $result BriefSearchResult */
        $result = $this['search_result'];
        $result->facets = $this['facet_translator']->translate($xml_result);
        $result->results = $this['pnx_translator']->translate($xml_result);

        $docset = $xml_result->xpath('/sear:SEGMENTS/sear:JAGROOT/sear:RESULT/sear:DOCSET');
        $result->total_results = (string) $docset[0]['TOTALHITS'];
        return $result;
    }
}