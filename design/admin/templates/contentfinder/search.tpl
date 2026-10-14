{* The contentfinder/search view (doc/features/6.0/content-finder.md). Variables: search_text, search_type, types,
   found (null before a search; else input, results, count, truncated), feedback (the outcome of an action, or
   null), form_token, token_field, can_act. Only what the user may see is in found.results. *}
{def $type_names = hash( 'auto', 'Recognise automatically'|i18n( 'kernel/contentfinder' ),
                         'object', 'Object id'|i18n( 'kernel/contentfinder' ),
                         'node', 'Node id'|i18n( 'kernel/contentfinder' ),
                         'attribute', 'Attribute id'|i18n( 'kernel/contentfinder' ),
                         'version', 'Version id'|i18n( 'kernel/contentfinder' ),
                         'remote', 'Remote id'|i18n( 'kernel/contentfinder' ),
                         'urlalias', 'URL alias'|i18n( 'kernel/contentfinder' ),
                         'file', 'File path'|i18n( 'kernel/contentfinder' ) )
     $action_names = hash( 'trash', 'Moved to the trash'|i18n( 'kernel/contentfinder' ),
                           'delete', 'Deleted permanently'|i18n( 'kernel/contentfinder' ),
                           'purge', 'Purged from the trash'|i18n( 'kernel/contentfinder' ),
                           'removeversion', 'Version removed'|i18n( 'kernel/contentfinder' ) )}
<div class="context-block a4-page a4-contentfinder" id="exp-contentfinder">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Content finder'|i18n( 'kernel/contentfinder' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $feedback}
    {if $feedback.done}
    <div class="message-feedback" role="status"><h2>{$action_names[$feedback.action]|wash}: {$feedback.name|wash} ({'object %id'|i18n( 'kernel/contentfinder',, hash( '%id', $feedback.object_id ) )}{if $feedback.version}, {'version %v'|i18n( 'kernel/contentfinder',, hash( '%v', $feedback.version ) )}{/if})</h2></div>
    {else}
    <div class="message-warning" role="alert"><h2>{'The action was not done.'|i18n( 'kernel/contentfinder' )} {include uri='design:contentfinder/reason.tpl' reason=$feedback.reason}</h2></div>
    {/if}
{/if}

<div class="block">
<form method="get" action={'contentfinder/search'|ezurl} class="a4-contentfinder-form" role="search">
    <div class="a4-contentfinder-text">
        <label for="exp-cf-text">{'Id, path or URL'|i18n( 'kernel/contentfinder' )}</label>
        <input type="text" class="box" id="exp-cf-text" name="SearchText" value="{$search_text|wash}" maxlength="1024" autocomplete="off" spellcheck="false" required="required" />
    </div>
    <div class="a4-contentfinder-type">
        <label for="exp-cf-type">{'Search for'|i18n( 'kernel/contentfinder' )}</label>
        <select id="exp-cf-type" name="SearchType">
        {foreach $types as $type}
            <option value="{$type|wash}"{if eq( $type, $search_type )} selected="selected"{/if}>{$type_names[$type]|wash}</option>
        {/foreach}
        </select>
    </div>
    <div><input type="submit" class="defaultbutton" value="{'Find'|i18n( 'kernel/contentfinder' )}" /></div>
</form>
<p class="a4-contentfinder-hint">{'Enter an object, node, attribute or version id, a remote id, a URL alias, a page URL, or the path or URL of a file below var/ (for example the path of an image). A path that ends with / or * finds every file below it. A number is tried as every kind of id.'|i18n( 'kernel/contentfinder' )}</p>
</div>

{if $found}
    {if $found.input.error|ne( '' )}
    <div class="message-warning a4-contentfinder-none" role="alert"><h2>{if eq( $found.input.error, 'too_long' )}{'The text is too long.'|i18n( 'kernel/contentfinder' )}{else}{'This is not an id, path or URL the finder can look for.'|i18n( 'kernel/contentfinder' )}{/if}</h2></div>
    {elseif $found.count|eq( 0 )}
    <div class="message-warning a4-contentfinder-none" role="status"><h2>{'Nothing found, or you have no access to what was found.'|i18n( 'kernel/contentfinder' )}</h2></div>
    {if $found.notice|eq( 'alias_part_too_short' )}<p class="a4-contentfinder-hint">{'Enter at least %count characters to search by part of an alias.'|i18n( 'kernel/contentfinder',, hash( '%count', ezini( 'ContentFinderSettings', 'MinimumPartLength', 'contentfinder.ini' ) ) )}</p>{/if}
    {else}
    <p>{'Objects found: %count.'|i18n( 'kernel/contentfinder',, hash( '%count', $found.count ) )}{if $found.truncated} {'Only the first %count are shown; enter a more exact text.'|i18n( 'kernel/contentfinder',, hash( '%count', $found.count ) )}{/if}</p>
    {foreach $found.results as $result}
        {include uri='design:contentfinder/result.tpl' result=$result form_token=$form_token token_field=$token_field type_names=$type_names}
    {/foreach}
    {/if}
{/if}

{* DESIGN: Content END *}</div></div></div>
</div>
{undef $type_names $action_names}
