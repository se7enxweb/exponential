{* The graphql/playground view: Setup > GraphQL. Whether the API answers here, its end points, the library, the
   schema, the siteaccesses it and its mutations answer in, its limits, its response cache, its persisted queries, the
   API keys with a GraphQL scope, and a playground that posts the query to the session end point graphql/query
   (graphql_playground.js). The buttons need the policy graphql/administrate.
   Variables: enabled, library, library_version, endpoint, session_endpoint, schema (hash, classes), example,
   can_administrate, messages (kind, text), siteaccesses (name, enabled, mutations), siteaccess (the one showing this
   page), answers_elsewhere (the API answers in some siteaccess), mutations, limits (label, value),
   cache (entries, bytes, expiredAt, enabled, ttl), persisted (hash, names, bytes, shipped), keys (id, prefix, name,
   owner, scopes, status, created, expires, lastUsed). *}
<div class="a4-page a4-graphql" id="exp-graphql-playground">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'GraphQL'|i18n( 'design/admin/graphql' )}</h1>
        <p class="a4-pagehead-sub">{'Ask the content of this site for exactly the fields a page needs, in one request. The schema follows the content classes; every query runs with the rights of the user who sends it.'|i18n( 'design/admin/graphql' )}</p>
    </div>
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}

{if and( $enabled|not, $answers_elsewhere )}
<div class="a4-notice is-info" role="status"><p>{'The GraphQL API is switched off in the siteaccess %siteaccess, which shows this page; the table below shows where it answers.'|i18n( 'design/admin/graphql',, hash( '%siteaccess', $siteaccess ) )|wash}</p></div>
{elseif $enabled|not}
<div class="a4-notice is-warn" role="status"><p>{'The GraphQL API is switched off in this siteaccess. Switch it on in graphql.ini: [GraphQLSettings] Enabled=enabled.'|i18n( 'design/admin/graphql' )}</p></div>
{elseif $library|not}
<div class="a4-notice is-bad" role="alert"><p>{'The GraphQL library (webonyx/graphql-php 15) is not installed.'|i18n( 'design/admin/graphql' )}</p></div>
{/if}

<section class="a4-card a4-section" aria-labelledby="graphql-facts">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-facts">{'End point'|i18n( 'design/admin/graphql' )}</h2>
        <p class="a4-meta">{'For the siteaccess %siteaccess, which shows this page.'|i18n( 'design/admin/graphql',, hash( '%siteaccess', $siteaccess ) )|wash}</p></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
            <dt>{'End point'|i18n( 'design/admin/graphql' )}</dt><dd><code class="a4-code">{$endpoint|wash}</code></dd>
            <dt>{'Library'|i18n( 'design/admin/graphql' )}</dt><dd>{if $library}webonyx/graphql-php {$library_version|wash}{else}&ndash;{/if}</dd>
            <dt>{'Schema'|i18n( 'design/admin/graphql' )}</dt><dd>{if $schema.hash}{'%count content classes, schema %hash'|i18n( 'design/admin/graphql',, hash( '%count', $schema.classes, '%hash', $schema.hash ) )|wash}{else}&ndash;{/if}</dd>
            <dt>{'Mutations'|i18n( 'design/admin/graphql' )}</dt><dd>{if and( $enabled, $mutations )}<span class="a4-badge is-ok">{'on'|i18n( 'design/admin/graphql' )}</span>{else}<span class="a4-badge">{'off'|i18n( 'design/admin/graphql' )}</span>{/if}</dd>
        </dl>
    </div>
</section>

<form method="post" action={'graphql/playground'|ezurl}>
<section class="a4-card is-flush a4-section" aria-labelledby="graphql-siteaccesses">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-siteaccesses">{'Siteaccesses'|i18n( 'design/admin/graphql' )}</h2>
        <p class="a4-meta">{'Where the API and its mutations answer. The buttons write graphql.ini, as ./console exp:graphql:enable and exp:graphql:disable do.'|i18n( 'design/admin/graphql' )}</p></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="graphql-siteaccesses" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Siteaccess'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'GraphQL API'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'Mutations'|i18n( 'design/admin/graphql' )}</th>
            {if $can_administrate}<th scope="col"><span class="a4-sr-only">{'Change'|i18n( 'design/admin/graphql' )}</span></th>{/if}
        </tr></thead>
        <tbody>
        {foreach $siteaccesses as $s}
        <tr>
            <td><code class="a4-code">{$s.name|wash}</code></td>
            <td>{if $s.enabled}<span class="a4-badge is-ok">{'on'|i18n( 'design/admin/graphql' )}</span>{else}<span class="a4-badge">{'off'|i18n( 'design/admin/graphql' )}</span>{/if}</td>
            <td>{if and( $s.enabled, $s.mutations )}<span class="a4-badge is-warn">{'on'|i18n( 'design/admin/graphql' )}</span>{else}<span class="a4-badge">{'off'|i18n( 'design/admin/graphql' )}</span>{/if}</td>
            {if $can_administrate}
            <td class="a4-actions">
                {if $s.enabled}
                <button type="submit" class="a4-btn a4-btn-small" name="DisableButton[{$s.name|wash}]" value="1">{'Switch off'|i18n( 'design/admin/graphql' )}<span class="a4-sr-only"> {$s.name|wash}</span></button>
                {if $s.mutations}
                <button type="submit" class="a4-btn a4-btn-small" name="MutationsOffButton[{$s.name|wash}]" value="1">{'Mutations off'|i18n( 'design/admin/graphql' )}<span class="a4-sr-only"> {$s.name|wash}</span></button>
                {else}
                <button type="submit" class="a4-btn a4-btn-small" name="MutationsOnButton[{$s.name|wash}]" value="1">{'Mutations on'|i18n( 'design/admin/graphql' )}<span class="a4-sr-only"> {$s.name|wash}</span></button>
                {/if}
                {else}
                <button type="submit" class="a4-btn a4-btn-small a4-btn-primary" name="EnableButton[{$s.name|wash}]" value="1">{'Switch on'|i18n( 'design/admin/graphql' )}<span class="a4-sr-only"> {$s.name|wash}</span></button>
                {/if}
            </td>
            {/if}
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>
</form>

