<h3>Melde dich an!</h3>
<form role="form" method="post">
    <div class="form-group">
        <label for="fullname">Dein Name</label>
        <input name="fullname" type="fullname" class="form-control" id="fullname" placeholder="Dein Name">
    </div>
    <div class="form-group">
        <label for="email">Email Adresse</label>
        <input name="email" type="email" class="form-control" id="email" placeholder="Email Adresse">
    </div>

    <label for="newsletter_category">Für welche Themen interessierst du dich?</label>
    <select name="newsletter_category" class="form-control" id="newsletter_category">
        <?php

        /*
         * Newsletter-Themen mittels Schleife durchlaufen
         * Für jede Kategorie ein <option> Element erzeugen (value = ID, Inhalt = Titel)
         */
        $result = mysqli_query($link, 'SELECT id,title FROM newsletter_categories');
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

        //Schleife-Beginn
        foreach ($categories as $category) {
            /*
             * Variablen mit Inhalt aus DB befüllen
             */

            $id = $category['id'];
            $title = $category['title'];

            echo "<option value='$id'>$title</option>";

        //Schleife-Ende
        }
        ?>
    </select>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
