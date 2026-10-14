{* The content finder's small search box below the content structure (parts/content/menu.tpl): only for a user
   with the policy contentfinder/search; it opens contentfinder/search, which checks the policy again. *}
{if fetch( 'user', 'has_access_to', hash( 'module', 'contentfinder', 'function', 'search' ) )}
<div id="exp-contentfinder-box" class="a4-contentfinder-box">
<form method="get" action={'contentfinder/search'|ezurl} role="search">
    <label for="exp-cf-box-text">{'Content finder'|i18n( 'kernel/contentfinder' )}</label>
    <div class="a4-contentfinder-box-row">
        <input type="search" class="box" id="exp-cf-box-text" name="SearchText" maxlength="1024" autocomplete="off" spellcheck="false" placeholder="{'Id, path or URL'|i18n( 'kernel/contentfinder' )|wash}" title="{'Find content by an object, node, attribute or version id, a remote id, a URL alias or the path of a file.'|i18n( 'kernel/contentfinder' )|wash}" required="required" />
        <input type="hidden" name="SearchType" value="auto" />
        <input type="submit" class="button" value="{'Find'|i18n( 'kernel/contentfinder' )}" />
    </div>
</form>
</div>
{/if}
