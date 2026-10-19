---
title: Feeds and syndication
audience: businesses, sales-marketing, admin
category: business-solutions
order: 70
summary: How a business distributes content beyond its website with Exponential - RSS, Atom, OPML and Apple Podcasts feeds, feed imports, JSON Feed output through the remote services, and the syndication extension that shares content between installations one way or both ways.
keywords: feeds, RSS, Atom, OPML, podcast, Apple Podcasts, JSON Feed, feed import, syndication, content sharing, partner sites, networks, business
type: help
---
# Feeds and syndication

This page is for owners, marketing leads and administrators who want their content to travel: into feed readers and
news aggregators, into podcast directories, onto partner sites, or between several Exponential installations of their
own. It explains the options in business terms and links to the guides for the people who set them up.

## Contents

1. [Why feeds still matter](#why-feeds-still-matter)
2. [Publishing feeds](#publishing-feeds)
3. [Podcasts](#podcasts)
4. [Importing other sites' feeds](#importing-other-sites-feeds)
5. [Syndication between installations](#syndication-between-installations)
6. [Choosing the right tool](#choosing-the-right-tool)
7. [Feed checklist](#feed-checklist)

## Why feeds still matter

A feed is a standard file that other programs read without an agreement or an API key: feed readers, aggregators,
podcast apps, partner sites and your own newsletter tools. For a small business it is the cheapest way to let others
show your latest news, products or episodes. The README lists RSS, ATOM, iTunes RSS and OPML feeds among
Exponential's main features.

## Publishing feeds

From [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md):

| | Export | Import |
|---|---|---|
| What | A feed this site publishes | Another site's feed read into this site |
| Address | `/rss/feed/<address>` | none; it creates objects |
| Formats | RSS 1.0, RSS 2.0, Atom, OPML, Apple Podcasts | what the other site offers |
| Needs a cronjob | no | yes |

Setting up an export takes a few minutes in **Setup > RSS**: a name and address, sources (a folder, the class to list,
which fields become title and description), a format and a number of items. The export card shows when the cached
copy was last written and marks exports that need attention. The administrators' guide is
[RSS feeds: publishing exports and reading imports](../../../guides/rss-feeds.md).

The remote services can also produce "RSS, Atom and JSON Feed output of any subtree"
([Remote services](remote-services.md#what-can-be-done-through-it)).

> **Tip**
> Add an autodiscovery link in the page head and a visible feed link on your news page, so readers and browsers find
> the feed ([Letting browsers and readers find it](../../creators/website-creators/rss-feeds-for-your-site.md#letting-browsers-and-readers-find-it)).

## Podcasts

An RSS export can be written as **Apple Podcasts (RSS 2.0 + iTunes)**, so "a show published here can be submitted to
Apple without a hand written feed" ([RSS: Apple Podcasts feeds](../../../features/6.0/rss-podcast-and-feed-list.md)).
Channel fields such as owner, artwork and category are filled in the export form; each episode's identifier is the
object's remote id, which "never changes, so readers do not see old episodes as new". An episode without a playable
audio file is left out, since Apple rejects it.

## Importing other sites' feeds

An import reads another site's feed and creates ordinary objects from it in a folder you choose; it needs the
`rssimport` cronjob, and "nothing is read without it". Removing the import keeps the objects. The guide's advice is
plain: "Only import feeds you are allowed to republish"
([Importing a feed](../../creators/website-creators/rss-feeds-for-your-site.md#importing-a-feed)).

## Syndication between installations

The `syndication` extension, required by the root `composer.json`, "exports part of the content tree of one site as a
feed, and another site imports it, kept in step by cron" ([syndication](../../../features/6.0/extensions/syndication.md)).
It supports:

- **One-way syndication**, "for example shared content or authentication data between separate databases and client
  sites";
- **Two-way syndication**, "for example forums, portal networks, social platforms and large e-commerce networks".

What a business should know:

| Topic | From the source |
|---|---|
| Transport | Cached XML plus binary files, served through a SOAP interface |
| Protection | Since 1.2.0 an import can send a login and password, so the exporting site can protect its endpoint with HTTP access control |
| Operation | A Syndication page in the administration shows what is exported and imported, when the cronjobs last ran and what needs attention; console commands show status and run exports and imports |
| Load | The export cronjob is suggested every 3 minutes, "since it carries much load once running" |
| Databases | Since 1.3.1 its tables can be created on every database Exponential supports, not only MySQL |

Typical business uses: a head office that feeds news to branch sites on their own installations; a group of
associations that share an events calendar; a manufacturer that supplies product content to dealers who run their own
Exponential sites ([Selling through networks and partners](selling-through-networks-and-partners.md)).

## Choosing the right tool

| You want | Use |
|---|---|
| Anyone may read your latest items | An RSS or Atom export |
| Your show in podcast apps | An Apple Podcasts export |
| A list of your feeds for readers | An OPML export ([OPML exports](../../../bc/6.0/opml.md)) |
| Another site's news shown on yours | An RSS import |
| Content shared with another Exponential installation, kept in step | `syndication` |
| Content for your own app | The REST API ([Headless with the REST API](headless-with-the-rest-api.md)) |
| Several brands on one installation | No feed needed: share content directly ([Sharing content across brands](../multisite/shared-content-across-brands.md)) |

## Feed checklist

- [ ] Each export has a source, an address and a format, and is active.
- [ ] Links in the feed point to the public siteaccess, in the right language.
- [ ] The page head links the feed for autodiscovery.
- [ ] `robots.txt` does not block `/rss/feed/` if you want feeds found.
- [ ] Imports run by cron into a folder editors check, and only from sources you may republish.
- [ ] Syndication endpoints are protected, and their credentials are kept out of version control.

## Related pages

- [Selling through networks and partners](selling-through-networks-and-partners.md)
- [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md)
- [RSS feeds: publishing exports and reading imports](../../../guides/rss-feeds.md)
- [syndication](../../../features/6.0/extensions/syndication.md)
- [Feeds for new acquisitions (libraries)](../libraries/feeds-for-new-acquisitions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
