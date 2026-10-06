# Roles, policies and unactivated users

This guide is for administrators. In about 25 minutes you read what every role gives and to whom, build a role with
limited policies, put its policies in order, assign it to a group, and clean up registrations nobody finished.

All pages are under **Users** in the administration: **Roles and policies** (`role/list`) and **Unactivated users**
(`user/unactivated`). The editor siteaccess refuses the role module; use the administration siteaccess.

## In short

- A **role** is a set of **policies**. A policy lets its users use one module, or one function of a module, possibly
  only in some sections, classes, subtrees, languages or siteaccesses.
- A role does nothing until it is **assigned** to users or user groups. A user has every policy of every role
  assigned to them or to one of their groups.
- Every change in the role editor is made in a **draft**. Nothing changes for anyone until you press **Save**;
  **Cancel** throws the draft away.
- A role with a policy over **every module** gives **full access**: the list and the role page mark it in red. A
  policy over the **role** module lets its users change roles, and so give themselves any access: marked in amber.

## 1. Read the role list

Open **Users > Roles and policies**. Each role is a card with its ID, how many policies it has and how many users and
groups it is assigned to (the number links to the role page), and its actions: View, Edit, Assign, Copy.

- **Find a role**: any part of the name, or the ID. Upper and lower case are the same.
- **Order**: by name, ID, number of policies or number of assignments. Click the current order again to reverse it.
  The order and the search are kept in the address (`/role/list/(sort)/policies/(dir)/desc?q=edit`), so you can
  bookmark them. The orders by count are offered up to 500 roles.
- **Badges**: *Full access* (red), *Can change roles* (amber), *Not assigned* (the role gives nobody anything).
- **Per page**: the sizes come from `site.ini [RoleSettings] RolesPerPageList`.

Expected: Administrator shows *Full access*; Anonymous shows about 15 policies and 3 assignments on a fresh install.

## 2. Read one role

Click a role. The page shows:

- **The figures**: policies; assignments; user groups; users assigned directly; and **users affected in all**: every
  user the role reaches, counting the members of its groups once each (a user in two of its groups counts once).
  Users of an assignment with a subtree or section limitation count too; the line below the figures says how many
  assignments are limited.
- **The policies as sentences**, for example:
  - *May read content, in section Standard, of the classes Article and Folder*
  - *May log in, on the siteaccesses site and bold*
  - *May do everything, in every module* (full access)

  Under each sentence: the policy ID, its module and function, and its limitations as stored, with links to the
  nodes and subtrees. A limitation that no handler evaluates is marked *(no handler, denies)*: that policy gives no
  access. Order the list by role order, module (grouped under each module), function or limitation.
- **The users and groups** the role is assigned to, with their limitation (*Only in section Standard*, *Only in the
  subtree Media*), a name filter when there are many, and paging. An assignment whose user or group was removed is
  listed first and can be removed.

## 3. Create a role with limited policies

1. On the role list, click **New role**. The editor opens on a new draft.
2. Type the name, for example `News editors`.
3. Click **New policy**. The wizard shows its three steps: Module, Function, Limitations.
   - **Step 1, Module**: choose `content`. With JavaScript, the functions of the module are offered at once.
     *Grant access to all functions* adds a policy over the whole module now. Choosing *Every module* with all
     functions is full access; the page warns before you add it.
   - **Step 2, Function**: choose `read`. *Grant full access* adds it without limitations; *Grant limited access*
     goes to step 3.
   - **Step 3, Limitations**: choose *Section: Standard* and *Class: Article* and *Folder* (Ctrl or Cmd for more than
     one). *Any* leaves a limitation out. Several limitations must all be met; several values of one are
     alternatives. Nodes and subtrees are picked in the content browser. Press **OK**.
4. The editor lists the policy as *May read content, in section Standard, of the classes Article and Folder* and
   says **Unsaved changes**.
5. Press **Save**. The role page opens.

To change the limitations of a policy later, click **Limitations** next to it in the editor.

## 4. Put the policies in order

The order of the policies does not change what anyone may do: each policy grants access on its own. It helps people
read a role. The order is the policies' IDs in the draft (see
[Roles: policy IDs, sorting and order buttons](../features/6.0/role-policy-order.md)); all ways of moving use it.

In the editor, keep **Role order** (ID ascending), then:

