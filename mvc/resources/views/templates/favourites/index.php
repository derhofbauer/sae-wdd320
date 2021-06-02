<h2>Favourites</h2>

<table class="table table-striped">

    <thead>
    <tr>
        <th>#</th>
        <th>Title</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($favourites as $favourite): ?>
        <tr class="favourite favourite-<?php echo $favourite->id; ?>">
            <td><?php echo $favourite->post()->id; ?></td>
            <td><?php echo $favourite->post()->title; ?></td>
            <td>
                <button class="btn btn-danger btn-sm favourite-remove" data-href="<?php echo BASE_URL; ?>/api/favourites/remove/<?php echo $favourite->post()->id;?>">Löschen</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>

</table>

<a href="<?php echo BASE_URL; ?>/checkout" class="btn btn-primary">Checkout</a>
