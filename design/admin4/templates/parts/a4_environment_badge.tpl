{* The environment badge: the name of this installation's environment (site.ini [AdminEnvironment], read by
   exp_environment_badge()), beside the logo in the header and on the sign-in page. Nothing without a level, the
   default. Text, not only color: the dot is decoration, the name is read out at every width.
   BadgeDisplay=hover (the default): a label that shows only while a mouse or trackpad pointer rests on the logo or
   the logo has the keyboard focus (Escape hides it, admin4.js); on touch screens, which cannot hover, it stands
   beside the logo as with always. The logo link names it as its description
   (aria-describedby="a4-env-badge"), so screen readers keep it. BadgeDisplay=always: beside the logo at all times
   (up to 767px the short code; the name stays for screen readers). The release only for signed-in users.
   The pagelayouts key their header cache-blocks by the badge's key. *}
{def $a4_env_badge = exp_environment_badge()}
{if $a4_env_badge.level|ne( '' )}
{def $a4_env_release = cond( first_set( $current_user.is_logged_in, false() ), $a4_env_badge.release, '' )}
<span id="a4-env-badge" class="a4-env a4-env-{$a4_env_badge.level} a4-env-{$a4_env_badge.display}"{if $a4_env_badge.display|eq( 'always' )} title="{'Environment'|i18n( 'design/admin/pagelayout' )|wash}: {$a4_env_badge.name|wash}{if $a4_env_release|ne( '' )} · {$a4_env_release|wash}{/if}"{/if}>
    <span class="a4-env-dot" aria-hidden="true"></span>
    <span class="a4-sr-only">{'Environment'|i18n( 'design/admin/pagelayout' )|wash}:</span>
    <span class="a4-env-name">{$a4_env_badge.name|wash}</span>
    <span class="a4-env-short" aria-hidden="true">{$a4_env_badge.short|wash}</span>
    {if $a4_env_release|ne( '' )}<span class="a4-env-release">{$a4_env_release|wash}</span>{/if}
</span>
{undef $a4_env_release}
{/if}
{undef $a4_env_badge}
