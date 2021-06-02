<form action="<?php echo BASE_URL; ?>/checkout/recipient" method="post">

    <div class="row">
        <div class="form-group col">
            <label for="name">Recipient: Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Arthur Dent" required>
        </div>

        <div class="form-group col">
            <label for="email">Recipient: E-mail</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="e.g. arthur.dent@galaxy.com" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Weiter</button>

</form>
