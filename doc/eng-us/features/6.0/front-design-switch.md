# Switching designs on a front siteaccess

A public siteaccess can offer its visitors more than one design and let them switch between them with one click, for
example a classic design and a modern one of the same content. Every visitor can switch: no account, no session and
no policy are needed. The choice is kept in a cookie and applies to every page of the siteaccess.

This is separate from the [UI switch](ui-switch.md), which lets signed-in users of the administration choose their
interface. A front design switch never works on an administration siteaccess.

## Turning it on

The switch is off until the siteaccess lists designs to switch to. In the siteaccess's
`settings/siteaccess/<siteaccess>/site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=doc
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base
SwitchableDesigns[]
SwitchableDesigns[]=doc2025
SwitchableDesignLabel[doc]=Modern
SwitchableDesignLabel[doc2025]=Classic
```

| Setting | Meaning |
|---|---|
| `SwitchableDesigns[]` | The designs a visitor may switch to, besides the siteaccess's own `SiteDesign`, which is always the default. Empty: the switch is off |
| `SwitchableDesignLabel[<design>]` | The name shown for a design, translated in the context `kernel/uiswitch`; the design's directory name without one |
| `SwitchableDesignChain[<design>]` | The fallback chain of a design, designs separated by `;`, used as `AdditionalSiteDesignList[]` while that design is chosen. Without one, the siteaccess's own list stays |
| `DesignSwitchCookie` | The cookie that keeps the choice, `exp_design` by default |

A design is offered only when its directory exists, in `design/` or in the `design/` directory of an active design
extension. A siteaccess whose design list contains an administration design (`admin` …) never switches.

## How a visitor switches

Any address of the siteaccess with the view parameter `/(design)/<name>` shows the page in that design and stores the
choice in the cookie, for example `/manual/installing/(design)/doc2025`. Choosing the siteaccess's own design clears
the cookie again. A name that is not offered, or a design that is not installed, is ignored, and a cookie that names a
design no longer offered is cleared.

The switch is applied at the very start of the request, before the design, the translations and any template are set
up, so every template of the page comes from the chosen design.

## In a template

`fetch( 'uiswitch', 'front_designs' )` gives the switch: empty where the siteaccess does not switch.

```smarty
{def $switch = fetch( 'uiswitch', 'front_designs' )}
{if $switch}
<nav class="design-switch" aria-label="{'Design'|i18n( 'kernel/uiswitch' )}">
    {foreach $switch.designs as $design}
        {if $design.current}
            <span aria-current="true">{$design.label|wash}</span>
        {else}
            <a href={concat( $node.url_alias, '/(', $switch.parameter, ')/', $design.name )|ezurl}>{$design.label|wash}</a>
        {/if}
    {/foreach}
</nav>
{/if}
{undef $switch}
```

| Key | Value |
|---|---|
| `current` | The design of this page |
| `default` | The siteaccess's own `SiteDesign` |
| `parameter` | The view parameter name, `design` |
| `designs` | Each design with `name`, `label`, `current` and `default` |

## Caches

- A chosen design adds `fd-<design>` to the request's cache variant key, the same key the UI switch uses. It
  separates the design base cache, the compiled templates, `{cache-block}` and the content view cache, so a page in
  one design is never served in the other.
- Every response of a switching siteaccess sends `Vary: Cookie`, so a cache in front of the site keeps the designs
  apart.
- A page in a chosen design (not the default) is sent as `Cache-Control: private`. Pages in the default design keep
  whatever `[HTTPHeaderSettings]` gives them.
- A persistent Velocity worker puts the design settings back at the start of the next request.

## Related

- [UI switch](ui-switch.md): the interface choices of signed-in administration users.
- [Configuring Exponential 6.0](../../CONFIGURING.md): the settings layers and siteaccesses.