<div class="a4-graphql-grid">
<section class="a4-card a4-section" aria-labelledby="graphql-limits">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-limits">{'Limits'|i18n( 'design/admin/graphql' )}</h2>
        <p class="a4-meta">{'graphql.ini [GraphQLSettings] of this siteaccess.'|i18n( 'design/admin/graphql' )}</p></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
        {foreach $limits as $l}
            <dt>{$l.label|wash}</dt><dd>{$l.value|wash}</dd>
        {/foreach}
        </dl>
    </div>
</section>

<section class="a4-card a4-section" aria-labelledby="graphql-cache">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-cache">{'Response cache'|i18n( 'design/admin/graphql' )}</h2>
        <p class="a4-meta">{'The answers of persisted queries, kept per user rights and expired whenever content is published.'|i18n( 'design/admin/graphql' )}</p></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
            <dt>{'State'|i18n( 'design/admin/graphql' )}</dt><dd>{if $cache.enabled}<span class="a4-badge is-ok">{'on'|i18n( 'design/admin/graphql' )}</span>{else}<span class="a4-badge">{'off'|i18n( 'design/admin/graphql' )}</span>{/if}</dd>
            <dt>{'Answers kept'|i18n( 'design/admin/graphql' )}</dt><dd>{'%count answers, %bytes bytes'|i18n( 'design/admin/graphql',, hash( '%count', $cache.entries, '%bytes', $cache.bytes ) )|wash}</dd>
            <dt>{'Kept at most'|i18n( 'design/admin/graphql' )}</dt><dd>{'%seconds s'|i18n( 'design/admin/graphql',, hash( '%seconds', $cache.ttl ) )|wash}</dd>
            <dt>{'Last expired'|i18n( 'design/admin/graphql' )}</dt><dd>{if $cache.expiredAt}{$cache.expiredAt|l10n( 'shortdatetime' )}{else}&ndash;{/if}</dd>
        </dl>
    </div>
    {if $can_administrate}
    <form method="post" action={'graphql/playground'|ezurl} class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="ClearCacheButton" value="1">{'Clear the response cache'|i18n( 'design/admin/graphql' )}</button>
    </form>
    {/if}
</section>
</div>

