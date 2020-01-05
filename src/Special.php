<?php
class SpecialLexicon extends SpecialPage {
    function __construct() {
        parent::__construct("LexiconEntriesAndPhantoms");
    }

    function execute($par) {
        $output = $this->getOutput();
        $dbr = wfGetDB(DB_REPLICA);

        foreach (range('A', 'Z') as $letter) {
            $entries = $dbr->select(
                array("page", "categorylinks"),
                array("page_title"),
                array(
                    "page_title LIKE '" . $letter . "%'",
                    "cl_to" => $wgLexiconCategory
                ),
                __METHOD__,
                array(),
                array("categorylinks" => array("INNER_JOIN", array("page_id=cl_from")))
            );

            $phantoms = $dbr->select(
                array("pagelinks", "page"),
                array("pl_title"),
                array(
                    "pl_title LIKE '" . $letter . "%'",
                    "page_id IS NULL"
                ),
                __METHOD__,
                array(),
                array("page" => array("LEFT_JOIN", array("pl_from=page_id")))
            );

            $titles = array_merge($entries, $phantoms);

            $output->addWikiText("=" . $letter . "=\n");

            foreach ($titles as $title) {
                $output->addWikiText("* [[" . $title . "]]\n");
            }
        }
    }
}