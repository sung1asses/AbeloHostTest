{extends file='layout.tpl'}

{block name=content}
<article class="post">
    <header>
        <h1>{$post.title}</h1>
        <p>{$post.description}</p>
        <div class="meta">
            <span>Просмотры: {$post.views}</span>
            <span>Опубликовано: {$post.published_at}</span>
        </div>
    </header>

    <div class="content">
        <p>{$post.content}</p>
    </div>
</article>

<section class="related">
    <h2>Похожие статьи</h2>
    <ul>
        {foreach $related as $item}
            <li><a href="{$baseUrl}/post?slug={$item.slug}">{$item.title}</a></li>
        {/foreach}
    </ul>
</section>
{/block}
