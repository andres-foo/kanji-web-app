<?php define('home', true); ?>


<?php require '../parts/header.php'; ?>

<div class="card empty rules">

    <h1>Add phrase</h1>

    <div class="add-example">
        <form action="../actions/add-phrase.php" method="POST">
            <p>Phrase</p>
            <input type="text" name="phrase" placeholder="りんご以外はいりません。">
            <p>Phrase with ruby</p>
            <input type="text" name="phrase_ruby" placeholder="りんご<ruby>以外<rt>いがい</rt></ruby>はいりません。" id="ruby">
            <div id="preview"></div>
            <p>Translation</p>
            <input type="text" name="translation" placeholder="I don’t need anything except apples.">
            <p>Source</p>
            <input type="text" name="source" placeholder="Game / Anime / Manga">
            <p><button type="submit">Add phrase</button></p>


        </form>

    </div>

</div>

<?php require '../parts/footer.php'; ?>

<script>
    let preview = document.querySelector("#preview");
    let ruby = document.querySelector("#ruby");

    ruby.onchange = () => {
        preview.style.display = "inline-block";
        preview.innerHTML = ruby.value;
    }
</script>