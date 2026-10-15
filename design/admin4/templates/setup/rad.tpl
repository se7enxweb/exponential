{* The RAD tools.

   Every point this system can be extended at is listed, whether or not there is
   a tool for it: what it is for, where the code goes, and what registers it -
   which is the part that is otherwise only discoverable by reading the kernel.
   A point with no tool is still worth knowing about. *}


<div class="context-block a4-page a4-setup-rad">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Rapid Application Development Tools'|i18n( 'design/admin/setup/rad' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'Every point this system can be extended at is listed below: what it is for, where the code goes, and what registers it. Where there is a tool it opens from here; where there is not, what is written here is what you would otherwise have to find by reading the kernel.'|i18n( 'design/admin/setup/rad' )}</p>
</div>

<div class="a4-setup-wz-summary">
    <span><b>{$rad_coverage.total}</b> {'extension points written up'|i18n( 'design/admin/setup/rad' )}</span>
    <span><b>{$rad_coverage.covered}</b> {'with a tool'|i18n( 'design/admin/setup/rad' )}</span>
    <span class="a4-meta">{'The rest are documented, and each is a tool waiting to be written.'|i18n( 'design/admin/setup/rad' )}</span>
{if ne( $rad_filter, 'all' )}
    <span class="a4-meta">{'Showing %shown of them.'|i18n( 'design/admin/setup/rad',, hash( '%shown', $rad_shown ) )}</span>
{/if}
</div>

{* The written list is the map. The survey is the territory, and it is a good
   deal larger than the map - which is worth saying on the page rather than
   leaving somebody to think forty odd points is the size of this system. *}
<div class="a4-setup-rad-survey-note">
<a class="a4-setup-rad-survey-link" href={'setup/radsurvey'|ezurl}>
    <span class="a4-setup-rad-survey-count">{$rad_survey.counts.total}</span>
    <span>
        <b>{'Extension point survey'|i18n( 'design/admin/setup/rad' )}</b>
        <span class="a4-meta">{'The list above is written by hand. This one is read off disk on every request: %settings settings that name a class across %ini ini files, %views module views, %repositories directories searched for handlers, and %contracts interfaces waiting to be implemented.'|i18n( 'design/admin/setup/rad',, hash( '%settings', $rad_survey.counts.settings, '%ini', $rad_survey.counts.ini, '%views', $rad_survey.counts.views, '%repositories', $rad_survey.counts.repositories, '%contracts', $rad_survey.counts.contracts ) )}</span>
        <span class="a4-meta">{'%runnables commands, cronjob parts and views are classes a subclass named in site.ini can replace.'|i18n( 'design/admin/setup/rad',, hash( '%runnables', $rad_survey.counts.runnables ) )}</span>
        <span class="a4-meta">{'exp:ini has %actions actions and %providers scope providers; an extension adds its own in ini.ini.'|i18n( 'design/admin/setup/rad',, hash( '%actions', $rad_survey.counts.ini_actions, '%providers', $rad_survey.counts.ini_scope_providers ) )}</span>
        <span class="a4-meta">{'%registries registries are declared in rad.ini by %origins sources (the kernel and extensions), with %entries entries. %limitations policy limitations give the values the role editor offers.'|i18n( 'design/admin/setup/rad',, hash( '%registries', $rad_survey.counts.registries_declared, '%origins', $rad_survey.counts.registries_declared_by, '%entries', $rad_survey.counts.registries_declared_entries, '%limitations', $rad_survey.counts.limitations ) )}</span>
        {if $rad_survey.counts.registries_broken|gt( 0 )}<span class="a4-meta">{'%broken registry entries cannot work and are shown as broken in the survey.'|i18n( 'design/admin/setup/rad',, hash( '%broken', $rad_survey.counts.registries_broken ) )}</span>{/if}
    </span>
</a>
<ul class="a4-tabs a4-setup-rad-survey-groups">
{foreach $rad_survey_groups as $rad_count}
    <li{if $rad_count.in_total|not} class="is-inside"{/if}><a href={concat( 'setup/radsurvey/(show)/', $rad_count.section )|ezurl}{if and( is_set( $rad_count.description ), $rad_count.description )} title="{$rad_count.description|wash}"{/if}><b>{$rad_count.count}</b> {$rad_count.label|i18n( 'design/admin/setup/rad' )|wash}{if and( $rad_count.in_total|not, $rad_count.counted|gt( 0 ) )} <span class="a4-meta">({'%counted of them counted as settings'|i18n( 'design/admin/setup/rad',, hash( '%counted', $rad_count.counted ) )})</span>{/if}</a></li>
{/foreach}
</ul>
</div>

