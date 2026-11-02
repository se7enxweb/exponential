# Static publishing for multisite installations

This guide is for administrators of an Exponential 6.0.15+ installation that serves several sites or languages from
one database. It shows how to give each its own target, keep them apart and publish them on their own schedule.

[Guides](README.md) · Related: [Languages on a static site](../help/static-publishing/languages-on-a-static-site.md),
[Static publishing: an overview](static-publishing.md)

## What you'll have at the end

One target per public siteaccess, each with its own repository or branch, public address and policies, all listed
and built from one installation.

## One target per siteaccess

A target publishes exactly one front siteaccess. Its pages, languages, design and address scheme are that
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
