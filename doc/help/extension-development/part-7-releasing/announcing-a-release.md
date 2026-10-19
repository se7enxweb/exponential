---
title: Announcing a release
audience: developer, designer, app-creator
category: extension-development
order: 120
summary: Tell the people who use your extension about a release - the GitHub release itself, share.exponential.earth, the project page on projects.exponential.earth, ezpedia - what to write, and what to leave out.
keywords: announce, release, share.exponential.earth, projects.exponential.earth, ezpedia, GitHub release, community, users, security release
type: help
---
# Announcing a release

Part 7, chapter 12. A release nobody hears about helps nobody. Most users of an extension learn about updates from
`composer outdated` or from a notification. Some need to be told, and new users need to find the extension. This
chapter covers where to announce and what to say.

## Where

| Place | Reaches | What to post |
|---|---|---|
| The GitHub release | everyone who watches the repository | the release notes ([GitHub releases and release notes](github-releases-and-release-notes.md)) |
| [share.exponential.earth](https://share.exponential.earth) | the community: its forums include one for extensions | a short post for releases that matter: a new extension, a major version, a security fix |
| [projects.exponential.earth](https://projects.exponential.earth) | people browsing the directory of community projects | keep the extension's project page current (version, links); see [projects.exponential.earth](../part-1-from-user-to-developer/projects-exponential-earth.md) |
| [ezpedia.exponential.earth](https://ezpedia.exponential.earth) | readers of the knowledge base | an article when the release brings a technique worth explaining |

Not every patch release needs a post. A new extension, a new major version and every security release do.

## What to write

A post is the release notes for people who do not know the extension yet:

1. **One sentence on what the extension does.**
2. **What is new**, in two to five lines, as users see it.
3. **Who should update, and how urgently.** "Everyone on PHP 8.5" or "Only if you use the newsletter import".
4. **How to get it**: the `composer require` line and the link to the release.
5. **Thanks** to contributors, by their public names or accounts, if they want to be named.

## Security releases

For a release that fixes a vulnerability:

- Publish the fix first, then announce. Never describe the problem before users can update.
- Say clearly that it is a security release, which versions are affected, and what users must do.
- Describe the risk in general terms; do not publish an exploit.
- Credit the person who reported it, if they agree ([The maintainer path](../part-5-extend-fork-contribute/the-maintainer-path.md)).

## What not to post

- Private information: customers, internal host names, unreleased plans of other people.
- Promises of dates you do not control.
- The same post in every channel at once: one main post, links elsewhere.

## Related pages

- Previous: [Testing before a release](testing-before-a-release.md)
- Next: [The maintenance life cycle](the-maintenance-life-cycle.md)
- [Growing the community through code](../part-5-extend-fork-contribute/growing-the-community-through-code.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
