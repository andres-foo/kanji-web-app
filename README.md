# KANJIAPP

## ABOUT

This is a small app I'm using to study kanjis. It's a locally run php app that uses a sqlite database for keeping track of kanjis being learned and custom stories.

![Screenshot](https://github.com/andres-foo/kanji-web-app/blob/main/data/Screenshot.png?raw=true)

## USAGE

It only requires an apache server to run. It currently holds my progress as I make modifications. To start fresh click the button **DELETE PROGRESS** found in the homepage.

The homepage is:

```
/kanjiapp/pages/index.php
```

## SCORING SYSTEM

There's a very simple scoring system in place dependent on reviews and selecting **easy** or **hard** and it functions like this:

- All kanjis begin with a score of zero
- Kanjis are selected for review by the lowest score first
- Being selected for review adds 1 to the score
- Being marked as easy adds 2 to the score
- Being marked as hard substract 2 from the score

## USING THE EXPORT FUNCTION FOR ANKI

Using the export function will create a csv file that can be imported in Anki. For that, a deck must be created that has the following fields with the exact same names:

```
Kanji
Meanings
Components
Story
Examples
Image
```

and the format for the cards should be the following:

### front template

```html
<div style="font-size:240px">{{Kanji}}</div>
```

### back template

```html
<div style="font-size:100px">{{Kanji}}</div>

<hr />

<p style="font-weight:bold">{{Meanings}}</p>

<p>{{Components}}</p>

{{#Story}}
<hr />
<p>{{Story}}</p>
{{/Story}} {{#Examples}}
<hr />
{{Examples}} {{/Examples}} {{#Image}}
<hr />
<img
  src="https://github.com/andres-foo/kanji-web-app/blob/main/data/images/{{Kanji}}.jpg?raw=true"
/>
{{/Image}}
```

The resulting deck would look like this:

![Anki](https://github.com/andres-foo/kanji-web-app/blob/main/data/Anki.png?raw=true)

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

### Example words by jlpt

The words used as example for each kanji were originally taken from [https://en.wiktionary.org/wiki/Appendix:JLPT](https://en.wiktionary.org/wiki/Appendix:JLPT)

### JLPT for kanjis

The JLPT that corresponds to each Kanji was modified from the original obtained from KANJIDIC project to include jlpt level 5. The new values were added from [https://www.nihongo-pro.com/kanji-pal/list/jlpt](https://www.nihongo-pro.com/kanji-pal/list/jlpt)
