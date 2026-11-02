# Static publishing for multisite installations

This guide is for administrators of an Exponential 6.0.15+ installation that serves several sites or languages from
one database. It shows how to give each its own target, keep them apart and publish them on their own schedule.

[Guides](README.md) · Related: [Languages on a static site](../help/static-publishing/languages-on-a-static-site.md),
[Static publishing: an overview](static-publishing.md)

## What you'll have at the end

Either one target per public siteaccess, each with its own repository or branch, public address and policies, or one
target that publishes several siteaccesses sharing one host by their URI prefix (`/`, `/bold/`, `/bold_ger/`) into
one copy, all listed and built from one installation.

## Several siteaccesses in one copy

When several sites or languages live on one host and are told apart by the first part of the address (site.ini
`[SiteAccessSettings] MatchOrder` with `uri`, `URIMatchType=element` or `map`, or `host_uri` with a URI), one target
can publish them together, so the copy keeps the same addresses as the live host:

```ini
[Target_sites]
Name=All sites on www.example.com
SiteAccessList[]
SiteAccessList[]=site
SiteAccessList[]=bold
SiteAccessList[]=bold_ger
DefaultSiteAccess=site
Remote=https://github.com/example/sites.git
PublicURL=https://www.example.com/
CustomDomain=www.example.com
```

- The `DefaultSiteAccess` is served at the root of the copy (`/`, `/about-us/`); every other siteaccess is in the
  folder of its URI prefix (`/bold/`, `/bold_ger/team/`), exactly where the live host serves it.
- Without a `DefaultSiteAccess` every siteaccess is in its folder (`/site/` too), and the root of the copy is a small
  page that links to each of them. A target of one siteaccess always has it at the root.
- `SiteAccess=` still works as the form of one; when `SiteAccessList[]` names any, `SiteAccess` is not read.
- The checks: only front siteaccesses (never an administration or editing one); every siteaccess but the default must
  be selected by a URI prefix, or it has no folder in a copy shared with others on one host (a siteaccess matched by
  its host alone can be the `DefaultSiteAccess`, or a target of its own); two siteaccesses cannot share a folder.
- Links between the siteaccesses of the copy stay in the copy (relative, or below the base path): the language
  switcher from `/about-us/` to `/bold_ger/` works offline. A link to a siteaccess the target does not publish
  (`/classic/...`) is a link outside the copy, and `OutsideLinks` decides it: to the live site, or taken out.
- The language switcher (`/bold/switchlanguage/to/bold_ger/careers`) goes to the same page in the other siteaccess of
  the copy (`/bold_ger/karriere/`), or to its front page when the copy holds no page of that node there.
- `hreflang` alternates between them are written as absolute addresses below `PublicURL`.
- One `sitemap.xml` lists the pages of all of them, one `robots.txt` points at it, and `404.html` is the default
  siteaccess's 404 page (the first siteaccess's without a default). Redirect pages are written per siteaccess, in its
  folder.
- The search index is kept per language as before; each entry names its siteaccess, and the search page offers "All
  sites" or one of them (`search.html?site=bold_ger`).
- `MaxPages` counts per siteaccess. The manifest names the siteaccesses and their folders, and each page's
  siteaccess; staleness, history and the "Publish this page" panel work on the page paths of every siteaccess.
- A content publish marks the node's pages stale in every siteaccess of the copy; `--nodes` builds a node's page in
  each of them.
- Build one of them only with `./console exp:static:build sites --only-siteaccess=bold_ger`: a partial build, the
  pages of the others stay as the last build left them. (`--siteaccess` stays the script option that chooses whose
  settings are read.) `--paths` takes paths of the copy (`/bold_ger/team`).

In the wizard, step 1 lists the front siteaccesses with their folder: tick the ones to publish and choose the one at
the root (or none). One ticked siteaccess is saved as `SiteAccess=` as before.

## One target per siteaccess

A target can also publish exactly one front siteaccess. Its pages, languages, design and address scheme are that
siteaccess's. An installation with the sites `site`, `shop_eng` and `blog` and a German `site_ger` has one target for
each that should have a public copy:

```ini
[StaticPublishSettings]
Enabled=enabled
TargetList[]
TargetList[]=site
TargetList[]=site-ger
TargetList[]=blog

[Target_site]
SiteAccess=site
Remote=https://github.com/example/site.git
PublicURL=https://www.example.com/
CustomDomain=www.example.com

[Target_site-ger]
SiteAccess=site_ger
Remote=https://github.com/example/site-de.git
PublicURL=https://de.example.com/
CustomDomain=de.example.com

[Target_blog]
SiteAccess=blog
Remote=https://github.com/example/blog.git
BasePath=/blog/
PublicURL=https://example.github.io/blog/
```

Leave out the sites that cannot be static (the shop here); they stay on your hosting, and links from the copies to
them go to the live site.

- Each target has its own folder (`var/<site>/static-publish/<target>/`), its own git repository, tags
  `static/<target>/<n>`, history, lock and stale pages. Two targets never share a folder.
- One repository per target is the simplest. Two targets may share a repository on different `Branch`es; a GitHub
  repository serves Pages from one branch only, so this suits other hosts or a branch you merge yourself.
- `exp:static:targets` lists them all and checks each; the dashboard block shows each with its stale pages.

## Languages

A language that is its own siteaccess gets its own target, as above. The `hreflang` links of every page are written
as absolute addresses: to the page in this copy when it holds it, otherwise to the live site. To make the language
switcher lead from one copy to the other, set each target's `LiveURL` to the other copy's public address where it
fits, or link the copies in the design. The search index of a copy is kept per language.

## Credentials and permissions

- One secret can serve several targets (a token with access to all their repositories), or one deploy key per
  target (`exp:static:keys <target> --generate`). A deploy key is limited to one repository by design.
- The policies `staticpublish/*` take a Target limitation: give the blog's editors `build` and `publish` for the
  `blog` target only.
- `exp:static:targets --export=site,blog` moves targets to another installation as a migration; the credential
  travels as its name.

## Building them

Builds of different targets may run side by side; one target runs one job at a time. On a busy server, give each
target its own `Schedule` or rely on `Triggers[]=publish`, which builds only the pages a content publish made stale.
Publishing content that several sites show marks its pages stale in every target with `Triggers[]=publish`, and
the cronjob part `staticpublish` builds each of them after the debounce.

## Next step

[Static publishing on Windows and clusters](static-publishing-on-windows-and-clusters.md).
