{* The graphql/playground view: Setup > GraphQL. Whether the API answers here, its end points, the library, the
   schema and a playground that posts the query to the session end point graphql/query (graphql_playground.js).
   Variables: enabled, library, library_version, endpoint, session_endpoint, schema (hash, classes), example. *}
<div class="a4-page a4-graphql" id="exp-graphql-playground">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'GraphQL'|i18n( 'design/admin/graphql' )}</h1>
        <p class="a4-pagehead-sub">{'Ask the content of this site for exactly the fields a page needs, in one request. The schema follows the content classes; every query runs with the rights of the user who sends it.'|i18n( 'design/admin/graphql' )}</p>
    </div>
</div>

{if $enabled|not}
<div class="a4-notice is-warn" role="status"><p>{'The GraphQL API is switched off in this siteaccess. Switch it on in graphql.ini: [GraphQLSettings] Enabled=enabled.'|i18n( 'design/admin/graphql' )}</p></div>
{elseif $library|not}
<div class="a4-notice is-bad" role="alert"><p>{'The GraphQL library (webonyx/graphql-php 15) is not installed.'|i18n( 'design/admin/graphql' )}</p></div>
{/if}

<section class="a4-card a4-section" aria-labelledby="graphql-facts">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="graphql-facts">{'End point'|i18n( 'design/admin/graphql' )}</h2></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
            <dt>{'End point'|i18n( 'design/admin/graphql' )}</dt><dd><code class="a4-code">{$endpoint|wash}</code></dd>
            <dt>{'Library'|i18n( 'design/admin/graphql' )}</dt><dd>{if $library}webonyx/graphql-php {$library_version|wash}{else}&ndash;{/if}</dd>
            <dt>{'Schema'|i18n( 'design/admin/graphql' )}</dt><dd>{if $schema.hash}{'%count content classes, schema %hash'|i18n( 'design/admin/graphql',, hash( '%count', $schema.classes, '%hash', $schema.hash ) )|wash}{else}&ndash;{/if}</dd>
        </dl>
    </div>
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
