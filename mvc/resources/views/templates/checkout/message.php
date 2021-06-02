<form action="<?php echo BASE_URL; ?>/checkout/message/<?php echo $share->id; ?>" method="post">

    <div class="form-group">
        <label for="message">Message</label>
        <textarea name="message" id="message" class="form-control" placeholder="e.g. Answer to the question of life, the universe and everything ..." required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Weiter</button>

</form>
