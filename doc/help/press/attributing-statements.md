---
title: Attributing statements
audience: press
category: press-media
order: 138
summary: How to attribute each kind of statement about Exponential correctly - repository files, documentation, the organizations' websites, press releases, social posts, interviews, community members and third parties - with attribution phrases for each and the mistakes that change a statement's weight.
keywords: attribution, attribute, according to, sources, statement, README, documentation, website, press release, social media, interview, community member, spokesperson
type: help
---
# Attributing statements

This page is for reporters and editors. Who said something changes what it means for the reader: a fact in a Git
tag, a claim on a company website and an opinion in a forum are three different things. This page gives the right
attribution for each source around Exponential.

## Contents

1. [Attribution by source](#attribution-by-source)
2. [Facts you can state without "according to"](#facts-you-can-state-without-according-to)
3. [Mistakes that change the weight](#mistakes-that-change-the-weight)
4. [Dating your attribution](#dating-your-attribution)

## Attribution by source

| Source | What it is | Phrase |
|---|---|---|
| Git tags and history | A record of what was published, and when | "the project's release history shows", or state it as fact |
| README, SECURITY.md, CONTRIBUTING.md | The project's own statements in its repository | "the project's README says", "its security policy states" |
| This help and `doc/` | Documentation written by the project and its contributors | "according to the project's documentation" |
| exponential.earth | The project website, operated by the Foundation | "the project website says" |
| foundation.exponential.earth | The Foundation's self-description | "the Foundation says on its website" |
| se7enx.com | 7x's self-description and offers | "7x says on its website" |
| A press release | A dated statement by its issuer | "7x said in a press release on <date>" |
| A social media post | A statement of the account's owner | "<account> wrote on <network> on <date>" |
| An interview or e-mail to you | A statement by a person for an organization | "<name>, <role> at <organization>, said" |
| A community member, advocate or partner | Their own opinion or experience | "<name>, who <relationship to Exponential>, said" |
| A third-party article | The author's account | "<outlet> reported"; check against primary sources ([Where the facts live, for the press](where-the-facts-live-for-press.md)) |

## Facts you can state without "according to"

Facts that anyone can check in a public record can be stated plainly if you checked them:

- "Exponential 6.0.14 was tagged on 7 June 2026" (Git tag).
- "The software is licensed under the GNU GPL, version 2 or later" (LICENSE).
- "The repository's oldest commit dates from September 2002" (`git log`).

Self-descriptions and evaluations always keep their attribution: "the Foundation describes itself as a 501(c)(3)
nonprofit", "the README calls Velocity the recommended engine".

## Mistakes that change the weight

| Mistake | Effect | Fix |
|---|---|---|
| "7x said" for a README sentence | Turns documentation into a spokesperson | "the README says" |
| "experts say" for an advocate's post | Inflates an opinion | Name the person and their relationship |
| "the company claims" for a checked fact | Casts doubt on a verifiable fact | State the fact with its source |
| A press release from 2024 as "said today" | Misdates a statement | Keep the release date |
| "the Exponential team" for one contributor | Inflates one voice to a group | Name the contributor's role |
| "Ibexa says" for the rename notice | The notice is the project's, about Ibexa's request | "the project website says it renamed at Ibexa's request" |

## Dating your attribution

Websites change. When you attribute a website statement, add when you read it, at least in your notes and ideally
in the text: "the Foundation's website said on 18 October 2026". The repository keeps its history, so a file can
always be cited at a commit ([Verifying dates](verifying-dates.md)).

## Related pages

- [Quotes policy](quotes-policy.md)
- [Fact-checking guide](fact-checking-guide.md)
- [Two organizations, one project](two-organizations-one-project.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
