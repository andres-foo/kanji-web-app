# KANJIAPP

## ABOUT
This is a small app I'm using to study kanjis. It's a locally run php app that uses a sqlite database for keeping track of kanjis being learned and custom stories.

![Screenshot](https://github.com/andres-foo/kanji-web-app/blob/main/data/Screenshot.png?raw=true)


## USAGE

It only requires an apache server to run. It currently holds my progress as I make modifications, to start fresh the table **kanjis_study** and **examples_study** should be wiped clean with a sqlite db browser like [this one](https://sqlitebrowser.org/).

The starting page is:
```
/kanjiapp/pages/index.php
```

## SCORING SYSTEM

There's a very simple scoring system in place dependent on reviews and selecting **easy** or **hard** and it functions like this:

* All kanjis begin with a score of zero
* Kanjis are selected for review by the lowest score first
* Being selected for review adds 1 to the score
* Being marked as easy adds 2 to the score
* Being marked as hard substract 2 from the score

## CREDITS

### Kanjis
Kanjis are taken from the KANJIDIC Project at: [http://www.edrdg.org/wiki/index.php/KANJIDIC_Project](http://www.edrdg.org/wiki/index.php/KANJIDIC_Project)

### Extractor
Kanjis were extracted and converted to a sqlite using kanjidic2 extractor found at: [https://github.com/andres-foo/kanjidic2-extractor](https://github.com/andres-foo/kanjidic2-extractor)

### Components 
Componets for each kanji were extracted from the Ideographic Description Sequences (IDS) for CJK Unified Ideographs from the url [https://babelstone.co.uk/CJK/IDS.TXT](https://babelstone.co.uk/CJK/IDS.TXT)

### Kanji stroke orders
For the stroke orders the font KanjiStrokeOrders was used, it can be found at: [https://www.nihilist.org.uk/](https://www.nihilist.org.uk/)

### Font support for certain unicode character
The font BabelStoneHan was added to correctly display some literals, it can be found at: [https://www.babelstone.co.uk/Fonts/PUA.html](https://www.babelstone.co.uk/Fonts/PUA.html)
