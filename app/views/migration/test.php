<h1>Test des modèles</h1>

<?php foreach($data as $table => $rows): ?>
    <h2><?php echo ucfirst($table); ?></h2>
    <?php if(!empty($rows)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <?php foreach(array_keys($rows[0]) as $col): ?>
                        <th><?php echo $col; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows as $row): ?>
                    <tr>
                        <?php foreach($row as $value): ?>
                            <td><?php echo htmlspecialchars($value ?? ''); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune donnée trouvée.</p>
    <?php endif; ?>
<?php endforeach; ?>
