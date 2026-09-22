<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'System information'|i18n( 'design/admin/setup/info' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="context-attributes">

<table class="list" cellspacing="0">

<tr>
    <th><label>{'Exponential'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'Site'|i18n( 'design/admin/setup/info' )}:</label>
        {ezini('SiteSettings','SiteURL')}
    </div>

    <div class="block">
        <label>{'Version'|i18n( 'design/admin/setup/info', 'Exponential version' )}:</label>
        {$ezpublish_version}
    </div>

    <div class="block">
        <label>{'Extensions'|i18n( 'design/admin/setup/info', 'Exponential extensions' )}:</label>
        {if $ezpublish_extensions}
            {foreach $ezpublish_extensions as $extension}
                {$extension}{delimiter}, {/delimiter}
            {/foreach}
        {else}
            {'Not in use.'|i18n( 'design/admin/setup/info' )}
        {/if}
    </div>

</td>
</tr>
</table>
{* Where the running engine came from. Worth its own block rather than a line
   in Miscellaneous: when a kernel edit appears to do nothing, or a stack trace
   names a phar:// path, this is the first thing to read. *}
<table class="list" cellspacing="0">
<tr>
    <th><label>{'Engine'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    {* Say which one is running before anything else, and say why.
       The panel used to list an archive, its version and whether it matched the
       working tree without ever making clear that none of it was in use, so it
       read as though the archive might be running and might be stale. *}
    <div class="block">
        <label>{'Loaded from'|i18n( 'design/admin/setup/info' )}:</label>
        {if eq( $engine_info.source, 'archive' )}
            {'The archive'|i18n( 'design/admin/setup/info' )} &mdash; {$engine_info.archive|wash}
        {else}
            {'Individual files on disk'|i18n( 'design/admin/setup/info' )}
            &mdash;
            {'the archive below is not being used, because the EXP_ENGINE_PHAR environment variable is not set'|i18n( 'design/admin/setup/info' )}
        {/if}
    </div>

    <div class="block">
        <label>{'Installation root'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.root|wash}
    </div>

    {if eq( $engine_info.source, 'archive' )}
    <div class="block">
        <label>{'Archive'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.archive_files} {'files'|i18n( 'design/admin/setup/info' )},
        {$engine_info.archive_bytes} {'bytes'|i18n( 'design/admin/setup/info' )},
        {'built'|i18n( 'design/admin/setup/info' )} {$engine_info.archive_built|wash}
    </div>
    {else}
    <div class="block">
        <label>{'Archive on disk'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.archive|wash}
    </div>
    {/if}

    <div class="block">
        <label>{'Archive was built from'|i18n( 'design/admin/setup/info' )}:</label>
        {if $engine_info.version}{$engine_info.version|wash}{else}&ndash;{/if}
    </div>

    {* Only worth saying when it would change what somebody does. An archive
       that is not running cannot explain a surprising result, so the mismatch
       is framed as "rebuild before switching" rather than as a failure. *}
    <div class="block">
        <label>{'Archive is current'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.matches_repo|wash}
        {if and( ne( $engine_info.source, 'archive' ), ne( $engine_info.matches_repo, 'yes' ) )}
            <br /><small>{'Rebuild it before switching to it; nothing is running from it now.'|i18n( 'design/admin/setup/info' )}</small>
        {/if}
    </div>

    {* Two unrelated facts, which were on one line and read as one. The wrapper
       is off because letting any path-taking function reach inside an archive
       is a real hazard on a site that accepts image uploads; phar.readonly is
       a separate php.ini setting about whether an archive can be written. *}
    <div class="block">
        <label>{'Phar stream wrapper'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.phar_wrapper|wash}
        {if eq( $engine_info.phar_wrapper, 'unregistered' )}
            <br /><small>{'Deliberate: with it registered, a file that is both a valid image and a valid archive can be executed through a phar:// path.'|i18n( 'design/admin/setup/info' )}</small>
        {/if}
    </div>

    <div class="block">
        <label>{'Writing archives (phar.readonly)'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.phar_readonly|wash}
    </div>

    <div class="block">
        <label>{'Opcode cache'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.opcache|wash}
    </div>
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
    <th><label>{'PHP'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'Version'|i18n( 'design/admin/setup/info', 'PHP version' )}:</label>
        {$php_version} (<a href={'/setup/info/php'|ezurl}>{'Details'|i18n( 'design/admin/setup/info', 'Detailed PHP information' )}</a>)
    </div>

    <div class="block">
        <label>{'Extensions'|i18n( 'design/admin/setup/info', 'PHP extensions' )}:</label>
        {foreach $php_loaded_extensions as $loadedExtension}{$loadedExtension}{delimiter}, {/delimiter}{/foreach}
    </div>

    <div class="block">
    <label>{'Miscellaneous'|i18n( 'design/admin/setup/info' )}:</label>
        {if $php_ini.safe_mode}
            {'Safe mode is on.'|i18n( 'design/admin/setup/info' )}<br/>
        {else}
            {'Safe mode is off.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {if $php_ini.open_basedir}
            {'Basedir restriction is on and set to %1.'|i18n( 'design/admin/setup/info',, array( $php_ini.open_basedir ) )}<br/>
        {else}
            {'Basedir restriction is off.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {if $php_ini.register_globals}
            {'Global variable registration is on.'|i18n( 'design/admin/setup/info' )}<br/>
        {else}
            {'Global variable registration is off.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {if $php_ini.file_uploads}
            {'File uploading is enabled.'|i18n( 'design/admin/setup/info' )}<br/>
        {else}
            {'File uploading is disabled.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {'Maximum size of post data (text and files) is %1.'|i18n( 'design/admin/setup/info',, array( $php_ini.post_max_size ) )}<br/>
        {if and( is_set( $php_ini.memory_limit ), $php_ini.memory_limit )}
            {'Script memory limit is %1.'|i18n( 'design/admin/setup/info' ,,array( $php_ini.memory_limit ) )}<br/>
        {else}
            {'Script memory limit is unlimited.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {'Maximum execution time is %1 seconds.'|i18n( 'design/admin/setup/info',, array( $php_ini.max_execution_time ) )}<br/>
    </div>
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
<th><label>{'PHP Accelerator'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
{if $php_accelerator}

<div class="block">
<label>{'Name'|i18n( 'design/admin/setup/info', 'PHP Accelerator name' )}:</label>
{if $php_accelerator.url}<a href="{$php_accelerator.url|wash}">{/if}{$php_accelerator.name|wash}{if $php_accelerator.url}</a>{/if}
</div>

<div class="block">
    <label>{'Version'|i18n( 'design/admin/setup/info', 'PHP Accelerator version' )}:</label>
    {if $php_accelerator.version_string}
        {$php_accelerator.version_string|wash}
        {else}
        {'Version information could not be detected.'|i18n( 'design/admin/setup/info' )}
    {/if}
</div>

<div class="block">
    <label>{'Status'|i18n( 'design/admin/setup/info' ,'PHP Accelerator status')}:</label>
    {if $php_accelerator.enabled}
        {'Enabled.'|i18n( 'design/admin/setup/info' )}
    {else}
        {'Disabled.'|i18n( 'design/admin/setup/info' )}
    {/if}
</div>

{else}
<div class="block">
    {'A known and active PHP Accelerator could not be found.'|i18n( 'design/admin/setup/info' )}
</div>
{/if}
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
<th><label>{'PHP autoload functions'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
{if $autoload_functions}
    <ol>
    {foreach $autoload_functions as $key => $function}
        {if is_array( $function )}
            {if $function[0]|is_object()}
                {set $function = concat( $function[0]|get_class(), '::', $function[1] )}
            {else}
                {set $function=$function|implode( '::' )}
            {/if}
        {/if}
        <li>{$function}</li>
    {/foreach}
    </ol>
{/if}
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
    <th><label>{'Web server (software)'|i18n( 'design/admin/setup/info', 'Web server title' )}</label></th>
</tr>
<tr>
<td>
    {if $webserver_info}

    <div class="block">
        <label>{'Name'|i18n( 'design/admin/setup/info', 'Web server name')}:</label>
        {$webserver_info.name}
    </div>

    <div class="block">
        <label>{'Version'|i18n( 'design/admin/setup/info', 'Web server version')}:</label>
        {$webserver_info.version}
    </div>

    <div class="block">
    <label>{'Modules'|i18n( 'design/admin/setup/info', 'Web server modules')}:</label>
    {if $webserver_info.modules}
        {section loop=$webserver_info.modules}{$:item}{delimiter}, {/delimiter}{/section}
    {else}
        {'The modules of the web server could not be detected.'|i18n( 'design/admin/setup/info', 'Web server modules')}
    {/if}
    </div>

    {else}
        {'Exponential was unable to extract information from the web server.'|i18n( 'design/admin/setup/info' )}
    {/if}
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
    <th><label>{'Web server (hardware)'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'CPU'|i18n( 'design/admin/setup/info', 'CPU info' )}:</label>
        {$system_info.cpu_type} {if $system_info.cpu_speed|is_null|not}{$system_info.cpu_speed} MHz{/if}
    </div>

    <div class="block">
        <label>{'Memory'|i18n( 'design/admin/setup/info', 'Memory info' )}:</label>
        {$system_info.memory_size|si( byte )}
    </div>
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
<th><label>{'Database'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
<div class="block">
    <label>{'Type'|i18n( 'design/admin/setup/info', 'Database type' )}:</label>
    {$database_info}
</div>

<div class="block">
    <label>{'Server'|i18n( 'design/admin/setup/info', 'Database server' )}:</label>
    {$database_object.database_server}
</div>

<div class="block">
    <label>{'Socket path'|i18n( 'design/admin/setup/info', 'Database socket path' )}:</label>
    {if $database_object.database_socket_path}
        {$database_object.database_socket_path}
    {else}
        {'Not in use.'|i18n( 'design/admin/setup/info' )}
    {/if}
</div>

<div class="block">
    <label>{'Database name'|i18n( 'design/admin/setup/info', 'Database name' )}:</label>
    {$database_object.database_name}
</div>

<div class="block">
    <label>{'Connection retry count'|i18n( 'design/admin/setup/info', 'Database retry count' )}:</label>
    {$database_object.retry_count}
</div>

<div class="block">
    <label>{'Character set'|i18n( 'design/admin/setup/info', 'Database charset' )}:</label>
    {$database_charset|wash}{if $database_object.is_internal_charset} ({'Internal'|i18n( 'design/admin/setup/info' )}){/if}
</div>

</td>
</tr>
</table>
<table class="list" cellspacing="0">
<tr>
<th><label>{'Slave database (read only)'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
{if $database_object.use_slave_server}

    <div class="block">
        <label>{'Server'|i18n( 'design/admin/setup/info', 'Database server' )}:</label>
        {$database_object.slave_database_server}
    </div>

    <div class="block">
        <label>{'Database'|i18n( 'design/admin/setup/info', 'Database name' )}:</label>
        {$database_object.slave_database_name}
    </div>

{else}

    {'There is no slave database in use.'|i18n( 'design/admin/setup/info' )}

{/if}
</td>
</tr>
</table>


</div>

{* DESIGN: Content END *}</div></div></div>

</div>
