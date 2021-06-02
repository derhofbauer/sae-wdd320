<h2>Shares</h2>

<table class="table table-striped">

    <thead>
    <tr>
        <th>#</th>
        <th>Message</th>
        <th>Recipient</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($shares as $share): ?>
        <tr>
            <td><?php echo $share->id; ?></td>
            <td><?php echo $share->message; ?></td>
            <td><?php echo $share->recipient; ?></td>
            <td><?php echo $share->status; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>

</table>