{* Narrowing as you type. Everything is already on the page, so this hides
   cards rather than fetching anything - which means it also works on a filtered
   list and needs no address of its own. With javascript off the box never
   appears and the links below still do the job. *}
<div class="a4-searchrow a4-setup-rad-find" id="radFind" hidden>
    <input type="text" class="a4-input" id="radFindInput" autocomplete="off" aria-label="{'Find'|i18n( 'design/admin/setup/rad' )|wash}"
           placeholder="{'Type to narrow: a name, a setting, a class, a file'|i18n( 'design/admin/setup/rad' )}" />
    <button type="button" class="a4-btn a4-btn-small a4-btn-icon" id="radFindClear" title="{'Clear'|i18n( 'design/admin/setup/rad' )}">&times;</button>
    <span class="a4-setup-rad-find-count a4-meta" id="radFindCount" aria-live="polite" data-text="{'%shown of %count'|i18n( 'design/admin/setup/rad' )|wash}"></span>
</div>

{* Which of them to list. Links rather than a script, so a filtered list can be
   linked to, comes back the same, and works with javascript switched off. *}
<div class="context-toolbar a4-setup-rad-filter"><div class="button-left">
<p class="table-preferences">
{foreach $rad_filters as $rad_option}
    {if $rad_option.current}<span class="current">{$rad_option.label|i18n( 'design/admin/setup/rad' )|wash} ({$rad_option.count})</span>
    {else}<a href={$rad_option.url|ezurl}>{$rad_option.label|i18n( 'design/admin/setup/rad' )|wash} ({$rad_option.count})</a>{/if}
{/foreach}
</p>
</div></div>

<div class="a4-setup-rad-groups">

{if $rad_groups|count|eq( 0 )}
<div class="block"><p>{'Nothing matches that.'|i18n( 'design/admin/setup/rad' )}</p></div>
{/if}

{foreach $rad_groups as $rad_group}
<div class="a4-setup-rad-group">
    <h2>{$rad_group.title|i18n( 'design/admin/setup/rad' )|wash}</h2>
    <span class="a4-meta">{$rad_group.description|i18n( 'design/admin/setup/rad' )|wash}</span>

    {foreach $rad_group.points as $rad_point}
    <div class="a4-card a4-setup-rad-point{if $rad_point.tool} is-ok{/if}">
        <div class="a4-title-row">
            <h3>{if $rad_point.tool}<a class="a4-setup-rad-open" href={concat( '/', $rad_point.tool )|ezurl}>{$rad_point.title|i18n( 'design/admin/setup/rad' )|wash}</a>{else}{$rad_point.title|i18n( 'design/admin/setup/rad' )|wash}{/if}</h3>
            {if $rad_point.tool}
            <span class="a4-tag is-ok">{'tool available'|i18n( 'design/admin/setup/rad' )}</span>
            {else}
            <span class="a4-tag">{'no tool yet'|i18n( 'design/admin/setup/rad' )}</span>
            {/if}
            <span class="a4-tag">{$rad_point.mechanism|wash}</span>
        </div>

        <div class="a4-setup-rad-point-what">{$rad_point.what|i18n( 'design/admin/setup/rad' )|wash}</div>

        <div class="a4-setup-wz-facts">
            <div class="a4-setup-wz-fact"><b>{'Code'|i18n( 'design/admin/setup/rad' )}</b><span><code>{$rad_point.where|wash}</code></span></div>
            <div class="a4-setup-wz-fact"><b>{'Registered by'|i18n( 'design/admin/setup/rad' )}</b><span>{$rad_point.register|wash}</span></div>
            <div class="a4-setup-wz-fact"><b>{'Contract'|i18n( 'design/admin/setup/rad' )}</b><span><code>{$rad_point.contract|wash}</code></span></div>
            <div class="a4-setup-wz-fact"><b>{'Kernel'|i18n( 'design/admin/setup/rad' )}</b><span class="a4-meta"><code>{$rad_point.source|wash}</code></span></div>
        </div>
    </div>
    {/foreach}
</div>
{/foreach}

