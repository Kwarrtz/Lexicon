# Lexicon MediaWiki Extension

This is an extension to MediaWiki intended to help manage instances of the wiki-based role-playing game [Lexicon](https://en.wikipedia.org/wiki/Lexicon_(game)).
It adds a special page at `Special:Lexicon` which gives an alphabetical list of all pages in the `Lexicon` category, as well as all non-existent pages
linked to by such a page (phantoms).

This extension has only been tested with MediaWiki versions 1.32-1.35, but is probably compatible with any version since 1.25.

## Installation

To install, clone this repository into the `extensions` directory of your MediaWiki setup and add the following line to the end of `LocalSettings.php` 
(if that file does not already exist, create it in the root MediaWiki directory):

```
wfLoadExtension('Lexicon');
```

If you want to use a category other than `Lexicon` to denote game entries, that can be configured by setting the `$wgLexiconEntryCategory` variable prior to 
loading the extension in `LocalSettings.php`:

```
$wgLexiconEntryCategory = 'My_Lexicon_Category';
wfLoadExtension('Lexicon');
```
