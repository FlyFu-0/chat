<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>Chat PHP Application - <?= $title ?></title>
</head>
<body>
<?php  if ($message): ?>
    <label>
        <input type="checkbox" class="alertCheckbox" autocomplete="off"/>
        <div class="alert notice">
        <span class="alertText"><?= $message ?>
    <br class="clear"/></span>
        </div>
    </label>
<?php endif ?>

<?= $content ?>
</body>
</html>