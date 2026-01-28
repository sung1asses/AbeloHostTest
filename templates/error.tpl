{extends file='layout.tpl'}

{block name=content}
<section class="error-state">
    <div class="error-card">
        <p class="error-eyebrow">Ошибка {$code|default:500}</p>
        <h1>{$heading|default:'Что-то пошло не так'}</h1>
        <p class="error-message">
            {$message|default:'Попробуйте обновить страницу или вернитесь чуть позже — мы уже разбираемся.'}
        </p>
        <div class="error-actions">
            <a class="btn" href="{$baseUrl}">На главную</a>
            <a class="ghost-btn" href="javascript:history.back()">Вернуться назад</a>
        </div>
    </div>
</section>
{/block}