- **Drag** a policy by its grip (the dotted handle) to its new place. The page sends the move, the policy lands
  there and the page says *The policy was moved to position N*.
- **Keyboard**: Tab to the grip, press the **up** or **down arrow** key. The grip keeps the focus at its new place.
- **Without JavaScript**: the **up** and **down** buttons move one place, also across pages; the **position field**
  and **Move** put a policy at any place in the whole list.
- Press **Save** to keep the order, or **Cancel** to drop it.

Dragging is offered only in role order and within the page shown: in another order the list is not the role's
order, and a drag can only reach what is on the screen. To move further, use the position field.

## 5. Assign the role

On the role page:

- **Assign** opens the content browser on the user tree: choose users or groups. Prefer groups: every member, now
  and later, gets the role.
- **Assign with limitation**: choose *Subtree* or *Section*, then the subtree or the section, then the users or
  groups. The role then applies only there.
- **Remove selected**: tick assignments, open the confirmation and press *Remove the ticked assignments*. The users
  and groups lose the role at once; they are not removed.

## 6. Copy and remove roles

- **Copy** opens a page that says what the copy holds; *Make the copy* creates "Copy of <name>" with the same
  policies and no assignments, and opens it in the editor. Opening the copy address alone makes nothing.
- **Remove**: on the role list tick roles, open *Remove selected* and confirm. The roles, their policies and
  assignments are removed for good.

## 7. Clean up unactivated users

**Users > Unactivated users** lists people who registered but never clicked the link of their activation mail.

- The heading counts them. **Find a registration** searches login, e-mail and name. Order by registration date,
  login, e-mail or name. A registration older than 30 days is marked **Old**.
- Tick users, then open one of the confirmations:
  - **Activate selected users**: the accounts work at once, as if their owners had clicked the link.
  - **Send the activation mail again**: each gets a new link; the earlier link stops working. The page names the
    mail transport (`site.ini [MailSettings] Transport`). A user without a valid address is reported, not mailed.
  - **Remove selected users**: removed for good, with their user objects.
- **Remove all unactivated users** removes every unactivated user, on every page. With a search active the button
  says *Remove all N matching the search* and removes only those. The confirmation states the number. Users are
  removed in batches of 50. Each one is checked again just before removal. Never removed: anyone activated
  meanwhile, the anonymous user, the account `admin`, and you. The page reports *N removed, M skipped* with the
  reasons. The removal is recorded in the audit trail as `access.user.remove`, with the counts.

Every action, here and on the role pages, needs the form token of the page. A request without it is refused with 403.

## Who may do this

- The role pages need access to the `role` module. The kernel does **not** stop someone with that access from giving
  themselves more: whoever may edit roles may give any role any policy. Give the role module only to administrators.
  The list marks such roles *Can change roles*.
- The unactivated users page needs `user/activation`.

## Problems

| You see | Why | Do |
|---|---|---|
| *Unsaved changes* on the editor | The draft differs from the saved role | Save or Cancel |
| The browser asks before you leave the editor | The draft or the name changed | Save, or Cancel to discard |
| No grip next to the policies | The list is not in role order | Click **Role order** |
| *(no handler, denies)* after a limitation | No extension evaluates that limitation | Activate the extension that adds it, or remove the policy |
| A user still listed after *Activate* | They were activated meanwhile, or are not unactivated | Read the message above the list |

## For developers

- `expRolePolicySentence` (kernel/classes/role) words a policy; `expRolePage` gives the role list rows, summaries,
  the users a role reaches, the draft comparison and the policy moves (`moveSteps()`, `movePolicyTo()`, built on
  `eZRole::movePolicy()`); `expUnactivatedUsers` (kernel/classes/user) gives the list, the age, the resend and
  Remove all (`removeAllWith()` takes the database work as functions).
- Tests without a database: `php vendor/bin/phpunit tests/tests/kernel/classes/expRolePagesTest.php`.
- Every template is in `design/admin` and `design/admin4`; the look is `role/exp_style.tpl`, scoped to `.exp-roles`.
  No shared stylesheet is changed.

## Related pages

- [Roles: policy IDs, sorting and order buttons](../features/6.0/role-policy-order.md)
- [Role assignment paging](../features/6.0/role-assignment-paging.md)
- [Sections](sections.md), [Object states](object-states.md), [Security and audit](security-and-audit.md)
- [Changelog 6.0.15](../changelogs/6.0/6.0.15.md)
