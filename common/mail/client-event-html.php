<h3><?= $action ?></h3>

<p>
    Client: <b><?= htmlspecialchars($client->name) ?></b><br>
    Email: <?= htmlspecialchars($client->email) ?><br>
    Phone: <?= htmlspecialchars($client->phone ?? '-') ?><br>
    Time: <?= date('d M Y H:i:s') ?>
</p>