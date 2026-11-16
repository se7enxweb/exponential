# The preview in the siteaccess and on the host the content belongs to (6.0.15)

Read this page if one Exponential installation serves several websites from one content tree (a site family: a main
site and microsites, each with its own siteaccess and often its own host), or if you write code that needs to know on
which site a node is published.

## What changed

Before 6.0.15 the preview of a version (Content > Preview, `content/versionview`) chose its siteaccess by language
alone: the first siteaccess of `site.ini [SiteAccessSettings] RelatedSiteAccessList[]` whose
`[RegionalSettings] ContentObjectLocale` was the language of the version, else `[SiteSettings] DefaultAccess`. A page
of a microsite was therefore shown in the main site's design, with its menu, footer and path prefix, and with links to
the main site's paths. The preview was also always rendered on the administration's host, so everything that depends
on the host (the canonical address, hreflang, absolute links, cookies) differed from the published page. The editor
could pick another siteaccess, but had to know which one.

Since 6.0.15:

1. **The siteaccess comes from the tree.** The preview preselects the siteaccess whose content root contains the node,
   in the language of the version.
2. **The host comes from the siteaccess.** When that siteaccess is served on another host, the preview is shown on
   that host, with a signed one-time token, so the editor does not have to sign in there.
3. **"View on site"** of a node (admin4's node view) opens the published page on the site and host it belongs to.

The siteaccess selector of the preview keeps working: it shows the chosen siteaccess selected, and a siteaccess the
editor picks there is kept.

## Which siteaccess a node belongs to

`expSiteAccessForNode` (`kernel/classes/expsiteaccessfornode.php`):

- **Candidates:** the siteaccesses of `RelatedSiteAccessList[]` of the siteaccess the preview is opened in (the
  administration), `AvailableSiteAccessList[]` when that list is empty. Administration and editor siteaccesses are left
  out (`expSiteAccessOverview::kindOf()`: `[SiteAccessSettings] SiteAccessType`, else the design and
  `RequireUserLogin`).
- **Content root of a siteaccess:** the deepest of `content.ini [NodeSettings] RootNode`, the node of
  `site.ini [SiteSettings] IndexPage` (`/content/view/full/<node>` or a URL alias) and the node of
  `[SiteAccessSettings] PathPrefix`. A siteaccess contains a node when its root is the node or an ancestor of it.
- **Most specific wins:** of the siteaccesses that contain the node, the one with the deepest root. A microsite under
  node 60 wins over the main site rooted at node 2, whatever the order of the list.
- **Language:** between equally deep roots, the siteaccess whose `ContentObjectLocale` is the language of the version
  comes first, then one whose `SiteLanguageList[]` has it (earlier in its list first), then the others; the order of
  the list decides last. A microsite that has no siteaccess in the version's language still wins over the main site:
  the node is published there.
- **Nothing contains the node** (a node under Media or Users, for instance): the rule before 6.0.15 applies, the first
  related siteaccess whose `ContentObjectLocale` is the language, else `DefaultAccess`.

The candidates are read once per request (a persistent Velocity worker reads them again for the next request).

Example: a main site `main_ger` / `main_eng` rooted at node 2, a wedding microsite `wedding_ger` (RootNode or
IndexPage node 60) and a forum `forum_eng` / `forum_ger` (node 70):

| Node path | Version language | Preselected |
|---|---|---|
| /1/2/60/61/ | ger-DE | `wedding_ger` |
| /1/2/60/61/ | eng-GB | `wedding_ger` (the only site that publishes it) |
| /1/2/70/71/ | eng-GB | `forum_eng` |
| /1/2/80/ | eng-GB | `main_eng` |
| /1/43/51/ | ger-DE | the old rule: the first with `ContentObjectLocale=ger-DE`, else `DefaultAccess` |

## On which host the preview is shown

The address of the chosen siteaccess is the one the administration's "Open the site" link uses
(`ezpSiteAccessURL::root()`): the host and path the matching rules (`MatchOrder`, `HostMatchMapItems`,
`HostUriMatchMapItems`, the siteaccess's `[SiteSettings] SiteURL`) lead to that siteaccess.

- **Matched by URI** on the administration's host (`https://admin.example.com/main_eng/`): the preview stays as it
  was, `content/versionview/<object>/<version>/<language>/site_access/<siteaccess>`, rendered in the administration's
  request with the siteaccess's settings.
- **Served on another host** (`https://wedding.example.com/`): `content/versionview` with that siteaccess answers the
  preview frame with a redirect to `https://wedding.example.com/content/hostpreview/<token>`. The frame, the "open in a
  new window" link and the link under the frame all go through it, in every administration design (admin, admin3,
  admin4, adminui) without a change to their templates.

The preview stays on the administration's host (as before) when the target host is not one a redirect may go to
(`eZRedirectManager::isOwnHost()`: the current host, `[SiteSettings] AllowedRedirectHosts[]` and the hosts of the
host maps), when the siteaccess needs a login (`RequireUserLogin=true`), when the secret store is not set up, or when
`PreviewOnOwningHost=disabled`.

### The one-time token

`expContentHostPreview` (`kernel/classes/expcontenthostpreview.php`), view `content/hostpreview/<token>`:

- **Signed:** the token carries the user, object, version, language, siteaccess, view mode, placement, expiry, a nonce
  and the origin of the administration, signed with HMAC-SHA256 under the secret `content.hostpreview.key` of the
  [secret store](secret-store.md) (made with the first token). Removing the secret on Setup > Secrets ends every token
  at once.
- **Short-lived:** `PreviewTokenLifetime` seconds (default 300, between 60 and 900).
- **Single use:** issuing it writes the row `exp_hostpv_<nonce>` of `ezsite_data`; showing it renames that row to a
  name only the showing request knows and removes it, so a second request with the same token (a reload, a copied
  address) answers 410 and asks to open the preview again in the administration. Opening the preview again in the
  administration issues a new token. Rows of tokens that were never used are removed after they expire.
- **Bound:** the token works only on the siteaccess it names (another siteaccess answers 404 and does not use it up),
  only while the user who asked for it exists and is enabled, and only for the version and language it names.
- **Nothing more:** `content/hostpreview` needs no policy (`site.ini [RoleSettings] PolicyOmitList[]`); the token is
  the permission to see that one version once. Nobody is signed in and no session is started on the site; menus and
  everything else around the version are what an anonymous visitor of that siteaccess sees. Issuing a token needs
  what the preview always needed: `content/versionread` of the version.
- **No cache, no index:** `Cache-Control: private, no-store`, kept out of the role-aware HTTP cache and Velocity's
  response cache, `X-Robots-Tag: noindex, nofollow`, `Referrer-Policy: no-referrer`.
- **Framing:** the administration shows the preview in a frame. `content/hostpreview` removes `X-Frame-Options` and
  adds the administration's origin (from the token) to the `frame-ancestors` of every `Content-Security-Policy` the
  response already has. Browsers ignore `X-Frame-Options` when a policy has `frame-ancestors`, so an
  `X-Frame-Options: SAMEORIGIN` that the web server adds on its own (nginx `add_header`, Apache `Header set`) does not
  block the frame; a `Content-Security-Policy` with its own `frame-ancestors` added by the web server or a CDN does,
  and must name the administration too (otherwise use "open in a new window", or switch `PreviewOnOwningHost` off).
- **Cross-site request forgery:** the token is only made inside `content/versionview` for the signed-in editor and
  only shows content; it changes nothing, so a forged request gains nothing.

A shared session cookie domain (`.example.com` for all hosts) is not used for this: a session on another siteaccess
would also need `user/login` there and `content/versionread` in that siteaccess's limitations, which an editor often
does not have on a public site. The token works the same everywhere.

## "View on site"

The template operator `exp_node_site_url( $node[, $language] )` (`kernel/common/expnodesiteurloperator.php`) gives the
address a node is published at on its own site: the siteaccess `expSiteAccessForNode` chooses, its address, and the
node's URL alias in that siteaccess's languages without its `PathPrefix` (`expOrderSiteaccess::nodeURL()`). admin4's
node view uses it for its "View on site" button. Published content needs no token. A hidden node on a siteaccess that
does not show hidden nodes is not on the site: the link is then the preview of its current version in the
administration (`content/versionview`, which checks `content/versionread`). With `PreviewInOwningSiteAccess=disabled`,
or for a node no siteaccess contains, the link is the administration's site and the node's URL alias, as before.

## Settings

`site.ini`, read in the siteaccess the preview is opened in (the administration):

```ini
[SiteAccessSettings]
# preselect the siteaccess the node belongs to; disabled: the rule before 6.0.15
PreviewInOwningSiteAccess=enabled
# show the preview on the host of a siteaccess served on another host; disabled: always on this host
PreviewOnOwningHost=enabled
# lifetime of the one-time token, in seconds (60 to 900)
PreviewTokenLifetime=300
```

Upgrading needs nothing: both switches are on by default, an installation with one site (or with siteaccesses
matched by URI only) sees the same preview as before, and every setting the old rule read is still read.

## For developers

```php
$chosen = expSiteAccessForNode::forNode( $node, 'ger-DE' );
// array( 'siteaccess' => 'wedding_ger', 'reason' => 'tree', 'depth' => 3 ); reason 'default': no root contains it
$base = expSiteAccessForNode::baseURL( $chosen['siteaccess'] );   // https://wedding.example.com/

// pure, without settings or a database:
expSiteAccessForNode::resolve( array( 1, 2, 60, 61 ), 'ger-DE', $candidates, 'main_ger' );

$token = expContentHostPreview::issue( $version, 'ger-DE', 'wedding_ger', 'full', 0, 'https://admin.example.com' );
$shown = expContentHostPreview::redeem( $token, 'wedding_ger' ); // array( claims, object, version ) or array( error )
```

Tests: `tests/tests/kernel/classes/expSiteAccessForNodeTest.php` (root containment, most specific root, language,
fallback, path strings, host comparison, switches) and `tests/tests/kernel/classes/expContentHostPreviewTest.php`
(signature, tampering, expiry, siteaccess binding, single use, claim forms, lifetime, frame-ancestors), both without a
database.

## Related

- [Preview links: a draft for a reviewer without an account](preview-links.md)
- [The secret store](secret-store.md)
- [Safe redirects](safe-redirects.md)
- [Cross-site links in a multisite installation](multisite-cross-site-links.md)
- The settings reference: [site.ini \[SiteAccessSettings\]](../../examples/ini-settings/site/SiteAccessSettings.md)
