---
title: Publishing on Share and Projects
audience: advocate
category: advocacy
order: 56
summary: How advocates publish on the community's own sites - Share (share.exponential.earth) for forums, blogs, articles and tutorials, and Projects (projects.exponential.earth) for extensions and community projects - what each is for, accounts, and a checklist for a good listing.
keywords: Share, share.exponential.earth, Projects, projects.exponential.earth, community portal, blogs, articles, tutorials, extensions, project listing, RSS, publishing, accounts
type: help
---
# Publishing on Share and Projects

This page is for advocates who want their posts, tutorials and extensions to be found where the community looks. The
two community sites are **Share** and **Projects**. Both are described from their public pages, read on
18 October 2026; check the sites for their current rules.

## Contents

1. [Share and Projects at a glance](#share-and-projects-at-a-glance)
2. [Accounts](#accounts)
3. [Publishing on Share](#publishing-on-share)
4. [Listing a project on Projects](#listing-a-project-on-projects)
5. [A good listing: checklist](#a-good-listing-checklist)
6. [Following new work](#following-new-work)

## Share and Projects at a glance

| | Share | Projects |
|---|---|---|
| Address | [share.exponential.earth](https://share.exponential.earth/) | [projects.exponential.earth](https://projects.exponential.earth/) |
| How it describes itself | "Share is the gateway to the Exponential Community ..." (site description) | "Download Exponential For Free and Join Our Community! Publish your own customized Exponential Extensions" (site description) |
| What you find | Forums, blogs, articles, news from 7x | New and updated projects, browse and search, RSS feeds |
| Use it for | Questions, answers, posts, tutorials | Extensions and community projects |
| Documented in | [Community places](../who-is-the-exponential-foundation.md#community-places) | the site itself |

## Accounts

- Share has a registration page ([share.exponential.earth/user/register](https://share.exponential.earth/user/register)).
- Projects announced that it "now supports logins and use from Share Exponential website accounts" (news item on the
  Projects site), so one account serves both.
- Use a name you are comfortable having public. Do not put private data in your profile.

## Publishing on Share

| You have | Put it in |
|---|---|
| A question | The [forums](https://share.exponential.earth/forums) (a forum for Exponential Platform exists too) |
| An answer to a common question | A forum post, then improve the help page ([Improving the help](improving-the-help.md)) |
| A blog post or tutorial | Share's blogs and articles, or your own blog with a forum post that links it |
| News about your extension | A forum post linking its Projects page |

Before posting, read the site's own rules and the forum's topic. Then follow
[Writing a blog post](writing-a-blog-post.md) or [Writing a tutorial that works](writing-a-tutorial-that-works.md).

## Listing a project on Projects

Projects has an "Add your project!" section. A project listing should point to the source of truth: the Git
repository and, for Composer packages, the package name.

Prepare:

1. **A public repository** with a README, a LICENSE file and a clear name ([Extensions](../../guides/extensions.md)).
2. **Version information in the extension's own files**: `ezinfo.php` and `extension.xml` carry the release number
   ([Extensions, Release it](../../guides/extensions.md#7-release-it)).
3. **Installation instructions**: `composer require` and the settings to enable it.
4. **Compatibility**: which Exponential versions and PHP versions you tested.
5. **Screenshots** with alternative text and no personal data.

## A good listing: checklist

```text
[ ] Name: clear, uses "exp" or your own prefix for new work; never misuses the product name
[ ] One-sentence summary: what it does for whom
[ ] License stated (and the same as in the repository)
[ ] Link to the repository and, if any, the Composer package
[ ] Version, release date, tested Exponential and PHP versions
[ ] Install and enable steps
[ ] Where to report issues (the repository's tracker)
[ ] Screenshots with alt text; no secrets, no real personal data
[ ] Credits to the original author if it is a fork or port
```

Naming guidance for new things is on [Brand and naming rules, Names for new things](../business/brand-and-naming-rules.md#names-for-new-things).

## Following new work

Projects offers RSS feeds (projects, project news, news, forums, Git activity, as linked on its home page). Following
them is an easy way to find new extensions to try, review or write about
([Writing a blog post](writing-a-blog-post.md)).

## Related pages

- [The community channels](community-channels.md)
- [Contributing to eZpedia](contributing-to-ezpedia.md)
- [Extensions](../../guides/extensions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
