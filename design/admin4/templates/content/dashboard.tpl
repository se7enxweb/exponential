{* admin4 dashboard. Everything the admin dashboard had is here: the installation's version and maintenance
   notice (dashboard/maintenance.tpl) and every block of dashboard.ini [DashboardSettings] DashboardBlocks, in
   their priority order, each through its own template (Template= or dashboard/<identifier>.tpl) -- an
   extension's block included. Around them: a welcome with quick actions, key figures, the last 14 days of
   publishing and the system at a glance. Read-only fetches, cheap counts. *}
{* set scope=global persistent_variable=hash('extra_menu', false()) *}
{def $user        = fetch( 'user', 'current_user' )
     $root_node   = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $media_node  = ezini( 'NodeSettings', 'MediaRootNode', 'content.ini' )
     $users_node  = ezini( 'NodeSettings', 'UserRootNode', 'content.ini' )
     $extensions  = ezini( 'ExtensionSettings', 'ActiveExtensions' )
     $hour        = currentdate()|datetime( 'custom', '%H' )|int
     $day_start   = maketime( 0, 0, 0, currentdate()|datetime( 'custom', '%n' ), currentdate()|datetime( 'custom', '%j' ), currentdate()|datetime( 'custom', '%Y' ) )
     $greeting    = 'Good evening'|i18n( 'design/admin/dashboard' )}
{if and( ge( $hour, 5 ), lt( $hour, 12 ) )}{set $greeting = 'Good morning'|i18n( 'design/admin/dashboard' )}
{elseif and( ge( $hour, 12 ), lt( $hour, 18 ) )}{set $greeting = 'Good afternoon'|i18n( 'design/admin/dashboard' )}{/if}

{* Key figures *}
{def $count_content   = fetch( 'content', 'tree_count', hash( 'parent_node_id', $root_node ) )
     $count_week      = fetch( 'content', 'tree_count', hash( 'parent_node_id', 1, 'attribute_filter', array( array( 'published', '>=', sub( $day_start, mul( 6, 86400 ) ) ) ) ) )
     $count_drafts    = fetch( 'content', 'draft_count' )
     $count_pending   = fetch( 'content', 'pending_count' )
     $count_users     = fetch( 'content', 'tree_count', hash( 'parent_node_id', $users_node, 'class_filter_type', 'include', 'class_filter_array', array( 'user' ) ) )
     $count_online    = fetch( 'user', 'logged_in_count' )
     $count_media     = fetch( 'content', 'tree_count', hash( 'parent_node_id', $media_node ) )
     $count_trash     = fetch( 'content', 'trash_count' )}

{* The last 14 days of publishing, oldest first *}
{def $days = array() $day_max = 1 $day_from = 0 $day_count = 0}
{for 13 to 0 as $i}
    {set $day_from = sub( $day_start, mul( $i, 86400 ) )}
    {set $day_count = fetch( 'content', 'tree_count', hash( 'parent_node_id', 1, 'attribute_filter', array( array( 'published', 'between', array( $day_from, sum( $day_from, 86399 ) ) ) ) ) )}
    {set $days = $days|append( hash( 'time', $day_from, 'count', $day_count ) )}
    {if gt( $day_count, $day_max )}{set $day_max = $day_count}{/if}
{/for}

