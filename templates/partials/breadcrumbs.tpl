<nav class="breadcrumbs" aria-label="Хлебные крошки">
    <ol>
        {foreach from=$breadcrumbs item=crumb name=trail}
            <li>
                {if isset($crumb.url) && !$smarty.foreach.trail.last}
                    <a href="{$baseUrl}{$crumb.url}">{$crumb.title}</a>
                {else}
                    <span aria-current="page">{$crumb.title}</span>
                {/if}
            </li>
        {/foreach}
    </ol>
</nav>
