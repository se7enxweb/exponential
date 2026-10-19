---
title: "Lesson 3.5: Anonymous access and open registration"
audience: trainer, admin
category: training
order: 35
summary: Learners read what the Anonymous role lets every visitor do, check it as a signed-out visitor, and switch open registration on and off with the user/register policy. 40 minutes.
keywords: site administration, anonymous, anonymous role, visitors, public site, content read, section standard, user register, open registration, PolicyOmitList, access denied
type: help
---
# Lesson 3.5: Anonymous access and open registration

Every visitor who has not signed in is the anonymous user, and the roles of the **Anonymous Users** group decide what
the whole public may see and do. A careless change there can publish the user directory or close the site. This
lesson reads the Anonymous role, tests it as a visitor, and makes one deliberate change: opening and closing
registration.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 3: Roles and policies](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 3.4](testing-what-a-user-may-do.md) |

## Learning objectives

After this lesson, learners can:

- read the Anonymous role and say what every visitor may read and do;
- explain why content outside section Standard is hidden from visitors;
- test anonymous access in a private browser window;
- open registration by adding `user/register` to the Anonymous role, and close it again;
- explain how an upgraded site keeps registration open through `PolicyOmitList[]`.

## Trainer's notes

### How to present it

Source: [Sections in roles and policies](../../../../guides/sections.md#9-sections-in-roles-and-policies) and
[Decide who may register](../../../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data).

1. **The Anonymous role** (4 minutes). Open it on the projector. Policies such as `content/read` and `content/pdf`
   limited to Section Standard, `user/login` on the public siteaccesses, and reading feeds. Your site package may add
   more.
2. **Why Standard matters** (3 minutes). A new installation has five sections: Standard (1), Users (2), Media (3),
   Setup (4) and Design (5). Anonymous reads Standard only, which keeps user accounts and anything in another section
   away from visitors. Images in Media are shown when embedded in pages the visitor may read, depending on the site
   package's policies.
3. **Registration** (5 minutes). On a new installation registering needs the policy `user/register`. Add
   *User / Register* to the Anonymous role for open registration; the login pages then show the registration link,
   and only then. An upgraded site keeps registration open through `site.ini [RoleSettings] PolicyOmitList[]`; to
   close it, add `PolicyOmitListExclude[]=user/register` in the global override and clear the INI cache.
4. **Caches** (3 minutes). Visitors may be served cached pages. After a role change, clear the content cache (Module 5)
   before you conclude it did not work.

### Common questions

**Can I give visitors access to one more section?** Yes: edit the Anonymous role's `content/read` policy and tick the
section next to Standard. Module 4 builds a section for this.

**Visitors still see a page I took away.** A static cache, the HTTP cache or a proxy may still hold it. Clear them.

**Is the anonymous user an account someone can sign in with?** No. It cannot sign in; it is the identity of every
visitor who has not signed in.

### Pitfalls

- **Giving Anonymous `content/read` without limitation.** Visitors can then read user accounts, media folders and
  everything else. Never remove the Section limitation.
- **Testing while signed in.** Always use a private window, and check the header says you are not signed in.
- **Registration with real mail.** Opening registration on a live site sends activation mails; on the lab mail is
  written to files.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Open **Users > Roles and policies** and the role **Anonymous**. Write down its policies as sentences.
2. Open the **Users** group in the administration and note the node id of one user (the **Details** tab or the
   address).
3. In a private window, signed out, open the public site and then `/content/view/full/<that node id>`. Note the
   result.
4. In the private window, open the public sign-in page (`/user/login`). Is there a link to register?
5. As administrator, press **Edit** on the Anonymous role, then **New policy**: module `user`, function `register`,
   **Grant full access**. Press **Save**.
6. Clear the content cache: **Clear cache** in the right column with **Content**, or
   `php bin/php/ezcache.php --clear-tag=content` in a terminal.
7. Reload the sign-in page in the private window. Is the registration link there now?
8. Open it and look at the registration form. Do not register.
9. As administrator, remove the `user / register` policy from the Anonymous role again and **Save**.
10. Reload the sign-in page in the private window and check the link is gone (clear the content cache again if
    needed).

## Expected result

- Step 3 is refused: the user account is in section Users, which Anonymous may not read.
- On a new installation without open registration, step 4 shows no registration link; steps 5 to 7 make it appear;
  steps 9 and 10 remove it. (An upgraded site may show it from the start, through `PolicyOmitList[]`.)
- The trainer checks the Anonymous role on one learner's screen after step 9: no `user / register` policy left.

## Quiz

1. Which section may anonymous visitors read on a new installation?
   A. All of them B. Standard C. Users and Standard D. Media
2. Why can visitors not open user accounts on the public site?
3. Which policy opens registration on a new installation?
4. An upgraded site shows the registration link although no role has `user/register`. Why?
5. You removed a policy from Anonymous, but a visitor still sees the page. What do you check?

<details>
<summary>Answers</summary>

1. **B.** `content/read` and `content/pdf` limited to Section Standard
   ([Sections](../../../../guides/sections.md#2-the-sections-of-a-new-installation)).
2. User accounts are in section Users, and the Anonymous role reads Standard only.
3. `user/register` (*User / Register*) on the Anonymous role.
4. `user/register` is in `PolicyOmitList[]` there, which skips the check; close it with `PolicyOmitListExclude[]`.
5. The caches: the content view cache, the static cache, the HTTP cache or a proxy.

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): registration and node data.
- [Sections](../../../../guides/sections.md): the sections and the Anonymous role.
- [Security hardening](../../../security-hardening.md): what to check before a site goes public.

## Related pages

- [Module 3: Roles and policies](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
