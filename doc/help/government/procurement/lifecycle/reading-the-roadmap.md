---
title: Reading the roadmap as a public body
audience: government, project-manager, sales-marketing
category: public-sector
order: 50
summary: Where the Exponential roadmap lives, why a public body should read it as a statement of intent rather than a commitment, how to check roadmap items against the changelog, and how to request or fund a feature the organization needs.
keywords: roadmap, plans, future features, statement of intent, feature request, sponsorship, changelog, community roadmap, procurement, public sector
type: help
---
# Reading the roadmap as a public body

This page is for project managers and evaluation panels who want to know where Exponential is going. It explains
where the roadmap is, how much weight to give it in a procurement, and how a public body can influence it. The source
is [The roadmap: what is planned and how to shape it](../../../project/roadmap.md).

## Contents

1. [Where the roadmap is](#where-the-roadmap-is)
2. [Intent, not commitment](#intent-not-commitment)
3. [Checking an item against the repository](#checking-an-item-against-the-repository)
4. [Requesting a feature](#requesting-a-feature)
5. [In an evaluation](#in-an-evaluation)

## Where the roadmap is

The project keeps two public roadmaps on Share Exponential, both linked from exponential.earth
([The roadmap](../../../project/roadmap.md)):

| Roadmap | What it is |
|---|---|
| Official roadmap | the plans of 7x and the project, product by product |
| Community roadmap | feature requests from users, discussed in public, moderated |

The official roadmap read in October 2026 covered 2025/Q4 to 2026/Q2. For Exponential 6.0.x it named, among other
items, stabilization, more templates in the default design, video guides, a partner portal and PHP 8.6 support
estimated for 2026/Q3. The full list is on [The roadmap](../../../project/roadmap.md#what-the-official-roadmap-lists).

## Intent, not commitment

The help states it plainly: "The changelog is the authoritative record; the roadmap is a statement of intent"
([The roadmap](../../../project/roadmap.md#what-has-already-been-delivered)). The roadmap also lagged behind the
releases when it was read: it named 6.0.10 as current while 6.0.14 was already tagged.

For a public body this means:

- **Never buy on a roadmap item.** Score what exists today and can be demonstrated.
- If a requirement depends on a future feature, make its delivery a contract obligation of your supplier, with
  acceptance criteria, not an assumption about the open source project.
- Read the roadmap to judge direction and activity, not dates.

## Checking an item against the repository

Before you rely on any statement about a feature, check it:

1. Search the changelog of the current line ([6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md)).
2. Look for a feature page in `doc/features/6.0/` (for example [Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md)).
3. Ask for a demonstration on a test installation; see the evaluation part of this procurement section.

The roadmap page itself shows this method: it lists roadmap items with what the repository shows
([The roadmap](../../../project/roadmap.md#what-has-already-been-delivered)).

## Requesting a feature

| Way | How |
|---|---|
| Ask | add it to the community roadmap on Share Exponential; explain the problem, not only the solution |
| Report a gap | open an issue in the project's issue tracker on GitHub |
| Discuss | the forums and chats ([Community channels](../../../project/community-channels.md)) |
| Build | have your supplier contribute it as a pull request ([Contribute to the project](../../../project/contribute-to-the-project.md)) |
| Fund | sponsor the work ([Give and donate](../../../project/give-and-donate.md)); partners have their own route ([Roadmap participation](../../../partners/roadmap-participation.md)) |

A public body that pays a supplier to build a generally useful feature can require in the contract that it is
contributed back to the project under the GPL. Everyone then maintains it, not only you.

## In an evaluation

Questions a panel can ask bidders:

- Which roadmap items does your offer depend on? (Ideally: none.)
- Which features you propose to build would you contribute back, and how?
- How do you follow the project's releases and changelog?

## Related pages

- [The product lifecycle](README.md)
- [The roadmap: what is planned and how to shape it](../../../project/roadmap.md)
- [Release policy and version numbers](release-policy-and-version-numbers.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
