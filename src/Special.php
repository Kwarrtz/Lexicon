<?php
class SpecialLexicon extends SpecialPage {
    function __construct() {
        parent::__construct("Lexicon");
    }

    function execute($par) {
        $this->setHeaders();
        $output = $this->getOutput();
        $dbr = wfGetDB(DB_REPLICA);

        foreach (range('A', 'Z') as $letter) {
            $entries = $dbr->select(
                array("page", "categorylinks"),
                array("page_title"),
                array(
                    "page_title LIKE '" . $letter . "%'",
                    "cl_to" => "Loma_Roja"
                ),
                __METHOD__,
                array(),
                array("categorylinks" => array("INNER JOIN", array("page_id=cl_from")))
            );

            $phantoms = $dbr->select(
                array("pagelinks", "page"),
                array("pl_title"),
                array(
                    "pl_title LIKE '" . $letter . "%'",
                    "page_namespace IS NULL"
                ),
                __METHOD__,
                array(),
                array("page" => array("LEFT JOIN", array("page_namespace=pl_namespace", "page_title=pl_title")))
            );

            $output->addWikiTextAsContent("=" . $letter . "=\n");

            for ($i = 1; $i <= $entries->numRows(); $i++) {
                $output->addWikiTextAsContent("* [[" . $entries->fetchRow()["page_title"] . "]]\n");
            }

            for ($i = 1; $i <= $phantoms->numRows(); $i++) {
                $output->addWikiTextAsContent("* [[" . $phantoms->fetchRow()["pl_title"] . "]]\n");
            }
        }
    }
}
