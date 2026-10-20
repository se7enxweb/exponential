---
title: Open source values in daily partner work
audience: partner
category: partnership
order: 250
summary: What the program's values Open, Sharing and Innovative mean in a partner's daily work - in projects, contracts, code, documentation, support and sales - with concrete habits and examples from the project itself.
keywords: open source, values, open, sharing, innovative, GPL, contribution, upstream, collaboration, habits, community
type: help
---
# Open source values in daily partner work

This page is for partner teams who want to turn the program's values into everyday habits. "Exponential partners
must align with open-source values and a commitment to quality" (<https://exponential.earth/partner/requirements>,
read 18 October 2026).

## The three values

| Value | As published (<https://exponential.earth/partner/program>) |
|---|---|
| **Open** | "We welcome ideas and knowledge from other people and bring an open mind and a positive attitude to our exchanges with the world. Our open-source software distribution model and user-driven community are examples of this open philosophy." |
| **Sharing** | "We share our information, ideas and knowledge and work together as a team, both internally and with customers, partners and the Exponential community." |
| **Innovative** | "We build creative solutions that solve real problems and enable an easier and more effective exchange of knowledge and information." |

## Habits that show the values

| Where | Open | Sharing | Innovative |
|---|---|---|---|
| Projects | Build on public extensions before writing new ones | Contribute general fixes upstream instead of patching privately | Solve the customer's problem, then generalize it for everyone |
| Contracts | Tell customers the software is GPL and they own their site | Agree up front which generic work may be published | Offer maintenance that keeps sites current |
| Code | Work in Git with full history ([Develop with Git](../develop-with-git.md)) | Send pull requests ([Contributing code back](contributing-code-back.md)) | Propose new features with a working prototype |
| Documentation | Read and link the help | Write tutorials under a free license | Document what you discovered, even if small |
| Support | Answer in public channels | Turn repeated answers into docs | Automate what you answer twice |
| Sales | State facts with sources | Refer work you cannot do well to another partner | Show what is new in each release |

## Examples from the project itself

- **Every pull request is merged.** Contributors are not turned away. While a pull request is open, its author
  squashes it and rebases it onto `main` as the reviews ask; once it is merged, `main`'s history is never rewritten,
  and later fixes come as new pull requests (`doc/eng-us/bc/6.0/ci.md`).
- **Credit for others' work.** The LoveStack improvements of Mugo Web were rebuilt "with full credit to Mugo Web"
  (README).
- **Upstream contributions.** The se7enxweb profile README reports "14 security PRs submitted upstream to
  zetacomponents" (cited in [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to)).
- **Free documentation.** The help is GFDL 1.3 or later, so anyone can copy and improve it
  ([Free documentation](../free-documentation.md)).

## What to agree with customers

Many customers worry that "open" means their business secrets will be published. It does not. Agree in writing:

- the customer's content, data and branding stay private;
- generic, reusable improvements (bug fixes, general features) may be contributed upstream;
- the customer is credited or not, as they wish.

## Related pages

- [Partner code of conduct](partner-code-of-conduct.md)
- [Licensing for partners: GPL code, GFDL documentation](licensing-for-partners.md)
- [Contributing extensions back upstream](contributing-extensions-back.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
