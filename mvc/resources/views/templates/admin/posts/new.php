<h2>Post <small class="text-muted">New</small></h2>

<form action="<?php echo BASE_URL; ?>/admin/posts/create" method="post">

    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title" placeholder="e.g. Category #1" required>
            </div>

            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" class="form-control" name="slug" id="slug" placeholder="e.g. category-1">
            </div>

            <div class="form-group">
                <label for="author">Author</label>
                <select name="author" id="author" class="form-control">
                    <option value="_default" hidden>Bitte auswählen ...</option>
                    <?php
                    /**
                     * Damit wir ein Dropdown dynamisch generieren können, müssen wir mit einer Schleife arbeiten.
                     *
                     * Hier ist der Ternäre Operator zu beachten, den wir verwenden um anzugeben, ob eine <option>
                     * vorausgewählt sein soll oder nicht.
                     */
                    foreach ($admins as $user): ?>
                        <option value="<?php echo $user->id; ?>"<?php echo (\App\Models\User::getLoggedIn()->id === $user->id) ? ' selected' : ''; ?>><?php echo $user->username ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                <span class="label">Category</span>
                <?php
                /**
                 * Damit wir eine Liste an Checkboxes dynamisch generieren können, müssen wir mit einer Schleife
                 * arbeiten.
                 *
                 * Hier ist der Ternäre Operator zu beachten, den wir verwenden um anzugeben, ob eine Checkbox
                 * ausgewählt sein soll oder nicht.
                 */
                foreach ($categories as $category): ?>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="categories[<?php echo $category->id; ?>]">
                        <label for="categories[<?php echo $category->id; ?>]" class="form-check-label"><?php echo $category->title; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="content">Content</label>
        <textarea name="content" id="content" rows="10" class="form-control editor"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?php echo BASE_URL ?>/admin/posts" class="btn btn-danger">Cancel</a>

</form>
