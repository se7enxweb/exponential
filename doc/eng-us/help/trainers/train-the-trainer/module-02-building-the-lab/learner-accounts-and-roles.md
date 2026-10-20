---
title: "Lesson 2.5: Learner accounts and roles"
audience: trainer, admin
category: training
order: 25
summary: Create learner accounts with the right role for the course, editors in the Editors group, administrators in Administrator users, test e-mail addresses at example.invalid, and check what an account may do before class. 45 minutes.
keywords: user accounts, learners, roles, Editor role, Administrator users, Editors group, example.invalid, test users, create user, role assignment, training lab
type: help
---
# Lesson 2.5: Learner accounts and roles

An editor course taught with administrator accounts teaches the wrong thing: learners see menus their real accounts
will never show. A shared training site needs one account per learner, with the role that matches the course. This
lesson shows how to create such accounts in the administration and how to check what they may do.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Trainers who prepare a shared training site |
| Prerequisites | [Lesson 2.4](serving-the-lab-sites.md); an administrator account on the training site |

## Learning objectives

After this lesson, learners can:

- create a user in a user group from the administration;
- choose the group, and so the roles, that fit the course;
- use addresses at `example.invalid` for every test account, so no mail can reach a real person;
- read a role's page to see which groups it is assigned to and what it allows.

## Trainer's notes

### How to present it

1. Open **Users** in the top menu (3 minutes). The left column lists the user groups, for example **Administrator
   users**, **Editors**, **Members** and **Anonymous Users**, and under **Access control** the entries **Roles and
   policies**, **Unactivated users** and **Check user accounts**.
2. Explain the model (5 minutes): a role is a set of policies; a role does nothing until it is assigned; assign roles
   to groups, not to single users, so every member, now and later, gets the role.
3. Open **Roles and policies**, then the **Editor** role, and show the groups it is assigned to (3 minutes).
4. Explain `example.invalid` (2 minutes): the domain `.invalid` is reserved and can never receive mail, so a test
   account can never send mail to a stranger.

### Common questions

**Why not use the administrator account for everyone?** One shared account means one shared password, one set of
preferences and no way to see who did what in the audit trail. Never share the administrator account.

**Can learners have a role only in one part of the tree?** Yes: on a role page, **Assign with limitation** applies
the role only in a subtree or a section. Lesson 2.7 uses it for shared training servers.

**Which fields does a user have?** The User content type has **First name**, **Last name** and **User account**
(with **Username**, **Email**, **Password** and **Confirm password**), plus optional fields such as **Signature**
and **Image** on a site installed from the 7x site packages.

### Pitfalls

- **Weak passwords.** The site enforces a minimum length (`site.ini [UserSettings] MinPasswordLength`, 10 in the
  shipped settings). Prepare passwords that meet it.
- **Real addresses.** Never put a learner's private e-mail address into a training site. Use `example.invalid`.
- **Leftover accounts.** Remove training accounts after the course (lesson 2.6).

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Users** in the top menu, then the group **Editors** in the left column.
2. Under **Sub items**, press **Create new** and choose **User**.
3. Fill in **First name** `Learner`, **Last name** `One`, **Username** `learner01`, **Email**
   `learner01@example.invalid`, and a **Password** of at least 10 characters (twice).
4. Press **Send for publishing**. The new user appears in the group's sub items.
5. Open **Users > Roles and policies** and open the role **Editor**. Find the groups it is assigned to and check that
   **Editors** is one of them. (If it is not, your site package assigns roles differently: note which role the
   Editors group has.)
6. Sign out, and sign in to the administration as `learner01`. Note which top menu entries are missing compared with
   the administrator.
7. Sign out and sign in again as the administrator. Repeat steps 2 to 4 for `learner02` if you have time.

## Expected result

- `learner01` exists in the Editors group with an address at `example.invalid`.
- Signed in as `learner01`, the administration shows fewer menus than for the administrator (the Setup area, for
  example, is not available to the Editor role).
- The trainer checks the user list: no real e-mail address, no shared accounts.

## Quiz

1. Why assign roles to groups instead of single users?
2. Why use `example.invalid` for test accounts?
3. Which fields does the User account part of a user hold?
4. Where do you see which groups a role is assigned to?
5. True or false: one shared administrator account is fine for a classroom.

<details>
<summary>Answers</summary>

1. Every member of the group, now and later, gets the role ([Roles and policies](../../../../guides/roles-and-policies.md#5-assign-the-role)).
2. The domain `.invalid` cannot receive mail, so no message can reach a real person.
3. **Username**, **Email**, **Password** and **Confirm password** (and **Confirm email** when the site requires it).
4. On the role's page under **Users > Roles and policies**.
5. **False.** Shared credentials hide who did what and share one set of preferences.

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): the complete guide.
- [Accounts and signing in](../../../accounts-and-sign-in.md): what learners see when they sign in.
- [Logging into the editor](../../../logging-into-the-editor.md): the editor siteaccess.

## Related pages

- [Module 2: Building the training lab](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
