---
title: "Lesson 2.5: Changing the owner of content"
audience: trainer, admin
category: training
order: 25
summary: Learners change the owner of one item and of a subtree from the administration with a preview first, and preview the same change on the command line, as part of handing over a departing user's content. 30 minutes.
keywords: site administration, owner, change owner, ezownerchange, owner limitation, handover, departing user, subtree, dry-run, audit
type: help
---
# Lesson 2.5: Changing the owner of content

Every content item has an owner, the user it belongs to. Roles can limit what someone may do to their own content,
so when a colleague leaves or content was created under the wrong account, the owner has to change. This lesson uses
the extension ezownerchange, switched on in new installations, to hand content over safely.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 2: Users and groups](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 2.4](unactivated-users-and-account-checks.md); the users `tess.trainee` and `sam.sample` |

## Learning objectives

After this lesson, learners can:

- explain why ownership matters for the `Owner` limitation of a role;
- change the owner of one item, reading the preview first;
- change the owner of a subtree, limited to the items of one owner;
- preview a change on the command line with `--dry-run`;
- name a sensible order of steps when a user leaves.

## Trainer's notes

### How to present it

Source: [Changing the owner of content](../../../../guides/changing-owners.md).

1. **Why owners matter** (3 minutes). The policies `content/read`, `content/edit` and `content/remove` can carry the
   `Owner` limitation ("only their own content"). If the owner is a departed colleague, nobody with such a role can
   edit the content any more.
2. **The handover** (4 minutes). In the context menu of a node, **Change owner** (needs `owner/change`, the
   Administrator role has it). Choose the new owner in the content browser and press **Select**: nothing has changed
   yet. The preview lists every object the change reaches. Pick **This object only** or
   **This object and everything below it** (needs `owner/subtree`), optionally only the current owner's objects,
   **Update preview**, then **Change the owner of N objects**.
3. **Command line** (2 minutes). For subtrees over 500 objects or scripted handovers, the script
   `extension/ezownerchange/bin/php/expownerchange.php` with `--dry-run --list` first.
4. **Leaver checklist** (1 minute): hand over the content, then disable or deactivate the account (lesson 2.4), then
   check the audit trail.

### Common questions

**Does changing the owner change who created the item or its versions?** No. The owner changes; the version
history keeps who made each version.

**What if I may not edit some items in the subtree?** Each object changes only where you may also edit it
(`content/edit`); the preview shows the outcome for each.

**Is the change recorded?** Yes, every change in the audit trail as `content.ezownerchange.change`.

### Pitfalls

- **Skipping the preview.** On a large subtree the preview is the only place to see what else will change. Read it
  aloud in class.
- **Missing menu entry.** If **Change owner** is not in the context menu, the extension is not active or the role
  lacks `owner/change`. Check **Setup > Extensions** on the lab.
- **Object ids versus node ids.** The command line takes owner ids (user object ids) and a node id. Show where to read
  each on an item's **Details** tab.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Sign in as `tess.trainee` in a private window (the account is in **Editors** since lesson 2.2) and create a folder
   `Tess folder` with one article inside it, where your site lets editors create content. If it does not, create the
   folder and article as the administrator and skip to step 3.
2. Sign out in the private window.
3. As the administrator, open `Tess folder`. On the **Details** tab, note the owner, the node id and the object id.
4. Open the context menu of `Tess folder` and choose **Change owner**.
5. Choose `sam.sample` in the content browser and press **Select**.
6. In the preview, choose **This object only** and press **Update preview**. Read the list.
7. Choose **This object and everything below it**, limit it to the objects of the current owner, and press
   **Update preview** again. Compare the count.
8. Press **Change the owner of N objects**. Read the result.
9. Check the owner on the **Details** tab of the folder and of the article.
10. In a terminal, preview the change back, replacing the ids with those from step 3 and the user object ids of both
    users:

    ```bash
    php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<tess object id> --node=<folder node id> --from-owner=<sam object id> --dry-run --list
    ```

## Expected result

- Step 7 counts the folder and the article (two objects) when both belonged to `tess.trainee`.
- After step 8 both items name `sam.sample` as owner on their **Details** tab.
- Step 10 lists the objects that would change back and changes nothing.
- The trainer checks the **Details** tab of one learner's folder.

## Quiz

1. Which policy limitation makes ownership matter for editing?
2. Which policy do you need to change the owner of a whole subtree?
   A. `owner/change` B. `owner/subtree` C. `content/move` D. `user/hygiene`
3. When does the owner actually change: after **Select**, or later?
4. What do `--dry-run --list` do on the command line?
5. Put in order: disable the account, check the audit trail, hand over the content.

<details>
<summary>Answers</summary>

1. The `Owner` limitation of `content/read`, `content/edit` or `content/remove`
   ([Changing the owner of content](../../../../guides/changing-owners.md)).
2. **B.** On top of `owner/change`.
3. Only after **Change the owner of N objects**; **Select** leads to the preview.
4. They show which objects would change and change nothing.
5. Hand over the content, disable the account, check the audit trail.

</details>

## Further reading

- [Changing the owner of content](../../../../guides/changing-owners.md): the administration and the command line.
- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): limitations such as `Owner`.
- [Security and audit](../../../../guides/security-and-audit.md): reading the audit trail.

## Related pages

- [Module 2: Users and groups](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
