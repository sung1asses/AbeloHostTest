{extends file='layout.tpl'}

{block name=content}
<section class="category-intro">
    <h1>{$category.name}</h1>
    <p>{$category.description}</p>
</section>

<section class="filters">
    <form method="get">
        <input type="hidden" name="slug" value="{$category.slug}">
        <label>Сортировать по:
            <select name="sort">
                {foreach $filters.options as $value => $label}
                    <option value="{$value}" {if $filters.current == $value}selected{/if}>{$label}</option>
                {/foreach}
            </select>
        </label>
        <button class="btn" type="submit">Применить</button>
    </form>
</section>

<section class="article-list">
    {foreach $articles as $article}
        <article class="article-card">
            {if $article.image}
                <div class="cover">
                    <img src="{$article.image}" alt="{$article.title}" loading="lazy">
                </div>
            {/if}
            <div class="article-body">
                <h2>
                    <a href="{$baseUrl}/post?slug={$article.slug}">{$article.title}</a>
                </h2>
                <p>{$article.excerpt}</p>
                <div class="meta">
                    <span>Просмотры: {$article.views}</span>
                    <span>Опубликовано: {$article.published_at}</span>
                </div>
                <a class="ghost-btn" href="{$baseUrl}/post?slug={$article.slug}">Читать статью</a>
            </div>
        </article>
    {/foreach}
</section>

<section class="pagination">
    <p>Страница {$pagination.current} из {$pagination.total}</p>
    <div class="pager-controls">
        {if $pagination.has_prev}
            <a class="ghost-btn pager-btn" href="{$baseUrl}/category?slug={$category.slug}&sort={$filters.current}&page={$pagination.prev}">← Назад</a>
        {else}
            <span class="ghost-btn pager-btn disabled" aria-disabled="true">← Назад</span>
        {/if}

        {if $pagination.has_next}
            <a class="btn pager-btn" href="{$baseUrl}/category?slug={$category.slug}&sort={$filters.current}&page={$pagination.next}">Вперёд →</a>
        {else}
            <span class="btn pager-btn disabled" aria-disabled="true">Вперёд →</span>
        {/if}
    </div>
</section>
{/block}
