{* The place of a settings file in the override chain, as a label: expSettingsChain::placementOf() gives $origin
   (kind, extension, siteaccess, dir, path). The path is in the title and, with $show_path, after the label.
   The same file is in design/admin and design/admin4. *}
{if $origin}<span class="exp-origin is-{$origin.kind|wash}" title="{$origin.path|wash}">{switch match=$origin.kind}
{case match='default'}{'Default'|i18n( 'design/admin/settings' )}{/case}
{case match='override'}{'Global override'|i18n( 'design/admin/settings' )}{/case}
{case match='siteaccess'}{'Siteaccess %siteaccess'|i18n( 'design/admin/settings',, hash( '%siteaccess', $origin.siteaccess ) )|wash}{/case}
{case match='extension'}{'Extension %extension'|i18n( 'design/admin/settings',, hash( '%extension', $origin.extension ) )|wash}{/case}
{case match='extension-siteaccess'}{'Extension %extension for %siteaccess'|i18n( 'design/admin/settings',, hash( '%extension', $origin.extension, '%siteaccess', $origin.siteaccess ) )|wash}{/case}
{case match='extension-dir'}{'Extension %extension (%dir)'|i18n( 'design/admin/settings',, hash( '%extension', $origin.extension, '%dir', $origin.dir ) )|wash}{/case}
{case}{$origin.path|wash}{/case}
{/switch}</span>{if first_set( $show_path, false() )} <span class="exp-step-path">{$origin.path|wash}</span>{/if}{else}<span class="exp-origin">{'Unknown'|i18n( 'design/admin/settings' )}</span>{/if}