<form method="post" action={'graphql/playground'|ezurl}>
<section class="a4-card is-flush a4-section" aria-labelledby="graphql-persisted">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-persisted">{'Persisted queries'|i18n( 'design/admin/graphql' )}</h2>
        <p class="a4-meta">{'Stored with ./console exp:graphql:persist, or by clients (automatic persisted queries); called by name or SHA-256.'|i18n( 'design/admin/graphql' )}</p></div>
    {if $persisted|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="graphql-persisted" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Names'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">SHA-256</th>
            <th scope="col">{'Size'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'Where'|i18n( 'design/admin/graphql' )}</th>
            {if $can_administrate}<th scope="col"><span class="a4-sr-only">{'Remove'|i18n( 'design/admin/graphql' )}</span></th>{/if}
        </tr></thead>
        <tbody>
        {foreach $persisted as $q}
        <tr>
            <td>{if $q.names}{$q.names|wash}{else}<span class="a4-muted">&ndash;</span>{/if}</td>
            <td><code class="a4-code" title="{$q.hash|wash}">{$q.hash|shorten( 16, '…' )|wash}</code></td>
            <td>{'%bytes bytes'|i18n( 'design/admin/graphql',, hash( '%bytes', $q.bytes ) )|wash}</td>
            <td>{if $q.shipped}<span class="a4-badge is-info">{'shipped'|i18n( 'design/admin/graphql' )}</span>{else}<span class="a4-badge">{'stored'|i18n( 'design/admin/graphql' )}</span>{/if}</td>
            {if $can_administrate}
            <td class="a4-actions">{if $q.shipped|not}
                <details class="a4-confirm">
                    <summary>{'Remove'|i18n( 'design/admin/graphql' )}</summary>
                    <div>
                        <label class="a4-check"><input type="checkbox" name="ConfirmRemovePersisted[{$q.hash|wash}]" value="1" /> {'Yes, remove this query'|i18n( 'design/admin/graphql' )}</label>
                        <button type="submit" class="a4-btn a4-btn-small a4-btn-danger" name="RemovePersistedButton[{$q.hash|wash}]" value="1">{'Remove'|i18n( 'design/admin/graphql' )}</button>
                    </div>
                </details>{/if}</td>
            {/if}
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No persisted query yet.'|i18n( 'design/admin/graphql' )}</p></div>
    {/if}
</section>
</form>

<section class="a4-card is-flush a4-section" aria-labelledby="graphql-keys">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-keys">{'API keys'|i18n( 'design/admin/graphql' )}</h2>
        <p class="a4-meta">{'Personal API keys with the scope graphql:read (queries) or graphql:write (mutations). Make and revoke them on the page API access, or with ./console exp:graphql:keys.'|i18n( 'design/admin/graphql' )}
            <a href={'apikey/list'|ezurl}>{'API access'|i18n( 'design/admin/graphql' )}</a>{if $can_administrate} &middot; <a href={'oauthadmin/keys'|ezurl}>{'All keys'|i18n( 'design/admin/graphql' )}</a>{/if}</p></div>
    {if $keys|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="graphql-keys" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Key'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'Name'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'Owner'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'Scopes'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/graphql' )}</th>
            <th scope="col">{'Last used'|i18n( 'design/admin/graphql' )}</th>
        </tr></thead>
        <tbody>
        {foreach $keys as $k}
        <tr>
            <td><code class="a4-code">{$k.prefix|wash}</code></td>
            <td>{$k.name|wash}</td>
            <td>{$k.owner|wash}</td>
            <td>{foreach $k.scopes as $scope}<span class="a4-tag">{$scope|wash}</span> {/foreach}</td>
            <td>{if eq( $k.status, 'active' )}<span class="a4-badge is-ok">{'active'|i18n( 'design/admin/graphql' )}</span>{elseif eq( $k.status, 'expired' )}<span class="a4-badge is-warn">{'expired'|i18n( 'design/admin/graphql' )}</span>{else}<span class="a4-badge is-bad">{'revoked'|i18n( 'design/admin/graphql' )}</span>{/if}</td>
            <td>{if $k.lastUsed}{$k.lastUsed|l10n( 'shortdatetime' )}{else}<span class="a4-muted">{'never'|i18n( 'design/admin/graphql' )}</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No API key with a GraphQL scope yet.'|i18n( 'design/admin/graphql' )}</p></div>
    {/if}
</section>

{if and( $enabled, $library )}
<section class="a4-card a4-section" aria-labelledby="graphql-play">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-play">{'Query'|i18n( 'design/admin/graphql' )}</h2></div>
    <form class="a4-card-body a4-graphql-play" method="post" action={'graphql/query'|ezurl} id="exp-graphql-form"
          data-endpoint="{$session_endpoint|wash}" data-running="{'Running…'|i18n( 'design/admin/graphql' )|wash}"
          data-unreadable="{'The answer could not be read.'|i18n( 'design/admin/graphql' )|wash}">
        <div class="a4-stack">
            <div class="a4-field">
                <label class="a4-label" for="exp-graphql-query">{'Query'|i18n( 'design/admin/graphql' )}</label>
                <textarea id="exp-graphql-query" class="a4-graphql-query" name="query" spellcheck="false" autocapitalize="off">{$example|wash}</textarea>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="exp-graphql-variables">{'Variables (JSON)'|i18n( 'design/admin/graphql' )}</label>
                <textarea id="exp-graphql-variables" class="a4-graphql-variables" name="variables" spellcheck="false" autocapitalize="off"></textarea>
            </div>
            <div class="a4-actions">
                <button type="submit" class="a4-btn a4-btn-primary" id="exp-graphql-run">{'Run'|i18n( 'design/admin/graphql' )}</button>
                <span class="a4-meta">{'The query runs with your rights, signed in with this session.'|i18n( 'design/admin/graphql' )}</span>
            </div>
        </div>
        <div class="a4-stack">
            <span class="a4-label" id="exp-graphql-answer-label">{'Answer'|i18n( 'design/admin/graphql' )}</span>
            <pre class="a4-pre is-tall a4-graphql-answer" id="exp-graphql-answer" aria-labelledby="exp-graphql-answer-label" aria-live="polite"></pre>
        </div>
    </form>
</section>
{ezscript( array( 'graphql_playground.js' ) )}
{/if}

</div>
