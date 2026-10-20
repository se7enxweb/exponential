---
title: Content ownership
audience: government, editor, admin, project-manager
category: public-sector
order: 100
summary: The difference between the technical owner of a content object in Exponential and the organizational owner a public body names for each page, how to record the second, and how to hand content over when people or departments change.
keywords: content ownership, content owner, page owner, accountability, Owner limitation, change owner, ezownerchange, handover, departments, governance, responsible officer
type: help
---
# Content ownership

This page is for web managers and editors-in-chief who must name who is responsible for each page of a public
website. Exponential has a technical owner per object; organizations usually need a business owner too. This page
explains both and how to keep them right. Sources: [Changing the owner of content](../../../../guides/changing-owners.md)
and the `Owner` limitation in `kernel/content/module.php`.

## Contents
1. [Two kinds of owner](#two-kinds-of-owner)
2. [The technical owner](#the-technical-owner)
3. [Recording the business owner](#recording-the-business-owner)
4. [Handover when people or departments change](#handover-when-people-or-departments-change)
5. [Related pages](#related-pages)

## Two kinds of owner

| | Technical owner | Business owner |
|---|---|---|
| What | the user account a content object belongs to | the function or department responsible for what the page says |
| Set by | the account that created the object; changed with **Change owner** | your governance |
| Used for | the `Owner` limitation in roles ("may edit own content") | review, accuracy, complaints, archive decisions |
| Stored | on every object | not built in; see below |

## The technical owner

- Every content object has an owner. Roles can limit what someone may do to their own content: the `Owner`
  limitation of `content/read`, `content/edit` and `content/remove`.
- The extension **ezownerchange** changes the owner of one object or a subtree, with a preview: context menu,
  **Change owner** (policy `owner/change`; subtrees need `owner/subtree`). New installations have it switched on.
  Every change is recorded as `content.ezownerchange.change`.
- On the command line, preview first:

  ```bash
  php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<new owner id> --node=<node id> --from-owner=<old owner id> --dry-run --list
  ```

## Recording the business owner

Exponential has no built-in "responsible department" field. Options, from simplest:

| Option | How | Notes |
|---|---|---|
| The tree | each department's pages live under its own folder | ownership follows the subtree; matches department editor roles ([Role design: department editors](../people-and-permissions/role-design-department-editors.md)) |
| A field on the class | add an attribute such as "Responsible unit" to your page classes | shown to editors; can be listed with fetches; a site builder's change |
| Tags | the eztags extension, if active, with a tag per unit | searchable; see [Tags](../../../../features/6.0/extensions/eztags.md) |
| An object state | a state group per owner is possible but scales badly | prefer the options above |

A function (for example "Head of Communications, Department of Example") is a better owner than a person's name:
it survives staff changes and keeps personal data off public pages.

## Handover when people or departments change

1. **Person leaves**: disable or deactivate the account; their content stays and remains editable by others with
   rights. If roles use `Owner = Self`, change the owner of their content to a colleague with **Change owner**,
   subtree and "only the objects of the current owner" ([Joiners, movers and leavers](../people-and-permissions/joiners-movers-leavers.md)).
2. **Department reorganized**: move the subtree under the new department's folder, then adjust the limited role
   assignments; moving changes the section only for objects that had the old parent's section
   ([Sections and subtree limits](../people-and-permissions/sections-and-subtree-limits.md#what-happens-when-content-moves)).
3. **Record it**: moves and owner changes are in the audit trail's `content` channel.

## Related pages
- [Review dates with object states](review-dates-with-object-states.md)
- [Style guide governance](style-guide-governance.md)
- [Changing owners guide](../../../../guides/changing-owners.md)
- [Version history and accountability](version-history-and-accountability.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
