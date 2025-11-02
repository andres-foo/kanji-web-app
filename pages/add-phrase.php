<?php define('home', true); ?>


<?php require '../parts/header.php'; ?>

<div class="card empty rules">

    <h1>Add phrase</h1>

    <div class="add-example">
        <form action="../actions/add-phrase.php" method="POST">
            <span>AI input</span>
            <input type="text" name="ai" placeholder="phrase;phrase_ruby;translation" id="aiInput">

            <span>Phrase</span>
            <input type="text" name="phrase" placeholder="りんご以外はいりません。" id="phrase">
            <span>Phrase with ruby</span>
            <input type="text" name="phrase_ruby" placeholder="りんご<ruby>以外<rt>いがい</rt></ruby>はいりません。" id="phraseRuby">
            <span>Translation</span>
            <input type="text" name="translation" placeholder="I don’t need anything except apples." id="translation">
            <p><button type="submit">Add phrase</button></p>


        </form>
        <div class="phrase-preview" id="preview">
            <span class="phrase-preview-ruby" id="previewRuby"></span>
            <span class="phrase-preview-normal" id="previewNormal"></span>
            <span class="phrase-preview-translation" id="previewTranslation"></span>
        </div>
    </div>

</div>

<?php require '../parts/footer.php'; ?>

<script>
    let preview = document.querySelector("#preview");

    let aiInput = document.querySelector("#aiInput");
    let phrase = document.querySelector("#phrase");
    let phraseRuby = document.querySelector("#phraseRuby");
    let translation = document.querySelector("#translation");

    let previewRuby = document.querySelector("#previewRuby");
    let previewNormal = document.querySelector("#previewNormal");
    let previewTranslation = document.querySelector("#previewTranslation");

    aiInput.onchange = () => {
        let ai = aiInput.value;
        let [
            phrasev,
            phraseRubyv,
            translationv
        ] = ai.split(";");

        phrase.value = phrasev;
        phraseRuby.value = phraseRubyv;
        translation.value = translationv;

        preview.style.display = "flex";
        previewRuby.innerHTML = phraseRubyv;
        previewNormal.innerHTML = phrasev;
        previewTranslation.innerHTML = translationv;
    }
</script>