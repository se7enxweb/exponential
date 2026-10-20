---
title: "Lesson 3.4: Testing what a user may do"
audience: trainer, admin
category: training
order: 34
summary: Learners test a role by signing in as a test user, read a user's Policies tab, diagnose missing access, and fix it by narrowing or adding a policy. 35 minutes.
keywords: site administration, test permissions, access denied, policies tab, private window, test user, missing login policy, troubleshooting roles, audit
type: help
---
# Lesson 3.4: Testing what a user may do

A role is only right when someone has tried it. The reliable test is to sign in as a test account that holds the
role and do the work the role is meant for, and also try what it should not allow. This lesson teaches that routine
and how to read why access is refused.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 3: Roles and policies](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 3.3](assigning-roles.md): `News editors` assigned to `Training team`, limited to the `News` subtree |

## Learning objectives

After this lesson, learners can:

- test a role with a dedicated test account in a private browser window;
- read a user's **Roles** and **Policies** tabs to see what it holds and where it comes from;
- recognize the typical symptoms of a missing policy (no sign-in, missing menus, *Access denied*);
- fix a role and test again.

## Trainer's notes

### How to present it

Source: [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md) and
[Accounts and signing in](../../../accounts-and-sign-in.md).

1. **The routine** (3 minutes). One test account per role, in a group that has only that role, at
   `example.invalid`. Sign in in a private window so the administrator session stays open. Try three things it should
   do and two it should not.
2. **Reading what a user holds** (3 minutes). On the user's page, the **Roles** tab lists the roles of the user and
   of the groups above it, with limitations; the **Policies** tab lists the policies they give.
3. **Symptoms** (4 minutes):

   | Symptom | Usual cause |
   |---|---|
   | The sign-in to the administration is refused | no `user/login` for that siteaccess |
   | Signed in, but a top menu entry is missing | no policy for that module |
   | **Create new** does not offer a class | `content/create` lacks that Class, or the parent is outside the limitation |
   | *Access denied* on a page | the module or function is not granted there |

### Common questions

**Can I just switch to another user without their password?** Not in the standard administration. Use a test account
you created for the purpose.

**The user should have access but does not, and the role looks right.** Check the assignment's limitation (subtree or
section), the section of the content (Module 4), and whether the user is really in the group: its **Locations** tab.

**Do changed roles take effect at once?** Yes, after **Save**. A user who is signed in may need to reload the page.

### Pitfalls

- **Testing as the administrator.** Full access hides every mistake. Always test with the test account.
- **Test accounts in several groups.** Extra roles from another group mask the problem. Keep test accounts in one
  group only, or read their **Roles** tab first.
- **Leaving test accounts active on a live site.** Disable them after the test (lesson 2.4).

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Create a user `nina.news` with the address `nina.news@example.invalid` in the group `Training team` only.
2. Open `nina.news` and read its **Roles** and **Policies** tabs.
3. In a private browser window, sign in to the administration (`/admin`) as `nina.news`. Note what happens.
4. If the sign-in is refused: back as administrator, check that `News editors` has `user` / `login` for the `admin`
   siteaccess (lesson 3.2, step 7). Fix the role if needed and save.
5. Signed in as `nina.news`, note which top menu entries are visible.
6. Open the `News` folder and create an **Article** in it. Publish it.
7. Try to create an article in another folder outside `News`. Note what happens.
8. Try to edit an article that someone else created. Note what happens.
9. As administrator, write down for steps 6 to 8 which policy allowed or refused each action.
10. Sign out of the private window.

## Expected result

- `nina.news` signs in once `user/login` covers `admin`.
- The article in `News` is created and published; outside `News` the folder offers no **Create new** for articles,
  or the action is refused.
- Editing another user's article is refused if the `content/edit` policy was limited to Owner *Self*.
- The trainer checks one learner's list from step 9.

![The Users tab, where a test account in each group is the way to try what a role allows](../../../images/trainers/sa-access-check.webp)

## Quiz

1. Why test with a dedicated test account rather than the administrator account?
2. A test user cannot sign in to the administration. Which policy is most likely missing?
   A. `content/read` B. `user/login` for that siteaccess C. `setup/system_info` D. `role/assign`
3. Where do you see all the policies a user holds?
4. The role looks right, but the user still cannot edit. Name two other things to check.
5. True or false: a saved role change only applies after the user's next sign-in.

<details>
<summary>Answers</summary>

1. Full access hides every missing or wrong policy.
2. **B.** Without `user/login` on that siteaccess the sign-in is refused.
3. On the user's **Policies** tab (and the **Roles** tab for where they come from).
4. The assignment's limitation, the section or location of the content, the user's groups (its **Locations** tab).
5. **False.** It applies at once after **Save**; reload the page.

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md): the problems section lists
  more symptoms.
- [Permission checks for users with many role assignments](../../../../features/6.0/permission-checks-for-many-assignments.md):
  large sites.
- [Accounts and signing in](../../../accounts-and-sign-in.md): what a user sees when signing in.

## Related pages

- [Module 3: Roles and policies](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
