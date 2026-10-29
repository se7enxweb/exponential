# Upgrade note: the public policies of the Anonymous role

A visitor who is not signed in is the anonymous user and has the policies of the **Anonymous** role. This note says
which policies a public site gives that role, why a new installation could refuse every registration, and how to
complete the role of an installation that exists.

## What was wrong

From 12 October 2026 on, a new installation writes `PolicyOmitListExclude[]=user/register` (site.ini
`[RoleSettings]`, in `settings/override/site.ini.append.php`): registering is no longer open to everybody but needs
the policy **User / Register**. The base data gave the Anonymous role that policy only from 19 October 2026 on, and
none of the site packages (`sevenx_multisite`, `sevenx_site`, `cjw_multisite`, the ezwebin, ezflow and ezdemo packages,
`plain_site`, and their `_clean` variants) granted it itself. An installation made in between refuses every
registration (`user/register` answers with the sign-in page), and its sign-in pages show no "register" link.

An installation upgraded from an older version is not affected as long as its settings do not exclude `user/register`
from `PolicyOmitList[]`: registration is then open through that list, policy or not.

## What changes

- **The setup wizard and the kickstarter** check, after the site package's roles, that the Anonymous role has
  `user/register` and add it when it is missing (`expAnonymousPublicPolicies::ensure()`, in
  `kernel/setup/steps/ezstep_create_sites.php`). This holds for every site package and every base data, on every
  database engine.
- **The site packages** `sevenx_multisite`, `sevenx_multisite_clean`, `sevenx_site`, `sevenx_site_clean` and
  `cjw_multisite` grant `user/register` themselves when the role lacks it, and, because they install cjw_newsletter,
  `newsletter/subscribe`, `newsletter/configure`, `newsletter/unsubscribe` and `newsletter/archive`: the subscription
  form, the two links of every newsletter mail and the web version of a sent newsletter are opened by visitors who
  are not signed in. A policy the role has already is never added twice.
- **`bin/php/addanonymouspublicpolicies.php`** completes the role on an installation that exists (below).

## The policies a public site gives the Anonymous role

| Policy | Why | Where it comes from |
| --- | --- | --- |
| content / read, Section Standard | reading the public content; content/view, content/search, content/advancedsearch and content/download declare the function *read*, so search, advanced search and downloading a public file need nothing more | base data |
| content / pdf, Section Standard | the PDF view of public content | base data |
| user / login, SiteAccess = the public siteaccesses | signing in on the site (not in the administration); also user/activate, which declares *login* | site package |
| user / register | registering (`user/register`) and the confirmation page (`user/success`, which declares *register*) on a new installation, which takes `user/register` out of `PolicyOmitList[]` | base data from 6.0.15, setup step, site packages, upgrade script |
| rss / feed | the RSS feeds `rss/feed/<name>` | site packages, upgrade script |
| tags / view (eztags) | the tag pages that article and recipe pages link to | site packages, upgrade script |
| info-collection / read | the lead form of the Bold site | `sevenx_*` and `cjw_multisite` packages |
| content / read, Section Media | images and files embedded in public pages | site packages (class list or section, by package) |
| ezjscore / call, FunctionList ezstarrating_rate, ezstarrating_user_has_rated, loadMore | rating, and the "load more" of layout lists | site packages |
| newsletter / subscribe, configure, unsubscribe, archive (cjw_newsletter) | see above | `sevenx_*` and `cjw_multisite` packages, upgrade script |
| osm_proxy / tiles (cjw_themes_jumper) | the map tiles of the CJW design | `cjw_multisite` |

Not needed as a policy: `user/forgotpassword`, `user/activate`, `user/success` and `user/login` are in
`PolicyOmitList[]`; `oauth/authorize` checks its policy itself, after the visitor has signed in. Not for visitors:
`ezinfo` (switched off on public siteaccesses by the site packages).

## What to do when you upgrade

Run the script without options first: it is a dry run. It lists every policy it would add, and the reason a policy
is left out (the role has it already, in any form, also limited or as `module/*`; or the module is not installed),
and ends with `PASS: ... (dry run, nothing changed; --apply writes)`. Then run it with `--apply`:

```bash
php bin/php/addanonymouspublicpolicies.php            # dry run: what it would add
php bin/php/addanonymouspublicpolicies.php --apply    # adds it
```

With `--apply` it adds only what is missing, without limitations, expires the role cache, checks what was stored and prints `PASS`
or `FAIL`. A second run adds nothing.

- `--skip=user/register` keeps registration closed on a site that removed the policy on purpose (the script
  cannot tell a removed policy from one that was never there).
- `--only=user/register` adds only that policy.
- `--role=<name or ID>` completes another role than Anonymous.

The script never changes `PolicyOmitList[]` or `PolicyOmitListExclude[]`, and never touches the role's other policies.

To close registration on a new installation, remove *User / Register* from the Anonymous role. To close it on an
upgraded one, add `PolicyOmitListExclude[]=user/register` to `[RoleSettings]` in
`settings/override/site.ini.append.php` and do not give the role the policy.

Code: `kernel/classes/expanonymouspublicpolicies.php`, command
`kernel/private/classes/commands/addanonymouspublicpolicies.php`. Test without a database:
`tests/tests/kernel/classes/expAnonymousPublicPoliciesTest.php`.
