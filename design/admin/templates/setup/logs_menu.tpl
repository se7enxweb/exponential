{* The left menu of the log viewer (setup/logs, setup/logsaction; doc/features/6.0/log-viewer.md, "The navigator"):
   the Setup menu, then every log the viewer may open, grouped Kernel, Site, Extensions, Web server and Rotated
   archives, each with its size, its last change and the lines a minute it gains (from fetch( 'setup', 'log_files' ),
   which is empty for a user without setup/logs). Each link is the log's permalink, setup/logs/view/<key>[/(archive)/N].
   The open file's link carries aria-current="page". A log with a label (LogLabel[<key>]) shows it, with the file name below.

   The same file is in design/admin and design/admin4. Styles: stylesheets/modules/setup.css (.a4-setup-lognav). *}
{include uri='design:parts/setup/menu.tpl'}

{* The other pages of the log viewer, for a user who may read the logs *}
{if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'logs' ) )}
<nav class="a4-setup-lognav a4-setup-lognav-tools" aria-labelledby="a4-setup-logtools-title">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h4 id="a4-setup-logtools-title">{'Log tools'|i18n( 'design/admin/setup/logs' )}</h4>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">
<ul class="leftmenu-items">
    <li><div><a href={'setup/logs'|ezurl}>{'Log files'|i18n( 'design/admin/setup/logs' )}</a></div></li>
    {if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managelogs' ) )}<li><div><a href={'setup/logsmanage'|ezurl}>{'Manage logs'|i18n( 'design/admin/setup/logs' )}</a></div></li>{/if}
    <li><div><a href={'setup/logshealth'|ezurl}>{'Log health'|i18n( 'design/admin/setup/logs' )}</a></div></li>
    <li><div><a href={'setup/logsdigest'|ezurl}>{'Error digest'|i18n( 'design/admin/setup/logs' )}</a></div></li>
</ul>
{* DESIGN: Content END *}</div></div></div>
</nav>
{/if}

{def $lognav_groups = fetch( 'setup', 'log_files' )
     $lognav_current = ''
     $lognav_names = hash( 'kernel',     'Kernel'|i18n( 'design/admin/setup/logs' ),
                           'site',       'Site'|i18n( 'design/admin/setup/logs' ),
                           'extensions', 'Extensions'|i18n( 'design/admin/setup/logs' ),
                           'webserver',  'Web server'|i18n( 'design/admin/setup/logs' ),
                           'archives',   'Rotated archives'|i18n( 'design/admin/setup/logs' ) )}
{if is_set( $log_file.id )}{set $lognav_current = $log_file.id}{/if}
{if $lognav_groups|count}
<nav class="a4-setup-lognav" aria-labelledby="a4-setup-lognav-title">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h4 id="a4-setup-lognav-title">{'Log files'|i18n( 'design/admin/setup/logs' )}</h4>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">
{foreach $lognav_groups as $lognav_group => $lognav_files}
    {if $lognav_files|count|gt( 0 )}
    <details class="a4-navgroup a4-setup-lognav-group" open>
        <summary class="a4-nav-title">{first_set( $lognav_names[ $lognav_group ], $lognav_group )|wash} <span class="a4-setup-lognav-count">({$lognav_files|count})</span></summary>
        <ul class="leftmenu-items a4-setup-lognav-items">
        {foreach $lognav_files as $lognav_file}
            <li{if $lognav_file.id|eq( $lognav_current )} class="current"{/if}><div>
                {if $lognav_file.readable}
                <a href={concat( 'setup/logs/view/', $lognav_file.key, cond( $lognav_file.rotation|ne( 0 ), concat( '/(archive)/', $lognav_file.rotation ), '' ) )|ezurl}{if $lognav_file.id|eq( $lognav_current )} aria-current="page"{/if} class="a4-setup-lognav-link" data-level="{$lognav_file.level|wash}">
                {else}
                <span class="disabled a4-setup-lognav-link" title="{'The web server may not read this file.'|i18n( 'design/admin/setup/logs' )|wash}">
                {/if}
                    <span class="a4-setup-lognav-name">{if and( $lognav_file.labeled, $lognav_file.archive|not )}{$lognav_file.label|wash}{else}{$lognav_file.name|wash}{/if}</span>
                    {if and( $lognav_file.labeled, $lognav_file.archive|not )}<span class="a4-setup-lognav-dir">{$lognav_file.name|wash}</span>
                    {elseif and( $lognav_file.configured|not, or( $lognav_group|eq( 'site' ), $lognav_group|eq( 'archives' ) ) )}<span class="a4-setup-lognav-dir">{$lognav_file.dir|wash}</span>{/if}
                    <span class="a4-setup-lognav-meta">
                        <span>{$lognav_file.size|si( byte )}</span>
                        <span><time datetime="{$lognav_file.mtime|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}">{$lognav_file.mtime|l10n( 'shortdatetime' )}</time></span>
                        {if $lognav_file.archive|not}
                        <span>{if $lognav_file.rate|is_null}{'rate: measuring'|i18n( 'design/admin/setup/logs' )}{else}{'%rate lines/min'|i18n( 'design/admin/setup/logs',, hash( '%rate', $lognav_file.rate ) )}{/if}</span>
                        {/if}
                    </span>
                {if $lognav_file.readable}</a>{else}</span>{/if}
            </div></li>
        {/foreach}
        </ul>
    </details>
    {/if}
{/foreach}
{* DESIGN: Content END *}</div></div></div>
</nav>
{/if}
{undef $lognav_groups $lognav_current $lognav_names}