</div>

<div class="a4-card is-soft a4-setup-rad-legend">
<h2>{'How things are registered'|i18n( 'design/admin/setup/rad' )}</h2>
<dl>
{foreach $rad_mechanisms as $rad_key => $rad_text}
    <dt>{$rad_key|wash}</dt>
    <dd>{$rad_text|i18n( 'design/admin/setup/rad' )|wash}</dd>
{/foreach}
</dl>
</div>

<p class="a4-meta a4-setup-rad-find-none" id="radFindNone" hidden>{'Nothing here matches that. The survey beside this list is larger and searchable too.'|i18n( 'design/admin/setup/rad' )}</p>

{* DESIGN: Content END *}</div></div></div>

</div>

{literal}
<script type="text/javascript">
( function () {
    var bar   = document.getElementById( 'radFind' ),
        input = document.getElementById( 'radFindInput' ),
        clear = document.getElementById( 'radFindClear' ),
        count = document.getElementById( 'radFindCount' ),
        none  = document.getElementById( 'radFindNone' );

    if ( !bar || !input ) return;

    // Collect the cards, and the text of each one, once. Reading textContent
    // per keystroke on sixty cards is wasteful and it never changes.
    // Whole class name only. a4-title-row and a4-setup-rad-point-what are children of
    // a card, and matching on a prefix would pick those up and hide the inside
    // of every card rather than the card.
    function hasClass( node, wanted )
    {
        return ( ' ' + ( node.className || '' ) + ' ' ).indexOf( ' ' + wanted + ' ' ) !== -1;
    }

    var cards = [], groups = [], i, all = document.getElementsByTagName( 'div' );

    for ( i = 0; i < all.length; i++ )
    {
        if ( hasClass( all[i], 'a4-setup-rad-point' ) )
            cards.push( { node: all[i], text: ( all[i].textContent || all[i].innerText || '' ).toLowerCase() } );
        else if ( hasClass( all[i], 'a4-setup-rad-group' ) )
            groups.push( all[i] );
    }

    if ( !cards.length ) return;

    // Only shown once it is known to work, so a browser that got this far is
    // the only one offered a box that does something.
    bar.hidden = false;

    function show( node, on )
    {
        var name = ( node.className || '' ).replace( / ?\bis-hidden\b/, '' );
        node.className = on ? name : name + ' is-hidden';
    }

    // Every word has to match, in any order and anywhere in the card. That is
    // what lets "cluster handler" and "handler cluster" both find the same
    // thing, which is how people actually type.
    function matches( text, words )
    {
        for ( var w = 0; w < words.length; w++ )
            if ( text.indexOf( words[w] ) === -1 )
                return false;

        return true;
    }

    function run()
    {
        var query = input.value.toLowerCase().replace( /^\s+|\s+$/g, '' ),
            words = query === '' ? [] : query.split( /\s+/ ),
            shown = 0, c, g;

        for ( c = 0; c < cards.length; c++ )
        {
            var on = words.length === 0 || matches( cards[c].text, words );
            show( cards[c].node, on );
            if ( on ) shown++;
        }

        // A group with nothing left in it goes too, or the page is a list of
        // headings with nothing under them.
        for ( g = 0; g < groups.length; g++ )
        {
            var points = groups[g].getElementsByTagName( 'div' ), any = false, p;

            for ( p = 0; p < points.length; p++ )
                if ( hasClass( points[p], 'a4-setup-rad-point' ) && !hasClass( points[p], 'is-hidden' ) )
                {
                    any = true;
                    break;
                }

            show( groups[g], any );
        }

        count.textContent = words.length === 0
                            ? ''
                            : ( count.getAttribute( 'data-text' ) || '%shown / %count' ).split( '%shown' ).join( shown ).split( '%count' ).join( cards.length );

        if ( none ) none.hidden = !( words.length && shown === 0 );
    }

    // input: typing, pasting, cutting and dictation alike, not only a key let go
    input.addEventListener( 'input', run );

    if ( clear )
        clear.onclick = function () { input.value = ''; run(); input.focus(); return false; };

    // Esc clears rather than leaving somebody with a filtered page and no
    // obvious way back to all of it.
    input.onkeydown = function ( event ) {
        if ( ( event || window.event ).keyCode === 27 ) { input.value = ''; run(); }
    };

    run();
} )();
</script>
{/literal}
