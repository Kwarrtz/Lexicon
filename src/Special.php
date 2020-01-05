<?php
class SpecialLexicon extends SpecialPage {
    function __construct() {
        parent::__construct("LexiconEntriesAndPhantoms");
    }

    function execute($par) {
        $output = $this->getOutput();
        $dbr = wfGetDB(DB_REPLICA);

        $pages = $dbr->select(
            "categorylinks",
            array("cl_from"),
            "cl_to = " . $wgLexiconCategory,
            __METHOD__
        );

        $cond = "page_id = " . array_pop($pages);
        foreach ($pages as $row) {
            $cond .= " OR " . $row->page_id;
        }

        foreach (range('A', 'Z') as $letter) {
            $titles = $dbr->select(
                "page",
                array("page_title"),
                "page_title LIKE " . $letter . "% AND (" . $cond . ")",
                __METHOD__,
                array("ORDER BY" => "page_title ASC")
            );

            $output->addWikiText("=" . $letter . "=\n");

            foreach ($titles as $title) {
                $output->addWikiText("* [[" . $title . "]]\n");
            }
    }
}