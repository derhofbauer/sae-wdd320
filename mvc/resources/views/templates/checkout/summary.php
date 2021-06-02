<h2>Share <small>#<?php echo $share->id; ?></small></h2>

<div class="row">

    <div class="col">
        <h3>Absender</h3>
        <p>
            <strong>Name:</strong> <?php echo $user->username; ?>
        </p>
        <p>
            <strong>Email:</strong> <?php echo $user->email; ?>
        </p>
    </div>

    <div class="col">
        <h3>Empfänger</h3>
        <p>
            <?php echo htmlentities($share->recipient); ?>
        </p>
    </div>

</div>

<h3>Posts</h3>
<table class="table table-striped">

    <thead>
    <tr>
        <th>#</th>
        <th>Title</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($favourites as $favourite): ?>
        <tr class="favourite favourite-<?php echo $favourite->id; ?>">
            <td><?php echo $favourite->post()->id; ?></td>
            <td><?php echo $favourite->post()->title; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>

</table>

<a href="<?php echo BASE_URL; ?>/checkout/final/<?php echo $share->id; ?>" class="btn btn-primary">Finish</a>
