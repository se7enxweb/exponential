---
title: Deploying a DXP project
audience: dxp-developer
category: dxp-development
order: 38
summary: The production deploy of a DXP project in order - pull, vendors without dev packages, migrations, public assets, admin and site builds, translations, cache clear and warm-up, reindex when needed - with the checks before and after, and what never goes to a server.
keywords: deploy, production, composer install --no-dev, migrations, assets:install, yarn build, cache warmup, reindex, release, rollback, checklist
type: help
---
# Deploying a DXP project

This page is for developers and operators who put a new version of a DXP project live. The order is the skeleton
guide's production checklist (section 15), with the checks around it.

## Contents

1. [Before you deploy](#before-you-deploy)
2. [The deploy, step by step](#the-deploy-step-by-step)
3. [After the deploy](#after-the-deploy)
4. [Building elsewhere](#building-elsewhere)
5. [Rolling back](#rolling-back)
6. [Never on a server](#never-on-a-server)

## Before you deploy

- The change passed its tests on a copy with production-like data.
- `composer.lock` and `yarn.lock` are committed; the server installs exactly them.
- Pending migrations were tried with `--dry-run` on a copy.
- A database backup exists, and you know how to restore it.

## The deploy, step by step

```bash
# 1. Code
git pull --rebase

# 2. Vendors
composer install --no-dev -o

# 3. Database
php bin/console doctrine:migration:migrate --allow-no-migration --env=prod

# 4. Public bundle assets
php bin/console assets:install --symlink --relative public --env=prod

# 5. Admin assets (when admin-ui or a bundle's assets changed)
source ~/.nvm/nvm.sh && nvm use 20 && yarn ibexa:build

# 6. Site assets (when the theme changed)
yarn build

# 7. Admin JavaScript translations
php bin/console bazinga:js-translation:dump public/assets --merge-domains --env=prod

# 8. Caches
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 9. Search (when the content model changed)
php bin/console exponential:reindex --env=prod
```

If message workers run, send `php bin/console messenger:stop-workers` so they restart with the new code. If you
changed PHP settings, reload PHP-FPM.

## After the deploy

```bash
curl -sI https://<your host>/ | head -1
curl -sI https://<your host>/adminui/ | head -1
tail -n 50 var/log/prod.log
```

Open one page of every important content type, and sign in to `/adminui/`.

## Building elsewhere

Steps 5 and 6 need Node.js. Many teams build in CI and deploy the built `public/assets/` and `public/bundles/` with
the code, so production servers carry no Node.js and no build tools. Either way, build with the versions the project
pins ([Building assets](building-assets-with-yarn-and-encore.md#building-for-production)).

## Rolling back

Code rolls back with Git; a database migration does not by itself. Prefer migrations that are backward compatible
(add first, remove in a later release), so the previous code still runs on the new schema. When that is not possible,
restore the backup made before the deploy.

## Never on a server

- `APP_ENV=dev` or `APP_DEBUG=1` in production.
- The development dependencies (`--no-dev` keeps them out).
- `.env.local` from another environment, or JWT keys copied between environments.
- The installer's default administrator password.

## Related pages

- [Updating](updating-dxp.md)
- [Doctrine migrations](doctrine-migrations.md)
- [DXP project checklist](dxp-project-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
