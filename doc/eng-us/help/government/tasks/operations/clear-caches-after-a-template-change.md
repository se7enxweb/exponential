---
title: Clear caches after a template change
audience: government, admin, developer
category: public-sector
order: 80
summary: Step by step - make a changed or new template visible on the public site - clear the template caches (and the override cache for new templates), the content view cache and Velocity's response cache, without a restart.
keywords: template change, template cache, template-override, design_base, content cache, Velocity cache, exp:cache clear, new template, stylesheet, ezjscore packer
type: help
---
# Clear caches after a template change

This task is for administrators and developers after a design change has been deployed. Template and stylesheet
changes need fewer steps than PHP changes: no restart, only caches ([Deploying, section 7](../../../../guides/deploying.md#7-ship-a-php-change-with-one-command)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The template is deployed to the server.
- Shell access, or **Setup > Cache management** with `setup/managecache`.

## Steps

| You changed | Run |
|---|---|
| An existing template | `./console exp:cache clear --id=template --allow-root-user` |
| A new template or a new override rule | also `php bin/php/ezcache.php --clear-id=template-override --allow-root-user` and `--clear-id=design_base` |
| A stylesheet or script bundled by the packer | `./console exp:velocity deploy --packer --allow-root-user`, or clear the packer and template-block caches |

Then the page caches:

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user
./console exp:velocity cache clear --allow-root-user
```

In the browser: **Setup > Cache management** has the groups *Templates* (Template cache, Template block cache,
Template override cache), *Content and views* and *Pages, queries and the server*, each with **Clear**.

## Check it worked

- Open the changed page in a private window, as a visitor.
- If a browser still shows an old stylesheet, a hard reload fetches it.
- New template not used at all, page renders with an empty content area: the override cache was not cleared.

## Related pages
- [Clear caches after a settings change](clear-caches-after-a-settings-change.md)
- [Clear the Velocity response cache](clear-the-velocity-response-cache.md)
- [Stale cache](../../operations/incidents/stale-cache.md)
- [Templates and design guide](../../../../guides/templates-and-design.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
