<h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
<p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>

<table width="100%">
    <thead>
        <tr>
            <th>User</th>
            <th>Email</th>
            <th>Details</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($users as $id => $user) { ?>
            <tr>
                <td><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><a href="/users/<?= $id ?>">Details</a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>