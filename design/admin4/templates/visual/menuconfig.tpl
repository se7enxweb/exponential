{* Setup > Menus (classic): visual/menuconfig.

   The menu.ini [SelectedMenu] settings of one siteaccess, which only the page layouts of the classic designs read
   (base, ezwebin, the demo and simple designs). The page says what they are, finds for every siteaccess whether its
   pages read them (expClassicMenuSiteAccessInspector), shows where the current value comes from and what saving
   writes, and saves the three settings into settings/siteaccess/<siteaccess>/menu.ini.append.php only.

   The same file is in design/admin and design/admin4. Form fields and buttons are those of the page before 6.0.15
   (CurrentSiteAccess, SelectCurrentSiteAccessButton, MenuType, StoreButton), and it works without javascript.
   Guide: doc/guides/classic-menu-settings.md *}
{include uri='design:visual/exp_style.tpl'}

{def $mc_info = first_set( $menu_selected_info, false() )
     $mc_current = first_set( $menu_current, false() )
     $mc_relevant_count = first_set( $menu_relevant_count, 0 )
     $mc_all_count = first_set( $menu_siteaccesses, array() )|count()
     $mc_other_count = sub( $mc_all_count, $mc_relevant_count )
     $mc_relevant = first_set( $menu_selected_info.relevant, false() )
     $mc_feedback = first_set( $menu_feedback, false() )
     $mc_current_title = first_set( $menu_current_choice.title, $menu_current.current_menu, '' )}

<div class="context-block exp-menuconfig">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
    <h1 class="context-title">{'Menus (classic)'|i18n( 'design/admin/visual/menuconfig' )}</h1>
    <span class="exp-badge is-classic">{'Classic designs'|i18n( 'design/admin/visual/menuconfig' )}</span>
</div>
<p class="exp-subtitle">{'The top and left menus of sites built with the classic designs, chosen per siteaccess.'|i18n( 'design/admin/visual/menuconfig' )}</p>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $mc_feedback}
    {if eq( $mc_feedback.type, 'saved' )}
