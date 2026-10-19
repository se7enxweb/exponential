---
title: "Lesson 6.3: Policies for your views"
audience: trainer, developer
category: training
order: 63
summary: Learners declare a policy function in their module, tie the greet view to it, grant it to the Anonymous role in Users > Roles and policies, and check the result with has_access_to; exp:accesscheck explains content access. 50 minutes.
keywords: developers course, policy, policy function, FunctionList, functions, role, Anonymous role, New policy, Grant access to one function, hasAccessTo, exp:accesscheck, has_access_to, permissions, limitations
type: help
---
# Lesson 6.3: Policies for your views

A view is only as safe as its permission check. The kernel does the check for you when a view names a **policy
function**: users then need a role with a policy for that function. In this lesson learners declare the function
`greet`, tie the view to it, grant it to the Anonymous role on their training installation, and check who may open
the view from the command line.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 6: Modules and views](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators who build roles |
| Prerequisites | [Lesson 6.2](a-view-of-your-own.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- declare policy functions in `$FunctionList` of a `module.php`;
- tie a view to a function with `'functions' => array( '<function>' )`;
- explain what happens for a view without `functions`;
- grant a module function to a role in **Users** > **Roles and policies**;
- check module function access with `has_access_to`, and content access of a user with `exp:accesscheck`.

## Trainer's notes

### How to present it

Sources: [Roles and policies](../../../../guides/roles-and-policies.md), `kernel/section/module.php` (its
`$FunctionList` with limitations) and `eZUser::hasAccessToView()` in `kernel/classes/datatypes/ezuser/ezuser.php`.

1. **Functions** (4 minutes). `$FunctionList['greet'] = array();` declares a policy function without limitations.
   The section module's `$FunctionList['assign']` shows limitations (Class, Section, Owner) a role can set.
2. **Tying a view** (3 minutes). `'functions' => array( 'greet' )` in the view. Several functions are combined with
   "and"; a string such as `'read or edit'` is an expression.
3. **No `functions`** (2 minutes). The view then needs a policy for the whole module (every function), which only
   broad roles such as an administrator's have. That was the situation in lesson 6.2.
4. **Granting** (4 minutes). **Users** > **Roles and policies**, open a role, **Edit**, **New policy**, choose the
   module, then **Grant access to one function**, the function, **Grant full access**, and **Save**.
5. **Checking** (2 minutes). `exp:accesscheck` explains content access (`content/read`, `edit` ...) of a user on a
   node or object, for example `php bin/php/console exp:accesscheck --user=anonymous --node=2`; for a module function
   templates use `fetch( 'user', 'has_access_to', ... )` (lesson 5.3) to hide links, never to protect.

### Common questions

**Do I need to clear caches after changing a role?** Role changes are picked up by the kernel; users who are signed
in may need to sign in again for cached permission data, and pages in the view cache may need the content cache
cleared.

**Can I add limitations to my own function?** Yes: give the function an array of limitation definitions, as the
section module does. The role editor then offers them.

**What is the difference between the module policy and a function policy?** A module policy grants every function
of the module, present and future; a function policy grants one.

### Pitfalls

- **Granting the whole module to Anonymous.** Easy in the role editor and dangerous: grant the one function.
- **Checking only in the template.** A hidden link is not a protected view. The view's `functions` is the check.
- **Shared installation.** Changing the Anonymous role affects every learner. Each learner works on their own
  installation, or the trainer makes the role change once on the projector.

## Hands-on exercise

On the training installation, in `extension/mysite_hello`, and in the administration as an administrator.

1. Change `modules/hello/module.php`:

   ```php
   <?php
   $Module = array( 'name' => 'hello' );
   $ViewList = array();
   $ViewList['greet'] = array(
       'functions' => array( 'greet' ),
       'script' => 'greet.php',
       'params' => array( 'Name' ),
       'unordered_params' => array( 'times' => 'Times' ) );
   $FunctionList = array();
   $FunctionList['greet'] = array();
   ```

2. Check it with `php -l` and clear the caches: `php bin/php/ezcache.php --clear-tag=ini` and
   `--clear-id=global_ini`.
3. Signed out, open `/hello/greet/Ada`: access is refused.
4. In the administration open **Users** > **Roles and policies** and the role **Anonymous**. Read its policies.
5. Press **Edit**, then **New policy**. Choose the module `hello`, then **Grant access to one function**, choose
   `greet`, then **Grant full access**.
6. Press **Save** on the role.
7. Signed out, open `/hello/greet/Ada` again.
8. Compare with content access, which the access checker explains:
   `php bin/php/console exp:accesscheck --user=anonymous --node=2`.
9. In the playground, check the function:
   `{fetch( 'user', 'has_access_to', hash( 'module', 'hello', 'function', 'greet' ) )|choose( 'no', 'yes' )}`.
10. Remove the policy from the Anonymous role again if your trainer asks you to, and commit `module.php`.

## Expected result

- Step 3: the site's access denied page (or the login form).
- Step 5: the role editor lists `hello` among the modules and `greet` as its function.
- Step 7: the greeting is shown to visitors.
- Step 9 prints `yes` after step 6 (the playground runs as the anonymous user).
- The trainer checks one learner's Anonymous role: one policy `hello/greet`, not `hello/*`.

## Quiz

1. Which array of `module.php` declares policy functions?
2. A view has no `functions` key. Which policy does a user need?
   A. None B. A policy for the whole module C. `content/read` D. `user/login`
3. Which button in the role editor starts adding a policy?
4. Why is hiding a link with `has_access_to` not enough?
5. Why grant `hello/greet` rather than the whole `hello` module to Anonymous?

<details>
<summary>Answers</summary>

1. `$FunctionList`.
2. **B.** A policy for the whole module (`eZUser::hasAccessToView()`).
3. **New policy**.
4. The view can still be opened by its address; only the view's `functions` check protects it.
5. A module policy grants every function of the module, including ones added later.

</details>

## Further reading

- [Roles and policies](../../../../guides/roles-and-policies.md): roles, policies, limitations and assignments.
- [Role and policy order](../../../../features/6.0/role-policy-order.md) and
  [Permission checks for many assignments](../../../../features/6.0/permission-checks-for-many-assignments.md).
- [Content limitation handlers](../../../../features/6.0/content-limitation-handlers.md): limitations written in PHP.

## Related pages

- [Module 6: Modules and views](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
