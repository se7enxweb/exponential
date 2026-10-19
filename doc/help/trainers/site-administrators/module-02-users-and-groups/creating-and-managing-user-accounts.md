---
title: "Lesson 2.2: Creating and managing user accounts"
audience: trainer, admin
category: training
order: 22
summary: Learners create user accounts at example.invalid, place a user in a second group, move a user, and disable and enable an account. 40 minutes.
keywords: site administration, create user, user account, username, email, example.invalid, locations, add location, move, enable user account, configure user account settings
type: help
---
# Lesson 2.2: Creating and managing user accounts

Creating accounts is the administrator task done most often. This lesson covers the whole life of an account short of
its removal: creating it in the right group, adding a second group, moving it, and switching it off and on.

> **Note**
> Test accounts always use addresses at `example.invalid`. That domain is reserved and can never receive mail, so a
> training site can never write to a real person.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 2: Users and groups](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 2.1](users-and-user-groups.md): the group `Training team` exists |

## Learning objectives

After this lesson, learners can:

- create a user in a chosen group with a password that meets the site's rules;
- add a second location so a user belongs to two groups;
- move a user from one group to another;
- disable and enable an account with **Configure user account settings**;
- explain why the username cannot be changed later.

## Trainer's notes

### How to present it

Source: [Change the administrator password](../../../change-the-admin-password.md) and
[Roles, policies and unactivated users](../../../../guides/roles-and-policies.md).

1. **Create** (4 minutes). In a group, **Create new** > **User**. The **User account** field holds **Username**,
   **Email**, **Password** and **Confirm password**. The password must reach
   `site.ini [UserSettings] MinPasswordLength` (10 on a new installation); further rules can be switched on in
   `[PasswordSettings]`.
2. **The username is permanent** (2 minutes). Once an account has a username, it cannot be changed. Choose a scheme
   (first name and last name, or a staff number) before creating many accounts.
3. **Two groups** (3 minutes). On a user's **Locations** tab, add a location in a second group. The user now has the
   roles of both groups.
4. **Moving** (2 minutes). **Move** from the item's actions changes the main group. The rights change at once.
5. **Switching off** (4 minutes). Below the **User account** field on the user's page, **Configure user account
   settings** (`user/setting/<user id>`) has **Enable user account**, **Maximum number of failed login attempts** and
   **Reset** for failed attempts. A disabled account cannot sign in and its sessions end; its content stays.

### Common questions

**Can I give a user a role directly, without a group?** Yes, a role can be assigned to a single user, but assigning
to groups is easier to keep track of. Module 3 explains it.

**What happens to the content a user created when the account is disabled?** Nothing. It stays published, with the
user as its owner.

**Can I import many users at once?** Not from these pages. Large imports are a developer task; this course creates
accounts one by one.

### Pitfalls

- **Real addresses.** Learners type their own address out of habit. Check every account for `example.invalid`.
- **Weak passwords refused.** The form rejects passwords shorter than the minimum and well-known words. Let learners
  read the message; it names the rule.
- **Disabling `admin` or yourself.** Never disable the account you are signed in with. On **Check user accounts**
  that is refused anyway (lesson 2.4); on the user's settings page it is not.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Users**, then the group `Training team` from lesson 2.1.
2. Under **Sub items**, press **Create new** and choose **User**.
3. Fill in **First name** `Tess`, **Last name** `Trainee`, **Username** `tess.trainee`, **Email**
   `tess.trainee@example.invalid`.
4. Type the password `short` in **Password** and **Confirm password**, and press **Send for publishing**. Read the
   message.
5. Type a password of at least 10 characters twice and press **Send for publishing**.
6. Create a second user the same way: `Sam Sample`, username `sam.sample`, address `sam.sample@example.invalid`.
7. Open `tess.trainee`, then its **Locations** tab. Add a location in the group **Editors**.
8. Open the **Roles** tab of the user: which roles come from which group?
9. Move `sam.sample` from `Training team` to **Members**.
10. Open `sam.sample`'s page and open **Configure user account settings**. Untick **Enable user account** and save.
11. In a private browser window, open the public site's sign-in page (`http://localhost:8088/user/login` on the lab)
    and try to sign in as `sam.sample`. Note the message.
12. Enable the account again and sign in once more in the private window.

## Expected result

- Step 4 is refused with a message about the minimum length.
- `tess.trainee` has two locations, in `Training team` and **Editors**, and shows the roles of **Editors**.
- `sam.sample` is in **Members**.
- Step 11: the sign-in is refused while the account is disabled; step 12 succeeds.
- The trainer checks by opening **Check user accounts** (lesson 2.4 shows it) or each learner's `Training team`.

## Quiz

1. Which fields make up the **User account** field?
2. Can a username be changed after the account is created?
3. A user is placed in two groups. Which roles does it have?
   A. Those of the first group B. Those of the main location only C. Those of both groups D. None until assigned
4. Where do you disable an account?
5. Why do test accounts use `example.invalid`?

<details>
<summary>Answers</summary>

1. **Username**, **Email**, **Password** and **Confirm password**.
2. No. The login cannot be changed once an account has one
   ([Change the administrator password](../../../change-the-admin-password.md#change-another-users-password-as-an-administrator)).
3. **C.** A user belongs to every group it has a location in.
4. On the user's page, **Configure user account settings**, **Enable user account**; or in bulk on
   **Check user accounts**.
5. The domain cannot receive mail, so no test message reaches a real person.

</details>

## Further reading

- [Change the administrator password](../../../change-the-admin-password.md): another user's password and account
  settings.
- [Changing your password](../../../../features/6.0/modern-password-change.md): the password rules.
- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): what groups give.

## Related pages

- [Module 2: Users and groups](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
