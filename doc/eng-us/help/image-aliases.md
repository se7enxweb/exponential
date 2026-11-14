---
title: Image aliases
audience: admin, hosting
category: administration
order: 30
summary: Setup > Image aliases shows how the site's image aliases are made on demand and controls it - the dashboard, bulk runs to generate, regenerate, purge or resync, the test tools for one image and a staging source, the run history and the settings - plus the content tree's Image aliases menu and the command exp:image:alias.
keywords: image aliases, Bild-Aliase, on demand, setup/aliases, setup/managealiases, Setup, dashboard, bulk runs, generate, regenerate, purge, resync, staging, test tools, run history, imagealias.log, placeholder, swap script, WebP, AVIF, signing key, rotate, MaxParallel, MaxWait, pre-generation, exp:image:alias, context menu
context: setup/aliases
type: help
---
# Image aliases

An **image alias** is a size or variation of an uploaded image that the site's templates show: a thumbnail, an
article image, a full-width banner. Since Exponential 6.0.15 they are **made on demand**: a page lists its images
without converting anything, and each alias is made the first time a browser asks for it, or right after publishing in
the background. While an image is being made, visitors see a calm gray placeholder of the right size, which is
replaced by the real image a moment later.

**Setup > Image aliases** shows how that works on this site and lets you control it. How it works in depth:
[Image aliases made on demand](../features/6.0/image-aliases-on-demand.md).

## Open it

In the administration, click **Setup** in the top menu, then **Image aliases** in the System group, right after
**Cache** (the address is `https://www.example.com/admin/setup/aliases`). Seeing the page needs the permission
`setup/aliases`; starting runs, saving settings and every other action needs `setup/managealiases`. Administrators have
both. Someone with `setup/aliases` alone sees the page without its forms.

The page has five parts, as tabs at the top.

## Dashboard

What the site is doing now, and what needs attention:

- **Mode and delivery**: on demand (the default) or render (the old way: images converted while the page renders);
  the address form, how existing images are answered, how long a request may wait (`MaxWait`), how many conversions
  run at once and how many are running now.
- **Placeholder and swap script**, **format negotiation** (WebP/AVIF), **pre-generation after publishing** (with the
  aliases it makes when only some are chosen), and the number of aliases.
- **Converters of the format variants**, in the order they are tried, with whether each can write WebP and AVIF.
- **Environment and source**: only on a development or staging site that fetches its images from production. Shows
  whether the source answers, the failures in a row, the last error, and whether it is paused. **Reset the failures
  and ask the source again** ends a pause.
- **Job queue**: image jobs waiting, running and failed, and whether bulk runs are paused.
- **The last 24 hours**: aliases generated, placeholders answered, failures, deferred to a job, files staged, runs
  started. Each number opens the matching lines of the run history. **Deferred to a job** counts aliases that got no
  conversion slot in time: the visitor saw the placeholder and a job makes the alias. That is normal on a busy page
  and no failure; only real failures raise the warning.
- **Signing key**: the age of the key that signs image addresses, and how long the previous one is still accepted.
  **Rotate the key** makes a new one; addresses on pages cached with the old key keep working for the grace period
  (a week by default). Rotate it if you suspect the key has leaked.
- **What needs attention**: checks with a short explanation each (for example: no job queue, a source paused, many
  failures).

## Bulk runs

Work on many images at once:

- **Generate**: make the aliases that are missing.
- **Regenerate**: remove the aliases and make them again (after changing an alias's settings, for example).
- **Purge**: remove the aliases; the originals stay, and pages make them again on demand.
- **Resync** (only on a staging site): fetch missing originals and aliases from the source; **Overwrite files that differ** also
  replaces files that differ.

Choose the **scope**: the whole site, one node, or a node and its subtree; optionally classes, attributes, aliases,
**Changed since** (a date or "2 days") and **At most images**. Tick **Dry run** first to count the images without
changing anything. **Start the run** queues it as a job; its progress card updates every two seconds and has
**Cancel this job**. **Pause bulk runs** stops every bulk run on every server after its current image; **Resume bulk
runs** lets them go on where they stopped. Without a job queue a run of at most 50 images works in the request.

The same actions for one place in the content tree: right-click an item, **Advanced > Image aliases**, then generate,
regenerate or remove the aliases of the item or of its whole subtree. They run as jobs.

## Test tools

- **One image**: look up an object by node or object id. For each image field and language you see the original and
  every alias with the address a template gets now, its size and type, whether the file is there or will be made on
  demand, and its WebP/AVIF variants. **Make** makes one alias now and shows how long it took; **Check** only
  reports.
- **Connection to a source** (staging sites): asks the production site for its base address or a storage path, with
  the credential sent but never shown, and reports the status, type, size and time.

## Run history

The newest lines of `imagealias.log`: what was made, fetched, refused or queued, with the time it took. Filter by
event (or only the runs), run id, alias or a part of the path, and choose how many lines to show. The event `defer`
is an alias deferred to a job, `fail` a real failure; older lines that say `fail result=timeout` are shown as `defer`. **Follow** keeps the
list current every three seconds. A run id opens the lines of that run. The whole file is also in Setup > Log files.

## Settings

Every setting of the feature, with its help text and default: the mode, the address form, `MaxWait`, the conversions
at once (`MaxParallel`), negotiation, placeholder and swap script, pre-generation and the aliases it makes, and the
staging settings with the source's address and credential. **Preview the changes** shows what saving would change;
**Save** asks you to verify it's you, then writes `settings/override/image.ini.append.php` (with a backup). Tick
"Set here; tick to remove it" to go back to the shipped value. A credential is the **name** of a secret from Setup >
Secrets, never a password itself.

To go back to the old way at once, set the mode to **render**. Nothing is migrated; you can switch back any time.

## From a shell

`./console exp:image:alias` does everything the page does:

```bash
./console exp:image:alias status
./console exp:image:alias generate --subtree=93 --dry-run
./console exp:image:alias regenerate --node=93 --alias=i160,i320 --queue
./console exp:image:alias test --node=93
./console exp:image:alias status --pause="nightly import"
```

Every subcommand and option: [The command line](../features/6.0/image-aliases-on-demand.md#the-command-line-expimagealias).

## When something looks wrong

| You see | What to do |
|---|---|
| Gray placeholders that stay until a reload | The theme does not load the swap script `exp_image_swap.js`; ask your developer ([placeholders](../features/6.0/image-aliases-on-demand.md#placeholders-and-the-swap-script)). |
| Pages with many new images load slowly | Raise the conversions at once (`MaxParallel`, about half the server's processor cores); keep pre-generation on; put WebP/AVIF only on large aliases ([tuning](../features/6.0/image-aliases-on-demand.md#performance-and-tuning)). |
| Many images answer "Too many requests" (429) | The request shield counts image requests as page views; give images their own budget ([recipe](../examples/web-servers/image-aliases-on-demand.md#the-request-shields-image-budget)). |
| A missing image by its storage address is a 404 page | The web server rules for `index_image.php` are missing ([recipes](../examples/web-servers/image-aliases-on-demand.md)). |
| The staging source is paused | Look at the last error on the dashboard, fix the address or credential, then reset the failures. |
| Saving the settings fails | The settings file belongs to another system user; ask your hosting to give it to the web server's user. |

Something else: [When something goes wrong](when-something-goes-wrong.md).
