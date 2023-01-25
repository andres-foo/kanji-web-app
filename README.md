# KANJIAPP

## ABOUT
This is a small app I'm using to study kanjis. It's a locally run php app that uses a sqlite database for keeping track of kanjis being learned and custom stories.

## USAGE

It only requires an apache server to run. It currently holds my progress as I make modifications, to start fresh the table **kanjis_study** should be wiped clean with a sqlite db browser like [this one](https://sqlitebrowser.org/).

## CREDITS

### Kanjis
Kanjis are taken from the KANJIDIC Project at: [http://www.edrdg.org/wiki/index.php/KANJIDIC_Project](http://www.edrdg.org/wiki/index.php/KANJIDIC_Project)

### Extractor
Kanjis were extracted and converted to a sqlite using kanjidic2 extractor found at: [https://github.com/andres-foo/kanjidic2-extractor](https://github.com/andres-foo/kanjidic2-extractor)

### Components 
Componets for each kanji were extracted from the Ideographic Description Sequences (IDS) for CJK Unified Ideographs from the url [https://babelstone.co.uk/CJK/IDS.TXT](https://babelstone.co.uk/CJK/IDS.TXT)

### Kanji stroke orders
For the stroke orders the font KanjiStrokeOrders was used, it can be found at: [https://www.nihilist.org.uk/](https://www.nihilist.org.uk/)


