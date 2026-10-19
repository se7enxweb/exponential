---
title: "About the project and its websites"
audience: user, editor, admin, developer, designer, partner, supporter
category: project
order: 80
summary: The About pages of exponential.earth explained - what Exponential is, who leads and operates the project, the name change, the family of websites (exponential.earth, Share Exponential, Exponential Projects, eZpedia, the documentation and the Foundation), the Exponential Translation Project, the package server of the setup wizard, the public demo, the Powered by Exponential banner and the project's free software values.
keywords: about, Exponential project, exponential.earth, 7x, Exponential Foundation, name change, Share Exponential, Exponential Projects, eZpedia, documentation project, Exponential Translation Project, ETP, package server, setup wizard, site packages, demo, Powered by Exponential, banner, free software, open source, FSF, distributions
type: help
---
# About the project and its websites

The [About](https://exponential.earth/about) section of the project website describes the project, the people behind
it and the websites around it. This page retells it, read in October 2026, and adds what the repository says where
that is more precise.

## What Exponential is

The website describes Exponential as an enterprise-grade open source content management system and development
framework for web publishing, media portals, intranets, e-commerce and extranets: a flexible content engine at the
core, a lot of functionality out of the box, and its real strength in how far it can be customized. It is free
software written in PHP that runs on Linux, macOS, Windows and BSD. The website says it has been installed tens of
thousands of times.

For the features in detail, see the [README](https://github.com/se7enxweb/exponential/blob/main/README.md) and
[Build with Exponential](build-with-exponential.md).

## Who runs the project

- **7x leads it.** The website's [Project](https://exponential.earth/about/project) page calls the project a
  community effort led by the company 7x, which fosters its growth, support and maintenance, and says the project is
  funded by donations of time and resources from 7x and its sponsors ([Who is 7x?](../who-is-7x.md)).
- **The Exponential Foundation operates it.** Every page of the website ends with "Developed by 7x" and says the
  project is operated by the Exponential Foundation, a public nonprofit corporation. The Foundation's mission, in the
  website's words, is to support the Exponential family of CMS and DXP projects and to educate, assist, guide and
  foster the growth of the software, its users and the community. Its website has two official addresses,
  [foundation.exponential.earth](https://foundation.exponential.earth/) and
  [exponentialfoundation.earth](https://exponentialfoundation.earth/)
  ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).
- **The name.** The project used the name eZ Publish until 11 August 2025, when it changed to "Exponential" or
  "Exponential (CMS)" at the request of the owner of the old name; the website adds that the project is not
  associated with the original software's developer ([/about/project](https://exponential.earth/about/project)).

## The family of websites

The website's [Projects](https://exponential.earth/about) list names the sites 7x runs for the community:

| Site | What it is for |
|---|---|
| [exponential.earth](https://exponential.earth/) | The project's home: news about the 6.0 releases, the FAQ, the roadmap, support and giving |
| [share.exponential.earth](https://share.exponential.earth/) | **Share Exponential**, the community gateway: forums, articles, blogs and tutorials from decades of community writing; also the official and the community roadmap |
| [projects.exponential.earth](https://projects.exponential.earth/) | **Exponential Projects**, the project forge for extensions, designs and bundles: project news, reviews, screenshots, downloads, RSS feeds, group project management, notifications for tracked projects and Git or GitHub integration ([chapter on Exponential Projects](../extension-development/part-1-from-user-to-developer/projects-exponential-earth.md)) |
| [ezpedia.exponential.earth](https://ezpedia.exponential.earth/) | **eZpedia**, the community wiki, built with Exponential |
| [exponential.doc.exponential.earth](https://exponential.doc.exponential.earth/) | The **Exponential Documentation Project**: official and community documentation ([Documentation sources](documentation-sources.md)) |
| [basic.exponential.earth](https://basic.exponential.earth/) | Exponential Basic |
| [platform.exponential.earth](https://platform.exponential.earth/) | The Exponential Platform distributions |
| [foundation.exponential.earth](https://foundation.exponential.earth/) | The Exponential Foundation |
| [status.exponential.earth](https://status.exponential.earth/) | Whether the project's services are up |

The chats, social media and issue trackers are on [Community channels](community-channels.md).

## The Exponential Translation Project

The [Exponential Translation Project](https://exponential.earth/about/etp) (ETP) restarted translation work for the
whole family, including Exponential 6.0: first an inventory of which translations exist and how complete they are,
then new languages, with the goal that every widely used language works out of the box. The state of each language
in this repository is on [Languages](../../languages.md); how to help is on
[Contribute to the project](contribute-to-the-project.md#translations).

## The package server

The setup wizard installs site designs, extensions and demo content from **packages**. 7x hosts the package server
that holds them, at `exponential.packages.exponential.earth`, with a mirror of its contents on GitHub in
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth)
([/about/packages](https://exponential.earth/about/packages)). You can propose your own site package by forking that
repository and sending a pull request, the website says.

> **Note**
> The website's packages page shows a `settings/override/package.ini.append.php` that points the wizard at the 6.0.9
> folder of the server, and says this is the default since 6.0.10. You do not need it: Exponential 6.0.15 already
> sets `RemotePackagesIndexURL` to the 6.0.15 folder of the same server in `settings/package.ini`. Only add an override
> if you want a different server or folder.

## The public demo

[demo.exponential.earth](https://demo.exponential.earth/) shows the latest Exponential 6 with the responsive 7x
Simple design and a shop, and [edit.demo.exponential.earth](https://edit.demo.exponential.earth/) lets you try the
administration; the website's [community page](https://exponential.earth/about/community) gives the sign-in details.
The demo's files, database and software are reset every day.

## Powered by Exponential

The website offers a "Powered by Exponential" banner, as PNG and WebP, for sites built with Exponential to show their
pride and promote the software ([/about/powered-by](https://exponential.earth/about/powered-by)). Partners have their
own rules for marks and logos ([Powered by Exponential for partners](../partners/powered-by-exponential.md)).

## Free software and open source

The website stresses that Exponential is free and open source software under the GNU GPL, and invites its users to
support the Free Software Foundation's work. It asks open source developers to contribute a pull request or file an
issue. What the license means in practice: [Licensing questions](licensing-faq.md).

## Related pages

- [The Exponential project: start here](README.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Who is 7x?](../who-is-7x.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
