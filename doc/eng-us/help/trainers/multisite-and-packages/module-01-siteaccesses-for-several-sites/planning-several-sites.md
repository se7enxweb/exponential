---
title: "Lesson 1.2: Planning several sites on one installation"
audience: trainer, admin, developer
category: training
order: 12
summary: Learners choose between the shapes of a multisite (separate content, shared content, languages, site extensions) and decide when separate installations are the better answer. 40 minutes, mostly discussion and a planning exercise on paper.
keywords: multisite and site packages, multisite, planning, one installation or several, content tree, root node, site extension, trade-offs, isolation, sevenx_multisite
type: help
---
# Lesson 1.2: Planning several sites on one installation

Before anybody copies a settings directory, the team has to decide what "several sites" means for them. One
installation can serve several brands with separate content, one brand with several designs, or one site in several
languages; and sometimes the honest answer is several installations. This lesson gives learners the questions and the
trade-offs, so that the technical lessons that follow serve a plan.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 1: Siteaccesses for several sites](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Administrators, developers and the people who plan sites with them |
| Prerequisites | [Lesson 1.1](what-a-siteaccess-is.md) |

## Learning objectives

After this lesson, learners can:

- describe four shapes of a multisite and give an example of each;
- explain what the siteaccesses of one installation always share (code, extensions, database, users);
- list the trade-offs of one installation against one installation per site;
- write a one-page plan for a multisite: sites, siteaccesses, root folders, matching, teams.

## Trainer's notes

### How to present it

Sources: [Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md) and
[Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md).

1. **The four shapes** (6 minutes). Put the table from the website creators' page on the projector:

   | Shape | How |
   |---|---|
   | Separate content per site | one folder per site under the root; each siteaccess uses its folder as root |
   | Shared content, different designs | the same root, a different `SiteDesign` per siteaccess |
   | One site in several languages | one siteaccess per language |
   | Each site in its own extension | settings that apply only while the site's siteaccesses run |

   Shapes combine: the default site package `sevenx_multisite` is itself a multisite installation, with more than one
   public site in one content tree.
2. **What is always shared** (3 minutes). One code base, one set of extensions, one database (for the siteaccesses
   in `RelatedSiteAccessList[]`), one set of users and roles, one search index. A site of its own therefore means a
   folder of its own, a siteaccess of its own and roles limited to that folder, not a database of its own.
3. **One installation or several** (6 minutes). Walk through the trade-offs:

   | | One installation, several siteaccesses | One installation per site |
   |---|---|---|
   | Shared content and users | yes | no |
   | One update for all sites | yes | one per site |
   | Isolation between sites at operating-system level | no | yes |
   | One site's load affects the others | yes | no, with separate pools |
   | Backups and restores | together | per site |

   The rule of thumb in the hosting guide: several siteaccesses on one installation for **one organization** with
   several sites; separate installations for **different customers**.

### Common questions

**Can two sites have different extensions?** Extensions are installed for the whole installation, but an extension
can be activated for some siteaccesses only, and a site extension's settings can apply only to its own siteaccesses
(module 3).

**Can a site have its own administrators who cannot see the other sites?** Yes, through roles limited by subtree or
section, or an administration siteaccess of its own. Module 4 covers one administration for several sites.

**Does a second site cost a second license?** No. Exponential is free software under the GNU GPL; there is no
per-site license.

**Will an upgrade move all sites at once?** Yes: one code base means every site moves with the others. Plan tests
for every site before an upgrade.

### Pitfalls

- **Planning by technology.** Learners jump to `HostMatchMapItems[]`. Insist on the plan first: who edits what,
  which addresses, which languages.
- **Customers sharing an installation.** If the sites belong to different customers, steer the discussion to
  separate installations; one restore restores every site.
- **Forgetting the media library.** Shared images live in one media library; decide whether each site gets its own
  folder there too.

## Hands-on exercise

A planning exercise on paper or in a shared document; no installation is needed. Work in pairs.

The brief: *a sports club wants a main site, a site for its youth section with a different look, and the main site
in English and German. The same office edits everything; the youth trainers may edit only the youth site.*

1. List the sites the visitors will see.
2. For each, decide the shape from the table above (several may combine).
3. Name the siteaccesses you need, including the administration and the Editor. Use lower-case names with
   underscores, as the installer requires (for example `club`, `club_ger`, `youth`).
4. Sketch the content tree: which folder is the root of each site, and where shared content (logo, media, users)
   lives.
5. Decide how each siteaccess is reached: a path (`/youth`), a host (`youth.example.com`) or a port. Note it; module
   2 teaches each method.
6. Decide who edits what, and write one sentence per team on the limits of its role.
7. Decide one installation or several, and write the reason in one sentence.
8. Present the plan to another pair in two minutes and collect one question.

## Expected result

- Each pair has a one-page plan with three public siteaccesses (main site, its German siteaccess, youth site), the
  administration and the Editor, a tree with two site roots and a shared media folder, an addressing choice per
  siteaccess and a role for the youth trainers limited to the youth subtree.
- The plan chooses one installation, because one office edits everything; a pair that chose several installations
  can defend it only with a real isolation need.
- The trainer checks by comparing two plans on the projector and asking each pair where the German siteaccess gets
  its design from (the same design as the main site; lesson 1.4).

## Quiz

1. Name the four shapes of a multisite.
2. What do all siteaccesses of one installation share?
   A. Only the code B. Code, extensions, database, users C. Only the database D. Nothing
3. Two customers want a site each on your server. One installation or two? Why?
4. True or false: each public site of a multisite needs its own database.
5. Which site package installs a multisite out of the box?

<details>
<summary>Answers</summary>

1. Separate content per site; shared content with different designs; one site in several languages; each site in
   its own extension ([Setting up several sites](../../../creators/website-creators/multisite-setup.md#shapes-of-a-multisite)).
2. **B.** One code base, the extensions, the database of the related siteaccesses and the users.
3. Two installations: different customers need isolation, and a restore of one installation restores every site
   ([Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#one-installation-or-several)).
4. **False.** The siteaccesses in `RelatedSiteAccessList[]` share one database; separate content is separate
   folders.
5. `sevenx_multisite`, the default site package of `exp:install` and the kickstarter.

</details>

## Further reading

- [Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md): shapes and
  trade-offs.
- [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md): the host's
  questions.
- [Structuring the content tree](../../../creators/website-creators/structuring-the-content-tree.md): root folders
  per site.
- [Roles and policies](../../../../guides/roles-and-policies.md): limiting a team to its subtree.

## Related pages

- [Module 1: Siteaccesses for several sites](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
