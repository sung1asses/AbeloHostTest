<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title|default:$app.name}</title>
    <link rel="stylesheet" href="{$baseUrl}/assets/css/app.css">
</head>
<body>
    {include file='partials/header.tpl'}

    <main class="container">
        {if isset($breadcrumbs) && $breadcrumbs|@count > 0}
            {include file='partials/breadcrumbs.tpl'}
        {/if}
        {block name=content}{/block}
    </main>

    {include file='partials/footer.tpl'}

    <script src="{$baseUrl}/assets/js/app.js" defer></script>
</body>
</html>
