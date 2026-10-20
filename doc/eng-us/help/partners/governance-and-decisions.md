---
title: "Governance: who decides what"
audience: partner
category: partnership
order: 270
summary: Who decides what in the Exponential world as far as the sources say - the code, releases, security, the partner program, the Foundation's mission and funding - how partners can take part in decisions, and what governance questions are still to be defined by the Foundation.
keywords: governance, decisions, who decides, Foundation, 7x, releases, roadmap, pull requests, partner program, board, transparency
type: help
---
# Governance: who decides what

This page is for partners who need to know where decisions are made: before promising a customer a feature, before
investing in an extension, before planning a business on the program. It uses only published sources.

## The decisions and where they are made

| Decision | Who, according to the sources | Source |
|---|---|---|
| The project as a whole | "This project is operated by the Exponential Foundation" | <https://exponential.earth/about/project> |
| Development, support, adoption, community growth | "7x took over leadership of the project and it's development, support, adoption and community growth in 2023" | README.md |
| What gets merged | Pull requests are reviewed; every pull request is merged, once its author has squashed and rebased it onto `main` as the reviews ask; after the merge `main` is never rewritten and later fixes come as new pull requests | CONTRIBUTING.md, `doc/en/us/bc/6.0/ci.md` |
| Releases and versions | Maintainers tag releases; "A published tag is never changed" | SECURITY.md |
| Security fixes | Made on `main` and "published with the next 6.0.x release"; reports privately to security@se7enx.com | SECURITY.md |
| The partner program | 7x: "7x reserves full rights to change or administer this program" | <https://exponential.earth/partner/program> |
| The Foundation's funding and direction | The Foundation: its mission covers "Funding, Adoption, Support, Direction, Education, Development, Promotion and Documentation" | <https://foundation.exponential.earth/> |
| The software license | Fixed: GPL v2 or later for code, GFDL 1.3 or later for documentation | LICENSE, `doc/en/us/README-LICENSE.md` |

## What is not published

[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#what-the-sources-do-not-say) states
that no public source says "who sits on the Foundation's board, how it is governed, whether it has members besides
its supporters, and what its budget is". For partners this means the following are **to be defined by the
Foundation**:

- whether partners have a formal voice (an advisory council, a vote, a seat);
- how roadmap decisions are made and published;
- how the partner program and the Foundation relate.

## How partners take part today

| Channel | Influence | Page |
|---|---|---|
| Pull requests and issues | Direct: code and decisions are visible on GitHub | [Contributing code back](contributing-code-back.md) |
| Product roadmap requests (Silver and higher) | A program benefit | [Taking part in the roadmap](roadmap-participation.md) |
| Partner meetings and guidance meetings | Discussion with 7x | [Partner meetings](partner-meetings-and-briefings.md) |
| Sponsorship | Funding priorities; some sponsor tiers include backlog influence | [Feature requests and sponsored features](feature-requests-and-sponsored-features.md) |
| Community channels | Public discussion | [Communication channels](communication-channels.md) |

## Advice

- **Never promise a customer a future feature** unless it is merged or in a written agreement.
- **Build on what is released.** The changelog of the line you use is the truth ([Testing releases](testing-releases.md)).
- **Ask in public when you can.** Decisions discussed in the issue trackers are visible to everyone and stay on record.

## Related pages

- [Who runs the partner program](who-runs-the-partner-program.md)
- [Working with the Exponential Foundation](working-with-the-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
