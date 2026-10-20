---
title: "Lesson 10.4: The Extensions page"
audience: trainer, admin
category: training
order: 104
summary: Learners read the extension cards of Setup > Extensions, explain written order and loading order, plan an activation and a risky deactivation, read the review with its risks, and discard the plan without writing anything. 35 minutes.
keywords: site administration, extensions, Setup Extensions, ActiveExtensions, loading order, ExtensionOrdering, Activate, Deactivate, Review changes, Apply changes, Discard changes, ezformtoken, ezjscore, Velocity restart
type: help
---
# Lesson 10.4: The Extensions page

Most of what an installation can do comes from extensions, and their order decides which settings and templates win.
**Setup > Extensions** shows every extension the installation can see, which are active and in which order, and
changes that list with a review that names the risks before anything is written. This lesson teaches the page with a
plan that is reviewed and then discarded.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 10: Upgrades, extensions and packages](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 10.1](planning-and-running-an-upgrade.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- read an extension card: position, version, license, active state, git checkout, dependencies and problems;
- explain why an extension higher in the list wins, and why the loading order can differ from the written order;
- plan an activation, a deactivation and a move, and read **Review changes**;
- name two deactivations the review marks as risky and why;
- say what to do after applying when the site is also served by Velocity.

## Trainer's notes

### How to present it

Source: [The Extensions page: activate, deactivate and order extensions](../../../../guides/extensions-page.md).

1. **One list** (3 minutes). Active extensions first, in the order of `[ExtensionSettings] ActiveExtensions[]` in
   `settings/override/site.ini.append.php`, numbered from 1; inactive ones after, by name. Higher in the list means
   higher priority: when two extensions ship the same setting or template, the one loaded earlier wins.
2. **The card** (3 minutes). Number, name and version, license and website, active state, access extension per
   siteaccess, **git** (update it with git, not with a package), Requires, Uses, Extends, Needed by, Loads as number
   *n*, Problems and Details.
3. **Loading order** (2 minutes). With `ExtensionOrdering=enabled` (the default) the system sorts extensions by their
   declared dependencies; the written order counts only where nothing is declared.
4. **Plan, review, apply** (5 minutes). Nothing is written while you work: the bar says what will change. **Review
   changes** lists activations, deactivations and moves, the risks (an extension another requires; the only design a
   siteaccess uses; `ezformtoken`, the protection of every form against cross-site request forgery; `ezjscore`, the
   administration's scripts) and the resulting lines. Apply keeps a copy of the override file, rewrites only the
   `ActiveExtensions[]` lines, clears the caches and regenerates the autoloads.
5. **Velocity** (2 minutes). Its workers keep the extension list they started with: restart Velocity after applying.

### Common questions

**What does "Listed in ActiveExtensions, but its directory is missing" mean?** The setting names an extension whose
folder is gone. Install it again, or use **Remove from the list**.

**Two administrators change the list at the same time.** The page notices that the file changed and starts again from
the file instead of writing over the other change.

**Why can I not download an extension?** Exporting extensions is off by default; it needs the extension in
`package.ini [ExtensionExportSettings] AllowedExtensions[]` and the policy `package/export_extension`.

### Pitfalls

- **Deactivating `ezformtoken`.** Every form loses its protection against cross-site request forgery. The review marks
  it red; never confirm it on a real site.
- **Moving where dependencies decide.** The file changes but the loading order does not; the review says so.
- **Expecting a change on Velocity at once.** Restart it.

## Hands-on exercise

Use your own training installation, signed in as an administrator. This exercise writes nothing.

1. Open **Setup > Extensions**. Read **Overview**: active, inactive, only for siteaccesses, git checkouts, problems.
2. In **Find extensions**, type `ezjscore` and press **Show**. Read its card: number, version, license, **Needed by**.
3. Clear the search. Find one card where **Loads as number** differs from its written number, and explain why.
4. Find an inactive extension and press **Activate**. Read the bar that says what is not applied yet.
5. Find `ezformtoken` and press **Deactivate**.
6. Press **Review changes**. Read the activations and deactivations, the risks marked red, and the resulting
   `ActiveExtensions[]` lines.
7. Do not confirm. Press **Discard changes** and check that the bar is gone.
8. In a terminal, compare with the file: `grep -n "ActiveExtensions" settings/override/site.ini.append.php`.

## Expected result

- Step 3 finds an extension whose loading position is decided by its declared dependencies.
- Step 6 shows the deactivation of `ezformtoken` as a risk marked red that must be confirmed before **Apply changes**
  is accepted.
- After step 7 nothing was written: the list in step 8 is the same as before the exercise.
- The trainer checks the file of two learners with the command of step 8.

![The Extensions page: counters, search, filters and a card per extension](../../../images/trainers/sa-extensions.webp)

## Quiz

1. Two active extensions ship the same template. Which one wins?
2. Why does the loading order differ from the written order on most installations?
3. Name two deactivations the review marks as risky.
4. What does **Apply changes** write?
   A. The whole override file B. Only the `ActiveExtensions[]` lines C. `settings/site.ini` D. The extensions' own
   settings
5. You applied a change, and the Velocity port does not show it. What do you do?

<details>
<summary>Answers</summary>

1. The one higher in the list, which is loaded earlier.
2. With `ExtensionOrdering=enabled` the system sorts extensions by their declared dependencies
   ([The Extensions page, section 4](../../../../guides/extensions-page.md#4-written-order-and-loading-order)).
3. Any two of: an extension another active extension requires; the only extension providing a design a siteaccess
   uses; `ezformtoken`; `ezjscore`.
4. **B.** It keeps a copy of the file first and leaves every other line as it was.
5. Restart Velocity (`exp:velocity restart`): its workers keep the extension list they started with.

</details>

## Further reading

- [The Extensions page](../../../../guides/extensions-page.md): cards, review, apply and common problems.
- [Extension loading order](../../../../features/6.0/extension-loading-order.md).
- [Extension list and downloads](../../../../features/6.0/extension-list-and-downloads.md).
- [Extensions](../../../../guides/extensions.md): the developer guide.

## Related pages

- [Module 10: Upgrades, extensions and packages](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
