---
title: "Case study: porting a package from another project with credit"
audience: developer, app-creator
category: extension-development
order: 160
summary: How two extensions brought in outside code and credited it - sevenx_dse embedding a database tool from a chain of forks, expchangeclass merging two forks - and what to check in your own port.
keywords: case study, port, credit, attribution, sevenx_dse, AdminNeo, Adminer, expchangeclass, fork, license, README credits, Includes
type: help
---
# Case study: porting a package from another project with credit

Part 5, chapter 16. Two extensions show two common ways of bringing outside code into Exponential, and how each
credits its sources. Use them as a model when you port something yourself.

## 1. Embedding a tool: sevenx_dse

se7enxweb/sevenx_dse puts a full database administration interface into the Exponential administration, behind its
roles and policies. It does not write that interface itself: it embeds an existing open source tool. Its README
describes the chain in its first lines: the extension "is a downstream fork of AdminNeo embedded as a native ...
extension", and AdminNeo is "a downstream fork of the original Adminer database tool".

How it credits that chain:

| Where | What it says |
|---|---|
| README, first paragraph | what is embedded, with a link to the project |
| README, "Credits" | a table: the embedded core and its project, the original author of both tools, and the extension's own integration work, each with a link; then a sentence of thanks to the original author |
| README, "Copyright" | its own copyright, and "AdminNeo and Adminer are copyright their respective authors", with a link to their full credits |
| The embedded code | kept in its own folder, `adminneo/`, separate from the extension's classes |

What it changed, it says too: "CSS isolation to prevent AdminNeo global styles from damaging eZ admin chrome", the
role-based gate before anything connects, the session handling. A reader can tell which work is the extension's and
which is the tool's.

Changes to the embedded code are commits of their own, in the extension's history, with the reason. Commit b02ff09,
for example, replaced `strlen( utf8_decode() )` with `mb_strlen()` in two files of `adminneo/` for PHP 8.2
([PHP 8.1 to 8.5 deprecations](../part-4-upgrading/php-8-1-to-8-5-deprecations.md)). A fix like that is also a
candidate to send back to the tool's own project.

## 2. Merging forks: expchangeclass

se7enxweb/expchangeclass took a different path: the original extension was abandoned, and two forks existed. Its
README says which parts came from where: it "takes the OpenContent code as its base, takes the attribute
pre-selection and packaging from the Netgen fork, and fixes what neither had", and links the original repository.

Credit appears in every place this book recommends
([Attribution and copyright notices](attribution-and-copyright-notices.md)):

- `ezinfo.php`: an `Includes` entry with the original's name, copyright and license, shown on the about page;
- `composer.json`: the original author in `authors`, with the role "Original author of eZChangeclass";
- `composer.json` description: "A hardened fork of eZChangeclass";
- `doc/changelog.txt`: the original's changelog kept, with the fork's 1.0.0 entry on top, starting "Fork of
  eZChangeclass for Exponential Legacy / Exponential 6 on PHP 8, taking the OpenContent fork as its base and the
  mapping pre-selection from the Netgen fork";
- the license: GPL, unchanged.

## What to check in your own port

| Question | Why |
|---|---|
| What is the license of each piece, and do they combine? | [Licenses and how they combine](licenses-and-compatibility.md) |
| Does each piece's license text travel with it? | MIT, BSD and Apache require the notice; GPL requires the license |
| Is every author named, in the README and in metadata? | fairness, and the about page |
| Is it clear which work is yours? | users and the original authors need to know whom to ask |
| Is the outside code kept apart (its own folder)? | updates of the original can be applied; licenses stay clear |
| Are your changes to the outside code separate commits with reasons? | you can send them back, and re-apply them after an update |
| Did you offer your fixes back? | [Contributing back with pull requests](contributing-back-with-pull-requests.md) |

## A note on "inspired by"

Some extensions are not ports but reimplementations: they take the ideas of another project and write new code for
Exponential. Ideas are not protected the way code is ([Reading licenses before you reuse anything](../part-1-from-user-to-developer/reading-licenses-before-reusing.md)),
but saying where they came from is still right. The Exponential Layouts extensions do this in their READMEs ("an
Exponential Legacy port inspired by" the corresponding packages of another project). As soon as actual code, templates
or JavaScript from the other project is included, it is a port in the sense of this chapter, and its license and
notices apply.

## Related pages

- Previous: [Growing the community through code](growing-the-community-through-code.md)
- Next: [Case study: a pull request to an upstream author](case-study-a-pull-request-to-an-upstream-author.md)
- [Case study: from eZChangeclass to expchangeclass](../part-4-upgrading/case-study-expchangeclass.md)
- [Rebranding ethically](rebranding-ethically.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
