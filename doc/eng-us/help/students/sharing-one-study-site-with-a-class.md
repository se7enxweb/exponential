---
title: Sharing one study site with a class
audience: student
category: learning
order: 16
summary: For study groups and classes that share one Exponential installation - why one installation per learner is safer, how to share well when you must (accounts per learner, limited roles, demo mode when everyone is an administrator), rules for the group, and how to reset the shared site.
keywords: study group, class, shared site, shared installation, accounts, roles, demo mode, EXP_DEMO_MODE, lab, classroom, reset, learners
type: help
---
# Sharing one study site with a class

This page is for study groups, student clubs and classes that work on one shared installation, for example a server
of the school. It explains when sharing works, how to set it up so nobody locks the others out, and how to reset it.
Trainers have a fuller treatment in [Train the trainer, module 2](../trainers/train-the-trainer/module-02-building-the-lab/README.md).

## Contents

1. [One per learner, or one for all](#one-per-learner-or-one-for-all)
2. [Accounts: one per learner](#accounts-one-per-learner)
3. [When everyone is an administrator: demo mode](#when-everyone-is-an-administrator-demo-mode)
4. [Rules for the group](#rules-for-the-group)
5. [Resetting the shared site](#resetting-the-shared-site)

## One per learner, or one for all

The trainers' lesson [One installation per learner](../trainers/train-the-trainer/module-02-building-the-lab/one-install-per-learner.md)
says it plainly: "The safest lab gives every learner their own installation: nobody can break a neighbour's exercise,
and every learner can be an administrator." Since `exp:install` installs a complete site on SQLite in one command,
this is cheap.

| | One installation per learner | One shared installation |
|---|---|---|
| Who can break what | Only your own site | Everyone's work |
| Administrator for everyone | Yes | Only with care (below) |
| Command line exercises | Yes | Usually not |
| Good for | All learning paths | Editor exercises, group projects, a shared capstone |

Share only when you must, and give each learner their own site for the administrator and developer paths. The
trainers compare both in [Shared server or one per learner](../trainers/train-the-trainer/module-02-building-the-lab/shared-server-or-one-per-learner.md).

## Accounts: one per learner

On a shared site, every learner gets an account of their own, never the shared `admin` account:

1. The person who runs the site creates a user group, for example "Class 2026".
2. Each learner gets a user in that group, with an address at `example.invalid` if no real mail is needed.
3. The group gets a role that fits the exercises, for example editing in one subtree only
   ([How do I give a group edit rights on one section?](../manual/how-do-i-give-a-group-edit-rights-on-one-section.md),
   [Roles, policies and unactivated users](../../guides/roles-and-policies.md)).
4. Each learner works in a folder of their own, so the work stays apart.

The trainers' lesson [Learner accounts and roles](../trainers/train-the-trainer/module-02-building-the-lab/learner-accounts-and-roles.md)
shows the same set-up step by step. Setting it up is itself a good exercise: see
[project 9](project-09-roles-for-a-restricted-editor.md).

## When everyone is an administrator: demo mode

If every participant must sign in as an administrator, any one of them could change the administrator password and
lock out the rest. **Demo mode** prevents that: with the environment variable `EXP_DEMO_MODE=1` set for every PHP
process of the installation, administrator passwords cannot be changed or reset
([Demo mode FAQ](../demo-mode-faq.md)). The trainers use it for this case
([Trainers: start here](../trainers/trainers-start-here.md#finding-these-pages-again)).

What you must know before you use it:

- It is switched on by the server, not from the administration; only the exact value `1` counts
  ([Demo mode](../demo-mode.md#switching-it-on-or-off)).
- Everything else still works: an administrator can still change content, roles and settings, so the site must be
  rebuilt regularly ([Demo mode FAQ](../demo-mode-faq.md#what-does-it-not-block)).
- It means the administrator password is meant to be known. Never use it on a site with real content, real users
  or real data ([Demo mode FAQ](../demo-mode-faq.md#can-i-use-it-on-a-real-site)).
- Nobody may enter anything private on such a site. The banner says so: "Do not enter anything private here."

## Rules for the group

Agree on these before the first session, and write them at the top of the group's notes:

1. Everyone uses their own account. Nobody shares a password in a chat.
2. Only invented content and test addresses at `example.invalid`.
3. Nobody removes or changes another person's work without asking.
4. Settings are changed by one named person, and every change is written in the group's notes.
5. Before a risky exercise, the person who runs the site makes a backup
   ([Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md#save-before-a-risky-exercise)).
6. Problems with the software are reported once, by one person, with the steps
   ([Bugs you find while learning](bugs-you-find-while-learning.md)).

## Resetting the shared site

- Between courses or terms: reinstall with `exp:install --force`
  ([Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md#start-over-with-a-fresh-install)).
- Before an exam or a presentation: restore a saved state.
- The trainers' lesson [Resetting the lab](../trainers/train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)
  has a routine for groups.

## Related pages

- [Demo mode FAQ](../demo-mode-faq.md)
- [Asking for help and study groups](asking-for-help-and-study-groups.md)
- [For teachers: assigning these pages](for-teachers-assigning-these-pages.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