<div class="exp-feedback is-ok" role="status">
    <p><strong>{'Saved: %siteaccess now uses %menu.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $mc_feedback.siteaccess|wash, '%menu', $mc_feedback.menu|wash ) )}</strong></p>
    <p>{if and( is_set( $mc_feedback.created ), $mc_feedback.created )}{'The file %file was created with the three settings.'|i18n( 'design/admin/visual/menuconfig',, hash( '%file', concat( '<code>', $mc_feedback.file|wash, '</code>' ) ) )}{else}{'The three settings were written to %file; the rest of the file is unchanged.'|i18n( 'design/admin/visual/menuconfig',, hash( '%file', concat( '<code>', $mc_feedback.file|wash, '</code>' ) ) )}{/if}
       {'The INI cache, the compiled page layouts and the template blocks were cleared.'|i18n( 'design/admin/visual/menuconfig' )}{if and( is_set( $mc_feedback.backup ), $mc_feedback.backup )} {'The file as it was before is kept in %backup.'|i18n( 'design/admin/visual/menuconfig',, hash( '%backup', concat( '<code>', $mc_feedback.backup|wash, '</code>' ) ) )}{/if}</p>
    <p>{'No restart is needed, neither of PHP-FPM nor of Velocity: every request reads the settings again. Pages kept in a response cache show the old menus until that cache is cleared.'|i18n( 'design/admin/visual/menuconfig' )}</p>
</div>
    {elseif eq( $mc_feedback.type, 'unchanged' )}
<div class="exp-feedback is-info" role="status"><p>{'%siteaccess already uses %menu. Nothing was written.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $mc_feedback.siteaccess|wash, '%menu', $mc_feedback.menu|wash ) )}</p></div>
    {elseif eq( $mc_feedback.type, 'invalid' )}
<div class="exp-feedback is-warn" role="alert"><p>{'Choose one of the menu arrangements below. Nothing was saved.'|i18n( 'design/admin/visual/menuconfig' )}</p></div>
    {else}
<div class="exp-feedback is-bad" role="alert">
    <p><strong>{'The menu settings of %siteaccess were not saved.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $mc_feedback.siteaccess|wash ) )}</strong></p>
    <p>
    {switch match=$mc_feedback.error}
    {case match='not_writable'}{'The web server cannot write %file. Make it writable for the web server user, or set the value in that file by hand.'|i18n( 'design/admin/visual/menuconfig',, hash( '%file', concat( '<code>', $mc_feedback.file|wash, '</code>' ) ) )}{/case}
    {case match='refused'}{'The write was refused because the audit cannot record it right now. Nothing was written; try again when the audit works.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case match='verify_failed'}{'What was written could not be read back as written, so the file was put back exactly as it was.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case match='no_directory'}{'The settings directory of this siteaccess was not found under settings/siteaccess.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {case}{'Writing the file failed; it was left as it was. The error log has the details.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
    {/switch}
    </p>
</div>
    {/if}
{/if}

<section class="exp-about" aria-labelledby="menuconfig-about-title">
    <div>
        <h2 class="exp-h2" id="menuconfig-about-title">{'What these settings do'|i18n( 'design/admin/visual/menuconfig' )}</h2>
        <p>{'Sites built with the classic designs (base, ezwebin, the demo and the simple design) draw their top and left menus from one of four arrangements, chosen here for each siteaccess and stored in menu.ini.'|i18n( 'design/admin/visual/menuconfig' )}</p>
        <p>{'Sites built with Exponential Layouts do not read them: their menus are blocks in a layout, set in the layout editor. The administration interface does not read them either.'|i18n( 'design/admin/visual/menuconfig' )}</p>
    </div>
    {if first_set( $menu_layouts_url, false() )}
    <div class="exp-actions">
        <a class="exp-btn" href={$menu_layouts_url|ezurl}>{'Open the layout editor'|i18n( 'design/admin/visual/menuconfig' )}</a>
    </div>
    {/if}
</section>

{if eq( $mc_relevant_count, 0 )}
<div class="exp-feedback is-info" role="status">
    <p><strong>{'None of the siteaccesses listed here reads these settings.'|i18n( 'design/admin/visual/menuconfig' )}</strong>
    {'Saving would change a file but no page. Nothing needs to be done here unless a siteaccess with a classic design is added.'|i18n( 'design/admin/visual/menuconfig' )}</p>
</div>
{/if}

<section class="exp-section" aria-labelledby="menuconfig-siteaccesses-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="menuconfig-siteaccesses-title">{'Siteaccesses'|i18n( 'design/admin/visual/menuconfig' )}</h2>
        <p>{'Found from the page layout each siteaccess uses: only those whose page layout reads these settings are affected by them.'|i18n( 'design/admin/visual/menuconfig' )}</p>
    </div>
    {if gt( $mc_relevant_count, 0 )}
    <ul class="exp-sa-list">
        {foreach $menu_siteaccesses as $sa}{if $sa.relevant}{include uri='design:visual/menuconfig_siteaccess.tpl' sa=$sa}{/if}{/foreach}
    </ul>
    {/if}
    {if gt( $mc_other_count, 0 )}
    <details class="exp-fold" id="menuconfig-others">
        <summary>{'Siteaccesses where these settings do nothing'|i18n( 'design/admin/visual/menuconfig' )} <span class="exp-muted">({$mc_other_count})</span></summary>
        <div class="exp-fold-body">
            <ul class="exp-sa-list">
                {foreach $menu_siteaccesses as $sa}{if $sa.relevant|not}{include uri='design:visual/menuconfig_siteaccess.tpl' sa=$sa}{/if}{/foreach}
            </ul>
        </div>
    </details>
    {/if}
</section>

{if $current_siteaccess}
<section class="exp-section" aria-labelledby="menuconfig-edit-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="menuconfig-edit-title">{'Menu of %siteaccess'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</h2>
    </div>

    <div class="exp-panel">
    <form class="exp-switch" method="post" action={'/visual/menuconfig/'|ezurl}>
        <div class="exp-field">
            <label for="menuconfig-siteaccess">{'Siteaccess'|i18n( 'design/admin/visual/menuconfig' )}</label>
            <select id="menuconfig-siteaccess" name="CurrentSiteAccess">
            {foreach $siteaccess_list as $mc_siteaccess}
                <option value="{$mc_siteaccess|wash}"{if eq( $current_siteaccess, $mc_siteaccess )} selected="selected"{/if}>{$mc_siteaccess|wash}</option>
            {/foreach}
            </select>
        </div>
        <button class="exp-btn" type="submit" name="SelectCurrentSiteAccessButton" value="1">{'Show'|i18n( 'design/admin/visual/menuconfig' )}</button>
    </form>

    {if $mc_info}
        {switch match=$mc_info.status}
        {case match='classic'}<div class="exp-feedback is-ok"><p>{'The pages of %siteaccess read these settings: what you save here changes their menus.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</p></div>{/case}
        {case match='mixed'}<div class="exp-feedback is-ok"><p>{'The page layout of %siteaccess reads these settings and also renders Exponential Layouts: what you save here changes the menus it draws itself.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</p></div>{/case}
        {case match='layouts'}<div class="exp-feedback is-warn"><p><strong>{'Not used here: %siteaccess renders through Exponential Layouts.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</strong> {'Saving changes the file, not the site. Its menus are blocks in its layouts.'|i18n( 'design/admin/visual/menuconfig' )}{if first_set( $menu_layouts_url, false() )} <a href={$menu_layouts_url|ezurl}>{'Open the layout editor'|i18n( 'design/admin/visual/menuconfig' )}</a>{/if}</p></div>{/case}
        {case match='admin'}<div class="exp-feedback is-warn"><p><strong>{'Not used here: %siteaccess is an administration siteaccess.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</strong> {'Its tabs and side menus come from menu.ini [TopAdminMenu] and the Leftmenu sections, not from these settings.'|i18n( 'design/admin/visual/menuconfig' )}</p></div>{/case}
        {case match='other'}<div class="exp-feedback is-warn"><p><strong>{'Not used here: the page layout of %siteaccess does not read these settings.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</strong> {'Saving changes the file, not the site.'|i18n( 'design/admin/visual/menuconfig' )}</p></div>{/case}
        {case}<div class="exp-feedback is-warn"><p>{'No page layout was found for %siteaccess, so it is not known whether it reads these settings.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</p></div>{/case}
        {/switch}
    {/if}

    {if and( $mc_current, $mc_current.overridden )}
    <div class="exp-feedback is-bad" role="alert"><p><strong>{'A file read after the siteaccess settings decides the value:'|i18n( 'design/admin/visual/menuconfig' )}</strong> <code>{$mc_current.settings.CurrentMenu.file|wash}</code>. {'Saving here would not change what %siteaccess uses until that setting is removed there.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</p></div>
    {/if}

    {if $mc_current}
    <dl class="exp-facts">
        <div><dt>{'Current setting'|i18n( 'design/admin/visual/menuconfig' )}</dt><dd><strong>{if $mc_current_title}{$mc_current_title|wash}{else}{'none'|i18n( 'design/admin/visual/menuconfig' )}{/if}</strong></dd></div>
        <div><dt>{'Comes from'|i18n( 'design/admin/visual/menuconfig' )}</dt><dd><code>{$mc_current.settings.CurrentMenu.file|wash}</code>
            <span class="exp-muted">{switch match=$mc_current.settings.CurrentMenu.kind}{case match='default'}({'the default'|i18n( 'design/admin/visual/menuconfig' )}){/case}{case match='siteaccess'}({'this siteaccess'|i18n( 'design/admin/visual/menuconfig' )}){/case}{case match='extension'}({'an extension'|i18n( 'design/admin/visual/menuconfig' )}){/case}{case match='extension_siteaccess'}({'an extension, for this siteaccess'|i18n( 'design/admin/visual/menuconfig' )}){/case}{case match='override'}({'the global override'|i18n( 'design/admin/visual/menuconfig' )}){/case}{case}{/case}{/switch}</span></dd></div>
        <div><dt>{'Saving writes to'|i18n( 'design/admin/visual/menuconfig' )}</dt><dd>{if $menu_target_file}<code>{$menu_target_file|wash}</code> <span class="exp-muted">({if $menu_target_exists}{'exists'|i18n( 'design/admin/visual/menuconfig' )}{else}{'will be created'|i18n( 'design/admin/visual/menuconfig' )}{/if})</span>{else}<span class="exp-muted">{'no settings directory for this siteaccess'|i18n( 'design/admin/visual/menuconfig' )}</span>{/if}</dd></div>
    </dl>

    <details class="exp-fold" id="menuconfig-current-details">
        <summary>{'All three settings and the classes the menus list'|i18n( 'design/admin/visual/menuconfig' )}</summary>
        <div class="exp-fold-body">
            <div class="exp-table-wrap">
            <table class="exp-table">
                <caption class="exp-sr">{'[SelectedMenu] of %siteaccess'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</caption>
                <thead><tr><th scope="col">{'Setting'|i18n( 'design/admin/visual/menuconfig' )}</th><th scope="col">{'Value'|i18n( 'design/admin/visual/menuconfig' )}</th><th scope="col">{'File'|i18n( 'design/admin/visual/menuconfig' )}</th></tr></thead>
                <tbody>
                {foreach $mc_current.settings as $mc_name => $mc_setting}
                <tr><td><code>{$mc_name|wash}</code></td><td><code>{if $mc_setting.value}{$mc_setting.value|wash}{else}&mdash;{/if}</code></td><td><code>{$mc_setting.file|wash}</code></td></tr>
                {/foreach}
                </tbody>
            </table>
            </div>
            <p class="exp-muted" style="margin-top: 10px">{'The menus list only objects of these classes (menu.ini [MenuContentSettings], not changed by this page):'|i18n( 'design/admin/visual/menuconfig' )}</p>
            <dl class="exp-facts">
                <div><dt>{'Top menu'|i18n( 'design/admin/visual/menuconfig' )}</dt><dd>{if $mc_current.top_classes}<code>{$mc_current.top_classes|implode( ', ' )|wash}</code>{else}&mdash;{/if}</dd></div>
                <div><dt>{'Left menu'|i18n( 'design/admin/visual/menuconfig' )}</dt><dd>{if $mc_current.left_classes}<code>{$mc_current.left_classes|implode( ', ' )|wash}</code>{else}&mdash;{/if}</dd></div>
            </dl>
        </div>
    </details>
    {/if}

    {if $menu_choices}
    <form method="post" action={'/visual/menuconfig/'|ezurl}>
        <input type="hidden" name="CurrentSiteAccess" value="{$current_siteaccess|wash}" />
        <fieldset class="exp-choices">
            <legend>{'Menu arrangement'|i18n( 'design/admin/visual/menuconfig' )}</legend>
            <p class="exp-muted">{"Each preview puts this installation's pages where the menus would show them, for a visitor in the first top menu page that has pages of its own."|i18n( 'design/admin/visual/menuconfig' )}</p>
            <ul class="exp-choice-list">
            {def $mc_pv = first_set( $menu_preview, false() )
                 $mc_srcs = array()
                 $mc_left_items = array()
                 $mc_left_src = false()}
            {foreach $menu_choices as $mc_choice}
            {set $mc_srcs = first_set( $menu_sources[$mc_choice.type], array() )
                 $mc_left_src = false()
                 $mc_left_items = array()}
            {foreach $mc_srcs as $mc_src}{if eq( $mc_src.position, 'left' )}{set $mc_left_src = $mc_src}{/if}{/foreach}
            {if and( $mc_pv, $mc_left_src )}{set $mc_left_items = cond( eq( $mc_left_src.parent, 'root' ), $mc_pv.left_root.items, $mc_pv.left.items )}{/if}
            <li class="exp-choice">
            <label class="exp-choice-pick" for="Menu_{$mc_choice.type|wash}">
                <input type="radio" id="Menu_{$mc_choice.type|wash}" name="MenuType" value="{$mc_choice.type|wash}"{if eq( $mc_choice.type, $current_menu )} checked="checked"{/if} aria-describedby="Menu_{$mc_choice.type|wash}_desc" />
                <span class="exp-choice-title">{$mc_choice.title|wash}{if eq( $mc_choice.type, $current_menu )} <span class="exp-badge is-classic">{'current'|i18n( 'design/admin/visual/menuconfig' )}</span>{/if}</span>

                {* A small page with this installation's real pages in the places the menus put them *}
                <span class="exp-mini" aria-hidden="true">
                    <span class="exp-mini-head">{if $mc_pv}{$mc_pv.root.name|wash}{else}{'Start page'|i18n( 'design/admin/visual/menuconfig' )}{/if}</span>
                    {if $mc_choice.has_top}
                    <span class="exp-mini-top">{if first_set( $menu_preview.top.items, false() )}{foreach $mc_pv.top.items as $mc_k => $mc_item max 6}<b{if and( $mc_pv.section, eq( $mc_item.node_id, $mc_pv.section.node_id ) )} class="is-in"{/if}>{$mc_item.name|wash}</b>{/foreach}{else}<b>&hellip;</b>{/if}</span>
                    {/if}
                    {if $mc_choice.has_second_row}
                    <span class="exp-mini-second">{if first_set( $menu_preview.second.items, false() )}{foreach $mc_pv.second.items as $mc_item max 6}<b>{$mc_item.name|wash}</b>{/foreach}{else}<b>&mdash;</b>{/if}</span>
                    {/if}
                    <span class="exp-mini-body{if $mc_choice.has_left} has-left{/if}">
                        {if $mc_choice.has_left}
                        <span class="exp-mini-left">{if $mc_left_items}{foreach $mc_left_items as $mc_k => $mc_item max 6}<b>{$mc_item.name|wash}</b>{if and( eq( $mc_k, 0 ), $mc_left_src.deeper, $mc_pv.deeper.items )}{foreach $mc_pv.deeper.items as $mc_sub max 3}<b class="is-sub">{$mc_sub.name|wash}</b>{/foreach}{/if}{/foreach}{else}<b>&mdash;</b>{/if}</span>
                        {/if}
                        <span class="exp-mini-main">{if first_set( $menu_preview.section, false() )}<i>{'Visitor in: %page'|i18n( 'design/admin/visual/menuconfig',, hash( '%page', $mc_pv.section.name|wash ) )}</i>{/if}<i></i><i></i><i></i></span>
                    </span>
                </span>

                <span class="exp-choice-desc" id="Menu_{$mc_choice.type|wash}_desc">
                <dl>
                    <div><dt>{'Top:'|i18n( 'design/admin/visual/menuconfig' )}</dt>
                    <dd>{switch match=$mc_choice.top_kind}
                        {case match='flat_top'}{'one row of links to the pages directly below the start page.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {case match='double_top'}{'two rows: the pages directly below the start page and, under them, the pages of the first-level page the visitor is in.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {case match='none'}{'no menu.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {case}{"the design's own template menu/%name.tpl."|i18n( 'design/admin/visual/menuconfig',, hash( '%name', $mc_choice.top|wash ) )}{/case}
                    {/switch}</dd></div>
                    <div><dt>{'Left:'|i18n( 'design/admin/visual/menuconfig' )}</dt>
                    <dd>{switch match=$mc_choice.left_kind}
                        {case match='flat_left'}{if eq( $mc_choice.type, 'LeftOnly' )}{'a column with the pages directly below the start page, opened further below the page the visitor is in.'|i18n( 'design/admin/visual/menuconfig' )}{else}{'a column with the pages of the first-level page the visitor is in, opened further below the page the visitor is in.'|i18n( 'design/admin/visual/menuconfig' )}{/if}{/case}
                        {case match='sub_left'}{'a column with the pages of the first-level page the visitor is in, one level.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {case match='none'}{'no menu.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {case}{"the design's own template menu/%name.tpl."|i18n( 'design/admin/visual/menuconfig',, hash( '%name', $mc_choice.left|wash ) )}{/case}
                    {/switch}</dd></div>
                </dl>
                </span>

            </label>
                <details class="exp-choice-more">
                    <summary>{'Template, content and the lines it writes'|i18n( 'design/admin/visual/menuconfig' )}</summary>
                    <ul class="exp-plain exp-choice-facts">
                    {foreach array( $mc_choice.top, $mc_choice.left ) as $mc_template}{if $mc_template}
                        <li>{'Drawn by'|i18n( 'design/admin/visual/menuconfig' )} <code>menu/{$mc_template|wash}.tpl</code>{if first_set( $menu_template_files[$mc_template], '' )} &rarr; <code>{$menu_template_files[$mc_template]|wash}</code>{else} <span class="exp-muted">({'not found in the designs of this siteaccess'|i18n( 'design/admin/visual/menuconfig' )})</span>{/if}{if and( first_set( $menu_template_files[$mc_template], '' )|begins_with( 'design/base/' ), ne( $mc_template, 'sub_left' ) )} <span class="exp-muted">({'this design/base template lists the children of node 2, the top of the content tree, not those of content.ini RootNode'|i18n( 'design/admin/visual/menuconfig' )})</span>{/if}</li>
                    {/if}{/foreach}
                    {foreach $mc_srcs as $mc_src}
                        <li>
                        {switch match=$mc_src.position}
                        {case match='top'}{'Top row'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {case match='second'}{'Second row'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {case}{'Left column'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                        {/switch}:
                        {if eq( $mc_src.parent, 'root' )}{if $mc_pv}{'the children of the start page "%page" (node %id)'|i18n( 'design/admin/visual/menuconfig',, hash( '%page', $mc_pv.root.name|wash, '%id', $mc_pv.root.node_id ) )}{else}{'the children of the start page'|i18n( 'design/admin/visual/menuconfig' )}{/if}{else}{'the children of the first-level page the visitor is in'|i18n( 'design/admin/visual/menuconfig' )}{if $mc_pv}{if $mc_pv.section} ({'here: "%page"'|i18n( 'design/admin/visual/menuconfig',, hash( '%page', $mc_pv.section.name|wash ) )}){/if}{/if}{/if},
                        {'classes'|i18n( 'design/admin/visual/menuconfig' )} <code>{if eq( $mc_src.classes, 'TopIdentifierList' )}{$mc_current.top_classes|implode( ', ' )|wash}{else}{$mc_current.left_classes|implode( ', ' )|wash}{/if}</code>
                        <span class="exp-muted">({$mc_src.classes|wash})</span>{if $mc_src.deeper}, {"opened further along the visitor's path"|i18n( 'design/admin/visual/menuconfig' )}{/if}.
                        </li>
                    {/foreach}
                    {if $mc_srcs}<li class="exp-muted">{"Every list follows its parent page's sort order and leaves hidden pages out (ShowHiddenNodes=false)."|i18n( 'design/admin/visual/menuconfig' )}</li>{/if}
                    </ul>
                    <pre class="exp-lines">{$menu_planned_text[$mc_choice.type]|wash}</pre>
                </details>
            </li>
            {/foreach}
            {undef $mc_pv $mc_srcs $mc_left_items $mc_left_src}
            </ul>
        </fieldset>

        <details class="exp-fold" id="menuconfig-save-details">
            <summary>{'What saving does'|i18n( 'design/admin/visual/menuconfig' )}</summary>
            <div class="exp-fold-body">
                <ol class="exp-steps">
                    <li>{'Writes the three settings of the chosen arrangement into the section [SelectedMenu] of %file, and nothing else. The other sections and comments of that file stay as they are.'|i18n( 'design/admin/visual/menuconfig',, hash( '%file', concat( '<code>', $menu_target_file|wash, '</code>' ) ) )}</li>
                    <li>{'Keeps a copy of the file as it was in var/backup/ini, then reads the new file back; if it does not hold what was written, puts the old one back exactly as it was.'|i18n( 'design/admin/visual/menuconfig' )}</li>
                    <li>{'Clears the INI cache, the compiled page layouts and the template blocks. No restart of PHP-FPM or Velocity is needed.'|i18n( 'design/admin/visual/menuconfig' )}</li>
                </ol>
                {if and( $current_menu, is_set( $menu_planned_text[$current_menu] ) )}
                <p class="exp-muted" style="margin-top: 10px">{'For the arrangement checked when the page was opened, the section reads:'|i18n( 'design/admin/visual/menuconfig' )}</p>
                <pre class="exp-lines">{$menu_planned_text[$current_menu]|wash}</pre>
                {/if}
            </div>
        </details>

        <div class="exp-savebar">
            {if $mc_relevant|not}
            <label class="exp-confirm" for="menuconfig-confirm"><input type="checkbox" id="menuconfig-confirm" name="ConfirmNoEffect" value="1" required="required" />
                <span>{'I understand that %siteaccess does not read these settings and saving changes no page.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</span></label>
            {/if}
            <span class="exp-meta">{if $menu_target_writable}{'Saves for %siteaccess only.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}{else}{'The web server cannot write the settings file of %siteaccess, so saving is not possible here.'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}{/if}</span>
            <div class="exp-actions">
                <button class="exp-btn{if $mc_relevant} exp-btn-primary{/if}" type="submit" name="StoreButton" value="1"{if $menu_target_writable|not} disabled="disabled"{/if} title="{'Save the chosen arrangement for this siteaccess.'|i18n( 'design/admin/visual/menuconfig' )}">{'Save for %siteaccess'|i18n( 'design/admin/visual/menuconfig',, hash( '%siteaccess', $current_siteaccess|wash ) )}</button>
            </div>
        </div>
    </form>
    {else}
    <p class="exp-empty">{'menu.ini [MenuSettings] AvailableMenuArray lists no menu arrangement, so there is nothing to choose.'|i18n( 'design/admin/visual/menuconfig' )}</p>
    {/if}
    </div>
</section>

{* Worked examples: the exact lines (from expClassicMenuSettings::recipes(), the same lines saving writes) and what a
   visitor sees, with this installation's pages where they help *}
{def $mc_recipes = first_set( $menu_recipes, array() )
     $mc_pvr = first_set( $menu_preview, false() )
     $mc_top_names = array()
     $mc_left_names = array()
     $mc_section_name = ''}
{if $mc_pvr}
    {foreach $mc_pvr.top.items as $mc_item max 5}{set $mc_top_names = $mc_top_names|append( $mc_item.name )}{/foreach}
    {foreach $mc_pvr.left.items as $mc_item max 5}{set $mc_left_names = $mc_left_names|append( $mc_item.name )}{/foreach}
    {if $mc_pvr.section}{set $mc_section_name = $mc_pvr.section.name}{/if}
{/if}
{if $mc_recipes}
<section class="exp-section" aria-labelledby="menuconfig-recipes-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="menuconfig-recipes-title">{'Examples'|i18n( 'design/admin/visual/menuconfig' )}</h2>
        <p>{'Common recipes with the exact lines and what a visitor sees. The lines that choose an arrangement are the ones this page writes when you save it; the others go into the same files by hand.'|i18n( 'design/admin/visual/menuconfig' )}</p>
    </div>
    {if first_set( $menu_layouts_siteaccesses, array() )}
    <div class="exp-feedback is-info"><p>{'None of these examples does anything for %list: they render through Exponential Layouts, where a menu is a block in the layout.'|i18n( 'design/admin/visual/menuconfig',, hash( '%list', $menu_layouts_siteaccesses|implode( ', ' )|wash ) )}{if first_set( $menu_layouts_url, false() )} <a href={$menu_layouts_url|ezurl}>{'Open the layout editor'|i18n( 'design/admin/visual/menuconfig' )}</a>{/if}</p></div>
    {/if}
    <ul class="exp-recipes exp-plain">
    {foreach $mc_recipes as $mc_id => $mc_recipe}
        <li class="exp-recipe">
            <h3>{switch match=$mc_id}
                {case match='top_only'}{'Top menu only'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='top_and_left'}{'Top menu plus a left menu for the current section'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='limit_classes'}{'Limit the menus to folders and landing pages'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='hide_node'}{'Hide one page from the menus'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case}{'A different menu per siteaccess'|i18n( 'design/admin/visual/menuconfig' )}{/case}
            {/switch}</h3>
            <p>{switch match=$mc_id}
                {case match='top_only'}{'The arrangement Only top menu: menu/flat_top.tpl draws one row, no left column.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='top_and_left'}{'The arrangement Left and top: the top row as above, and menu/sub_left.tpl draws a left column with the pages of the first-level page the visitor is in. On the start page there is no left column.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='limit_classes'}{'The empty TopIdentifierList[] line clears the list read from the files before, so only the classes that follow are menu items: folder and frontpage (the landing page class of ezwebin). It applies to every arrangement.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='hide_node'}{'Hide the page in the content structure (its menu: Hide / unhide). The menus fetch pages as visitors see them, so a hidden page is left out of every classic menu as long as ShowHiddenNodes stays false, the default shown below. Another way: give the page a class that is not in the lists of the recipe above.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case}{'Every siteaccess reads its own settings/siteaccess/(name)/menu.ini.append.php. Save on this page once per siteaccess, or put the lines in the two files:'|i18n( 'design/admin/visual/menuconfig' )}{/case}
            {/switch}</p>
            {foreach $mc_recipe.files as $mc_file => $mc_lines}
            <p class="exp-recipe-file"><code>{$mc_file|wash}</code></p>
            <pre class="exp-lines">{$mc_lines|wash}</pre>
            {/foreach}
            <p class="exp-recipe-sees"><strong>{'A visitor sees:'|i18n( 'design/admin/visual/menuconfig' )}</strong>
            {switch match=$mc_id}
                {case match='top_only'}{if $mc_top_names}{'one row of links under the header: %names.'|i18n( 'design/admin/visual/menuconfig',, hash( '%names', $mc_top_names|implode( ', ' )|wash ) )}{else}{'one row of links under the header.'|i18n( 'design/admin/visual/menuconfig' )}{/if}{/case}
                {case match='top_and_left'}{if and( $mc_section_name, $mc_left_names )}{'the same row; inside %page, a left column with %names.'|i18n( 'design/admin/visual/menuconfig',, hash( '%page', $mc_section_name|wash, '%names', $mc_left_names|implode( ', ' )|wash ) )}{else}{'the same row, and inside a first-level page a left column with its pages.'|i18n( 'design/admin/visual/menuconfig' )}{/if}{/case}
                {case match='limit_classes'}{'only folders and landing pages as menu items; articles, links and other pages stay reachable but are no longer in the menus.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='hide_node'}{'the page gone from the menus and from the site; editors still see it in the administration.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case}{'on %first a top menu only, on %second a top menu and a left column.'|i18n( 'design/admin/visual/menuconfig',, hash( '%first', $menu_recipe_pair[0]|wash, '%second', $menu_recipe_pair[1]|wash ) )}{/case}
            {/switch}</p>
        </li>
    {/foreach}
    </ul>
</section>
{/if}
{undef $mc_recipes $mc_pvr $mc_top_names $mc_left_names $mc_section_name}
{/if}

{* Use in your templates: which designs draw these menus, and copy-ready template examples
   (kernel/classes/classicmenu/examples/, the same text as in the guide) *}
{def $mc_survey = first_set( $menu_design_survey, array() )
     $mc_examples = first_set( $menu_template_examples, array() )}
<section class="exp-section" aria-labelledby="menuconfig-templates-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="menuconfig-templates-title">{'Use in your templates'|i18n( 'design/admin/visual/menuconfig' )}</h2>
        <p>{'Saving here only changes what a page layout asks menu.ini for. A menu appears only where a page layout includes it. In this installation:'|i18n( 'design/admin/visual/menuconfig' )}</p>
    </div>
    {if $mc_survey}
    <ul class="exp-designs">
    {foreach $mc_survey as $mc_design}
        <li><code>{$mc_design.path|wash}</code>
        <span>{switch match=$mc_design.status}
            {case match='draws'}{'its page layout draws the menus, with the templates %list.'|i18n( 'design/admin/visual/menuconfig',, hash( '%list', $mc_design.menu_templates|implode( ', ' )|wash ) )}{/case}
            {case match='templates_only'}{'has menu templates (%list), but its page layout does not include them: the settings do nothing until a page layout of yours does.'|i18n( 'design/admin/visual/menuconfig',, hash( '%list', $mc_design.menu_templates|implode( ', ' )|wash ) )}{/case}
            {case}{if $mc_design.layouts}{'renders through Exponential Layouts; its menus are layout blocks.'|i18n( 'design/admin/visual/menuconfig' )}{else}{'no menu templates, and its page layout draws no classic menu.'|i18n( 'design/admin/visual/menuconfig' )}{/if}{/case}
        {/switch}</span></li>
    {/foreach}
    </ul>
    {/if}

    {foreach $mc_examples as $mc_id => $mc_example}
    <div class="exp-snippet">
        <div class="exp-snippet-head">
            <h3 id="menuconfig-snippet-{$mc_id|wash}-title">{switch match=$mc_id}
                {case match='pagelayout_menus'}{'Draw the chosen menus from a page layout'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='menu_my_top'}{'A top menu template of your own'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case match='menu_my_section_left'}{'A left menu for the current section'|i18n( 'design/admin/visual/menuconfig' )}{/case}
                {case}{'Where the files go'|i18n( 'design/admin/visual/menuconfig' )}{/case}
            {/switch}</h3>
            <button type="button" class="exp-btn exp-btn-small exp-copy" data-copy="menuconfig-snippet-{$mc_id|wash}" hidden="hidden">{'Copy'|i18n( 'design/admin/visual/menuconfig' )}</button>
        </div>
        <p>{switch match=$mc_id}
            {case match='pagelayout_menus'}{'Put it where the menus go in your pagelayout.tpl. The cache-block keys are those of design/base: the address, and the roles and limitations of the user, so every visitor gets the selected item of the page and only what their rights allow; the siteaccess is part of every cache-block key by itself, and the block expires whenever content is published.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
            {case match='menu_my_top'}{'The visible children of the start page (content.ini RootNode) of the TopIdentifierList classes, with the item the visitor is in marked. Unlike the design/base templates it follows RootNode, so it also fits a site whose start page is not node 2.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
            {case match='menu_my_section_left'}{'The pages of the first-level page the visitor is in, of the LeftIdentifierList classes, with the current page marked; nothing on the start page.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
            {case}{'Never edit a shipped design: an update replaces it. Your design extension is found before the shipped designs, and the extra [MenuSettings] entry offers your templates as a fifth arrangement on this page.'|i18n( 'design/admin/visual/menuconfig' )}{/case}
        {/switch}</p>
        <pre class="exp-code" id="menuconfig-snippet-{$mc_id|wash}" tabindex="0" aria-labelledby="menuconfig-snippet-{$mc_id|wash}-title"><code>{$mc_example.text|wash}</code></pre>
    </div>
    {/foreach}

    <div class="exp-feedback is-info"><p><strong>{'With Exponential Layouts:'|i18n( 'design/admin/visual/menuconfig' )}</strong> {'a siteaccess that renders through Layouts draws its menus with blocks in a layout zone, so none of these templates apply there. Classic and Layouts siteaccesses can live side by side in one installation, each with its own design.'|i18n( 'design/admin/visual/menuconfig' )}{if first_set( $menu_layouts_url, false() )} <a href={$menu_layouts_url|ezurl}>{'Open the layout editor'|i18n( 'design/admin/visual/menuconfig' )}</a>{/if}</p></div>
</section>
{literal}
<script>
(function () {
    var buttons = document.querySelectorAll('.exp-menuconfig .exp-copy');
    Array.prototype.forEach.call(buttons, function (button) {
        var target = document.getElementById(button.getAttribute('data-copy'));
        if (!target) { return; }
        button.hidden = false;
        var label = button.textContent;
        button.addEventListener('click', function () {
            var text = target.textContent;
            var done = function () { button.textContent = label + ' ✓'; setTimeout(function () { button.textContent = label; }, 1500); };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(done, function () {});
            } else {
                var range = document.createRange(); range.selectNodeContents(target);
                var selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(range);
                try { document.execCommand('copy'); done(); } catch (e) {}
            }
        });
    });
})();
</script>
{/literal}
{undef $mc_survey $mc_examples}

<details class="exp-fold" id="menuconfig-history">
    <summary>{'Why this page exists'|i18n( 'design/admin/visual/menuconfig' )} <span class="exp-muted">{'a short history'|i18n( 'design/admin/visual/menuconfig' )}</span></summary>
    <div class="exp-fold-body">
        <p>{'The classic designs build every page from one page layout, and that page layout asks menu.ini which menus to draw. This page has chosen that since the first versions: four arrangements of a top and a left menu, saved per siteaccess.'|i18n( 'design/admin/visual/menuconfig' )}</p>
        <p>{'Exponential Layouts replaced that for new sites: a layout holds zones and blocks, a menu is one of the blocks, and a rule picks the layout for each page. A siteaccess built that way never reads these settings.'|i18n( 'design/admin/visual/menuconfig' )}</p>
        <p>{'The page stays for sites that still use a classic design. Changing a setting here for a siteaccess built with Layouts is harmless but has no effect. The guide doc/guides/classic-menu-settings.md explains both ways.'|i18n( 'design/admin/visual/menuconfig' )}</p>
    </div>
</details>

</div></div></div>

</div>

{undef $mc_info $mc_current $mc_relevant_count $mc_all_count $mc_other_count $mc_relevant $mc_feedback $mc_current_title}
