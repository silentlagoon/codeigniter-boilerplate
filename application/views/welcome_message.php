<?php if(isset($users)) : ?>
    <div>
        <div style="display: table-cell">
            <b>ID</b>
        </div>
        <div style="display: table-cell">
            <b>Name</b>
        </div>
        <div style="display: table-cell">
            <b>Password</b>
        </div>
    </div>
    <?php foreach($users as $user): ?>
        <div>
            <div style="display:table-cell;">
                <?php echo $user['id'] . ' - ' . $user['name'] . ' - ' . $user['password']; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <?php echo 'You are ready to go!'; ?>
<?php endif; ?>
