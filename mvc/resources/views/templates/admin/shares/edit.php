<h2>Share #<?php echo $share->id; ?> <small class="text-muted">Edit</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/shares/<?php echo $share->id; ?>/update" method="post">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="title">User</label>
                <input type="text" class="form-control" id="title" value="<?php echo $share->user(); ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="recipient">Recipient</label>
                <input type="text" class="form-control" id="recipient" value="<?php echo $share->recipient; ?>" required readonly>

            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <?php
                    /**
                     * @todo: comment
                     */
                    $stati = \App\Models\Share::STATI;

                    foreach ($stati as $htmlValue => $label): ?>
                        <option value="<?php echo $htmlValue; ?>"<?php echo ($htmlValue === $share->status) ? ' selected': ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" class="form-control" readonly><?php echo $share->message; ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Posts</label>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($share->posts() as $post): ?>
                <tr class="favourite favourite-<?php echo $post->id; ?>">
                    <td><?php echo $post->id; ?></td>
                    <td><?php echo $post->title; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/categories" class="btn btn-danger">Cancel</a>

</form>
