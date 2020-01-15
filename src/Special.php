<?php
$gLexiconCategory = "Loma_Roja";

class SpecialLexicon extends SpecialPage {
    function __construct() {
        parent::__construct("lexicon");
    }

    function execute($par) {
        global $gLexiconCategory;

        $this->setHeaders();
        $output = $this->getOutput();
        $dbr = wfGetDB(DB_REPLICA);

        foreach (range('A', 'Z') as $letter) {
            $entries = $dbr->select(
                ["page", "categorylinks"],
                ["page_title"],
                [
                    "page_title LIKE '" . $letter . "%'",
                    "cl_to" => "Loma_Roja"
                ],
                __METHOD__,
                [],
                ["categorylinks" => ["INNER JOIN", ["page_id = cl_from"]]]
            );

            $phantomRefs = $dbr->select(
                ["pagelinks", "pg_to" => "page", "pg_from_nest" => ["pg_from" => "page", "categorylinks"]],
                ["to" => "pl_title", "from" => "pg_from.page_title"],
                [
                    "pl_title LIKE '" . $letter . "%'",
                    "pg_to.page_namespace IS NULL",
                    "cl_to" => "Loma_Roja"
                ],
                __METHOD__,
                [],
                [
                    "pg_to" => ["LEFT JOIN", ["pg_to.page_namespace = pl_namespace", "pg_to.page_title = pl_title"]],
                    "pg_from_nest" => ["LEFT JOIN", "pg_from.page_id = pl_from"],
                    "categorylinks" => ["INNER JOIN", "cl_from = pg_from.page_id"]
                ]
            );

            $phantoms = [];

            for ($i = 1; $i <= $phantomRefs->numRows(); $i++) {
                $row = $phantomRefs->fetchObject();
                $phantoms[$row->to][] = $row->from;
            }

            $linkify = function($title) { return "[[" . str_replace('_', ' ', $title) . "]]"; };

            $output->addWikiTextAsContent("=" . $letter . "=");

            for ($i = 1; $i <= $entries->numRows(); $i++) {
                $output->addWikiTextAsContent("*" . $linkify($entries->fetchObject()->page_title));
            }

            foreach ($phantoms as $phantom => $refs) {
                $output->addWikiTextAsContent("* [[" . $phantom . "]] (" . join(", ", array_map($linkify, $refs)) . ")");
            }
        }
    }

    function getGroupName() {
        return "pages";
    }
}
