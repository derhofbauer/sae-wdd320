<div class="blog">
    <article>
        <h2>
            <?php echo $post->title; ?>
            <small>
                <button class="btn btn-primary btn-sm favourite-add" data-href="<?php echo BASE_URL; ?>/api/favourites/add/<?php echo $post->id; ?>">
                    Favorit (DE!!)
                </button>
            </small>
            <small><?php echo $post->getAverageAndNumberRatings()['average']; ?>/5 Sternen (<?php echo $post->getAverageAndNumberRatings()['numberOfRatings']; ?> Ratings)</small>
        </h2>
        <?php require __DIR__ . '/../../partials/post/meta.php'; ?>

        <div class="content"><?php echo $post->content; ?></div>

        <div class="images slider">
            <?php foreach ($post->files() as $file): ?>
                <figure>
                    <?php echo $file->getImgTag(); ?>
                    <figcaption>
                        <?php echo $file->caption; ?>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>

        <h3>Comments</h3>

        <?php /* @todo: comment */
        if (\App\Models\User::isLoggedIn()): ?>
            <form action="<?php echo BASE_URL; ?>/blog/<?php echo $post->id; ?>/comment" method="post">
                <?php if (!$post->hasBeenRatedByUser(\App\Models\User::getLoggedIn()->id)): ?>
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <select name="rating" id="rating" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" class="form-control editor" placeholder="e.g. The answer to life, the universe and everything ..."></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Submit</button>
            </form>
        <?php endif; ?>

        <div class="comments" id="comments">
            <?php foreach ($post->comments() as $comment): ?>
                <div class="comment" id="comment-<?php echo $comment->id; ?>">
                    <div class="meta">
                        <span class="meta-item"><?php echo $comment->author()->email; ?></span> |
                        <span class="meta-item"><?php echo $comment->getCrdate(); ?></span>
                        <?php if (!empty($comment->rating)): ?>
                            | <span class="meta-item">Rating: <?php echo $comment->rating; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="content"><?php echo $comment->content; ?></div>

                    <div class="replies">
                        <?php foreach ($comment->replies() as $reply): ?>
                            <div class="replay" id="comment-<?php echo $reply->id; ?>">
                                <div class="meta">
                                    <span class="meta-item"><?php echo $reply->author()->email; ?></span> |
                                    <span class="meta-item"><?php echo $reply->getCrdate(); ?></span>
                                </div>
                                <div class="content"><?php echo $reply->content; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (\App\Models\User::isLoggedIn()): ?>
                        <form action="<?php echo BASE_URL; ?>/blog/<?php echo $post->id; ?>/comment" method="post">
                            <input type="hidden" name="parent-comment" value="<?php echo $comment->id; ?>">
                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <textarea name="comment" id="comment" class="form-control editor" placeholder="e.g. The answer to life, the universe and everything ..."></textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</div>
