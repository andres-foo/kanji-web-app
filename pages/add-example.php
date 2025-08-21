<?php define('home', true); ?>


<?php require '../parts/header.php'; ?>

<div class="card empty rules">

    <h1>Add example</h1>

    <div class="add-example">
        <form action="../actions/add-example.php" method="POST">
            <span>Kanji</span>
            <input type="text" name="kanji" placeholder="外国">
            <span>Kana</span>
            <input type="text" name="kana" placeholder="がいこく ">
            <span>Meaning</span>
            <input type="text" name="meanings" placeholder="foreign country">
            <span>JLPT level</span>
            <select name="jlpt">
                <option value="-1">-1
                <option value="1">1
                <option value="2">2
                <option value="3">3
                <option value="4">4
                <option value="5">5
            </select>
            <input type="checkbox" checked name="added">add example to my list
            <p><button type="submit">Add example</button></p>


        </form>
    </div>

</div>

<?php require '../parts/footer.php'; ?>