<div class="context-block content-dashboard a4-dashboard">

    {* ---- Welcome ---- *}
    <section class="a4-dash-hero">
        <div class="a4-dash-hero-text">
            <p class="a4-dash-date">{currentdate()|l10n( 'date' )}</p>
            <h1>{$greeting}, {$user.contentobject.name|wash}</h1>
            <p class="a4-dash-sub">{'Here is what is happening on %site.'|i18n( 'design/admin/dashboard', , hash( '%site', ezini( 'SiteSettings', 'SiteName' )|wash ) )}</p>
        </div>
        <nav class="a4-dash-actions" aria-label="{'Quick actions'|i18n( 'design/admin/dashboard' )|wash}">
            <a class="a4-dash-action a4-primary" href={concat( 'content/view/full/', $root_node )|ezurl}><span class="a4-dash-ico" aria-hidden="true">&#9776;</span>{'Content structure'|i18n( 'design/admin/dashboard' )}</a>
            <a class="a4-dash-action" href={concat( 'content/view/full/', $media_node )|ezurl}><span class="a4-dash-ico" aria-hidden="true">&#9635;</span>{'Media library'|i18n( 'design/admin/dashboard' )}</a>
            {if $extensions|contains( 'ezmultiupload' )}
            <a class="a4-dash-action" href={concat( 'ezmultiupload/upload/', $media_node )|ezurl}><span class="a4-dash-ico" aria-hidden="true">&#8679;</span>{'Upload files'|i18n( 'design/admin/dashboard' )}</a>
            {/if}
            <a class="a4-dash-action" href={concat( 'content/view/full/', $users_node )|ezurl}><span class="a4-dash-ico" aria-hidden="true">&#9787;</span>{'Users'|i18n( 'design/admin/dashboard' )}</a>
            {if $extensions|contains( 'eztags' )}
            <a class="a4-dash-action" href={'tags/dashboard'|ezurl}><span class="a4-dash-ico" aria-hidden="true">#</span>{'Tags'|i18n( 'design/admin/dashboard' )}</a>
            {/if}
            {if $extensions|contains( 'explayouts_ui' )}
            <a class="a4-dash-action" href={'explayouts_ui/dashboard'|ezurl}><span class="a4-dash-ico" aria-hidden="true">&#9638;</span>{'Layouts'|i18n( 'design/admin/dashboard' )}</a>
            {/if}
            {if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'administrate' ) )}
            <a class="a4-dash-action" href={'setup/cache'|ezurl}><span class="a4-dash-ico" aria-hidden="true">&#8635;</span>{'Caches'|i18n( 'design/admin/dashboard' )}</a>
            {/if}
        </nav>
    </section>

    {* ---- Key figures ---- *}
    <section class="a4-dash-figures" aria-label="{'Key figures'|i18n( 'design/admin/dashboard' )|wash}">
        <a class="a4-dash-figure" href={concat( 'content/view/full/', $root_node )|ezurl}><strong>{$count_content}</strong><span>{'Content items'|i18n( 'design/admin/dashboard' )}</span></a>
        <div class="a4-dash-figure a4-accent"><strong>{$count_week}</strong><span>{'Published in the last 7 days'|i18n( 'design/admin/dashboard' )}</span></div>
        <a class="a4-dash-figure" href={'content/draft'|ezurl}><strong>{$count_drafts}</strong><span>{'My drafts'|i18n( 'design/admin/dashboard' )}</span></a>
        <a class="a4-dash-figure" href={'content/pendinglist'|ezurl}><strong>{$count_pending}</strong><span>{'My pending items'|i18n( 'design/admin/dashboard' )}</span></a>
        <a class="a4-dash-figure" href={concat( 'content/view/full/', $users_node )|ezurl}><strong>{$count_users}</strong><span>{'Users'|i18n( 'design/admin/dashboard' )}</span></a>
        <div class="a4-dash-figure"><strong>{$count_online}</strong><span>{'Signed in now'|i18n( 'design/admin/dashboard' )}</span></div>
        <a class="a4-dash-figure" href={concat( 'content/view/full/', $media_node )|ezurl}><strong>{$count_media}</strong><span>{'Media items'|i18n( 'design/admin/dashboard' )}</span></a>
        <a class="a4-dash-figure" href={'content/trash'|ezurl}><strong>{$count_trash}</strong><span>{'In the trash'|i18n( 'design/admin/dashboard' )}</span></a>
    </section>

    <div class="a4-dash-row">
        {* ---- Activity ---- *}
        <section class="a4-dash-card a4-dash-activity">
            <header><h2>{'Publishing, last 14 days'|i18n( 'design/admin/dashboard' )}</h2></header>
            <div class="a4-dash-bars" role="img" aria-label="{'Content published per day over the last 14 days'|i18n( 'design/admin/dashboard' )|wash}">
                {foreach $days as $d}
                <div class="a4-dash-bar" title="{$d.time|l10n( 'shortdate' )}: {$d.count}">
                    <span class="a4-dash-bar-value">{if gt( $d.count, 0 )}{$d.count}{/if}</span>
                    <span class="a4-dash-bar-fill" style="height: {if gt( $d.count, 0 )}{max( 4, div( mul( $d.count, 100 ), $day_max )|round )}{else}0{/if}%"></span>
                    <span class="a4-dash-bar-day">{$d.time|datetime( 'custom', '%j' )}</span>
                </div>
                {/foreach}
            </div>
        </section>

        {* ---- System ---- *}
        <section class="a4-dash-card a4-dash-system">
            <header><h2>{'System'|i18n( 'design/admin/dashboard' )}</h2></header>
            {include uri='design:dashboard/maintenance.tpl'}
            <dl class="a4-dash-facts">
                <dt>{'Version'|i18n( 'design/admin/dashboard' )}</dt><dd>{fetch( 'setup', 'version' )}</dd>
                <dt>{'Database'|i18n( 'design/admin/dashboard' )}</dt><dd>{ezini( 'DatabaseSettings', 'DatabaseImplementation' )|wash}</dd>
                <dt>{'Extensions'|i18n( 'design/admin/dashboard' )}</dt><dd>{$extensions|count}</dd>
            </dl>
            {if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'administrate' ) )}
            <p class="a4-dash-links">
                <a href={'setup/info'|ezurl}>{'System information'|i18n( 'design/admin/dashboard' )}</a>
                <a href={'setup/cache'|ezurl}>{'Caches'|i18n( 'design/admin/dashboard' )}</a>
                <a href={'setup/systemupgrade'|ezurl}>{'Upgrade check'|i18n( 'design/admin/dashboard' )}</a>
            </p>
            {/if}
        </section>
    </div>

    {* ---- The configured blocks (dashboard.ini), each in a card ---- *}
    <div class="a4-dash-blocks">
    {foreach $blocks as $block}
        <section class="a4-dash-card dashboard-item a4-dash-block-{$block.identifier|wash}">
            {if $block.template}
                {include uri=concat( 'design:', $block.template )}
            {else}
                {include uri=concat( 'design:dashboard/', $block.identifier, '.tpl' )}
            {/if}
        </section>
    {/foreach}
    </div>

</div>
{undef}
