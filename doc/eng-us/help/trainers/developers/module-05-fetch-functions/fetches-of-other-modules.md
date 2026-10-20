---
title: "Lesson 5.3: Fetches of other modules"
audience: trainer, developer
category: training
order: 53
summary: Learners find the fetch functions every kernel module offers, use the user, section and relation fetches, check access with has_access_to, and read site data from a template. 40 minutes.
keywords: developers course, fetch, user current_user, has_access_to, section list, related_objects, reverse_related_objects, content object, site_data, function_definition.php, kernel modules
type: help
---
# Lesson 5.3: Fetches of other modules

Content is not the only thing templates fetch. The user module tells a template who is signed in and what they may
do, the section module lists sections, the content module also knows relations, bookmarks and drafts. In this lesson
learners find every module's fetch functions and use the ones a site needs most.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 5: Fetch functions](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 5.2](filters-sorting-and-paging.md) |

## Learning objectives

After this lesson, learners can:

- list the modules that offer fetch functions and the functions of one module;
- show the signed-in user with `fetch( 'user', 'current_user' )`;
- check a permission with `fetch( 'user', 'has_access_to', ... )`;
- list related and reverse related objects of an item;
- read an allowed site data row with `fetch( 'content', 'site_data', ... )`.

## Trainer's notes

### How to present it

Sources: `kernel/*/function_definition.php` and
[Templates and design, part 9](../../../../guides/templates-and-design.md#9-site-data-in-templates).

1. **Which modules** (3 minutes). `ls kernel/*/function_definition.php` lists them: `content`, `user`,
   `section`, `class`, `search`, `shop`, `url`, `workflow` and more. Extensions add their own the same way
   (lesson 5.4).
2. **The user module** (4 minutes). `current_user`, `is_logged_in`, `member_of`, `user_role`, `has_access_to`,
   `logged_in_count` and more. `has_access_to` with `module` and `function` answers "may the current user do
   this?" and is how templates hide buttons.
3. **Relations** (3 minutes). `related_objects` and `reverse_related_objects` with `object_id`; the `_count`
   variants count.
4. **Site data** (3 minutes). `fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )` returns a row of
   **Setup** > **Site data**, but only names listed in `site.ini [SiteDataSettings] TemplateReadable[]`; the list
   is empty by default.
5. **Cache awareness** (2 minutes). A page in the view cache is the same for everyone with the same permissions:
   fetching the user's name in a cached full view shows it to others. Per-user parts belong in the pagelayout or in
   uncached parts.

### Common questions

**How do I hide an edit link from visitors?** Use `$node.can_edit` on the node, or `has_access_to` for module-level
rights; [Role and policy template operators](../../../../features/6.0/role-and-policy-template-operators.md) adds
operators such as `has_policy`.

**What does `current_user` return for a visitor?** The anonymous user, with login `anonymous`.

**Is there a search fetch?** Yes, `fetch( 'content', 'search', ... )` and the `search` module's own; for a real
search page use the search module's views.

### Pitfalls

- **Per-user data in cached views.** See point 5; test while signed in as two different users.
- **Hiding is not protecting.** A hidden button does not stop a request; the module view must check permissions
  itself (module 6).
- **Site data names.** A name not in `TemplateReadable[]` returns `false` and leaves a notice in the debug output.

## Hands-on exercise

Use the template playground and run it after each step. The playground runs as the anonymous user.

1. List the modules with fetch functions: `ls kernel/*/function_definition.php`.
2. List the user module's functions: `grep -o "FunctionList\['[a-z_]*'\]" kernel/user/function_definition.php`.
3. Print the current user's login: `{fetch( 'user', 'current_user' ).login}`.
4. Check a permission:

   ```
   {if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'read' ) )}may read{else}may not read{/if}
   {if fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'setup' ) )}may use setup{else}no setup{/if}
   ```

5. Count the sections: `{fetch( 'section', 'list' )|count}`.
6. Count the relations of node 2's object:
   `{fetch( 'content', 'related_objects_count', hash( 'object_id', $node.contentobject_id ) )}`.
7. Try a site data name that is not allowed:
   `{fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )|choose( 'not readable', 'readable' )}`.
8. On the site, add the user check of step 3 to the override template of lesson 3.2, sign in to the site as a
   learner account, and compare with signed out. Then remove it again: it does not belong in a cached view.
9. Commit the playground template.

## Expected result

- Step 3 prints `anonymous` in the playground.
- Step 4 prints `may read` and `no setup` for the anonymous user.
- Step 5 prints the number of sections shown in **Setup** > **Sections**.
- Step 7 prints `not readable`.
- Step 8: depending on the view cache, the page may show the wrong user to the other session, which is the point
  of the step.
- The trainer asks why step 8 must be removed again.

## Quiz

1. Which fetch tells a template who is signed in?
2. Which fetch answers "may the current user use this module function"?
   A. `user/member_of` B. `user/has_access_to` C. `content/access` D. `section/roles`
3. Why is the user's name a poor thing to print in a cached full view?
4. What must be set before a template can read a site data row?
5. Where do you find all fetch functions of the section module?

<details>
<summary>Answers</summary>

1. `fetch( 'user', 'current_user' )`.
2. **B.** `fetch( 'user', 'has_access_to', hash( 'module', ..., 'function', ... ) )`.
3. The cached page may be shown to other visitors with the same permissions.
4. The name must be listed in `site.ini [SiteDataSettings] TemplateReadable[]`.
5. In `kernel/section/function_definition.php`.

</details>

## Further reading

- [Templates and design, part 9](../../../../guides/templates-and-design.md#9-site-data-in-templates): site data in
  templates.
- [Role and policy template operators](../../../../features/6.0/role-and-policy-template-operators.md).
- [Roles and policies](../../../../guides/roles-and-policies.md): the permissions the fetches respect.

## Related pages

- [Module 5: Fetch functions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
