{extends file='layout.tpl'}

{block name=content}
<section class="hero">
    <h1>Последние материалы по категориям</h1>
    <p>Это заглушка. Здесь появятся данные из базы, как только будет реализована бизнес-логика.</p>
</section>

<section class="categories">
    {foreach $categories as $category}
        <article class="category-card">
            <header>
                <h2>{$category.name}</h2>
                <p>{$category.description}</p>
            </header>
            <ul>
                {foreach $category.articles as $article}
                    <li><a href="{$baseUrl}/post?slug={$article.slug}">{$article.title}</a></li>
                {/foreach}
            </ul>
            <a class="btn" href="{$baseUrl}/category?slug={$category.slug}">Все статьи</a>
        </article>
    {/foreach}
</section>
{/block}
