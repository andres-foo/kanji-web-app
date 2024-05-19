<?php define('home', true); ?>


<?php require '../parts/header.php'; ?>

<div class="card empty rules">
    This is a simple app for studying kanjis and here are the rules:
    <h2>Searching</h2>
    The easiest way is to just type the kanji you're looking for, there're no radicals lookups or drawing since that's not the purpose of the app, but you can do the following:
    <ul>
        <li>You can pick a kanji from any list and select it (eg. <a href="kanji.php?literal=日">日</a>)</li>
        <li>You can type in English and it will look within the meanings of the kanji (eg. <a href="search.php?query=sound">sound</a>)</li>
        <li>You can type a string that contains several kanjis (eg. <a href="search.php?query=日本語は...">日本語は...</a>) and it will look up all of them</li>
        <li>You can type in hiragana and it will look within the readings (both on and kun)(eg. <a href="search.php?query=なな.つ">なな.つ</a>)</li>
        <li>You can type a reading in English and the hiragana will be suggested (eg. <a href="search.php?query=nana.tsu">nana.tsu</a>)</li>
    </ul>

    <h2>Studying</h2>
    The studying portion is quite simple:
    <ol>
        <li>Find the kanjis you want to study</li>
        <li>Click on the <b>add</b> button on the top right of the card to add them to your list</li>
        <li>Click on the green <b>review</b> button and a kanji from your study list will be selected for review</li>
        <li>If the kanji that gets selected is easy or hard for you click on the corresponding button, otherwise just let it be</li>
    </ol>
    That's it, if you have more time keep clicking on the <b>review</b> button to review another kanji.

    <h2>How are kanji selected for review?</h2>
    There's a very simple system in place that's based on scores, the rules are as follow:
    <ul>
        <li>Each kanji begins with a score of zero</li>
        <li>Clicking on the <b>review</b> button will select a kanji with a low score</li>
        <li>Just by being selected by the review, one point will be added to the score of the kanji</li>
        <li>Clicking on easy, will add two additional points to the score of the kanji</li>
        <li>Clicking on hard, will substract two points from the score of the kanji</li>
    </ul>

    <h2>What about the editing part?</h2>
    Some parts are in place to standardize the kanjis and other parts are for studying:
    <ul>
        <li><b>Components</b>: For the most part they're done so they don't need to be touched, but I have the editing in place to fine tune them as I study the kanjis.</li>
        <li><b>Other forms</b>: This is in place for kanjis that take different forms when used as components.</li>
        <li><b>Story</b>: This is a personal story that one can add to help remember the kanji</li>
        <li><b>Add an example</b>: This allows to add example words for kanjis. Adding a word that contains more than one kanji will add the example to all kanjis involved.</li>
    </ul>

    <h2>Reset study list</h2>
    Do you want to reset your current progress? Clicking the following button will remove any added kanji from your list and will reset every score back to zero. The stories and images already present will not be removed.<form action="../actions/reset_progress.php" method="POST"><button type="submit" class="danger"  onclick="return confirm('This will reset all your progress. Do you want to continue?')">delete progress</button></form>

    <h2>What's next?</h2>
    I contemplated adding a built in database for examples (words and/or phrases) like JMdict_e but I decided against it since I don't want this app to turn into a dictionary. For the mime_content_type 
    the app has everything I want it to have.


</div>

<?php require '../parts/footer.php'; ?>