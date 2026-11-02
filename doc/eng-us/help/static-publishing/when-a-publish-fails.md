---
title: "When a publish fails"
audience: admin, website-creator, hosting, developer
category: administration
order: 24
summary: Troubleshooting by symptom - a build that stops, a refused page, a push that is refused or stays pending, a 404 on github.io, style sheets missing below /<repository>/, a custom domain without HTTPS yet, a build that is too large and a page that is missing.
keywords: static publishing, troubleshooting, publish failed, build failed, push refused, pending push, 404, github.io, CSS missing, base path, custom domain, HTTPS, certificate, too large, size limit, page missing, refused page, session cookie, debug output, GitHub Pages
type: help
---
# When a publish fails

## What you'll have at the end

The cause of the failure and the fix, or the right place to look next.

Start with the job's page (Setup > Static publishing > History, then the run): it shows the progress and the report
with the reason. On the console, `./console exp:static:status <target>` shows the last runs.

## The build stops

| Message | Cause and fix |
|---|---|
| Static publishing is switched off | Switch it on on the dashboard (or `[StaticPublishSettings] Enabled=enabled`) |
| No page was exported | The server could not fetch its own site. Set **Fetch pages from** (`CrawlURL`) in step 7 to an address the server reaches, such as `http://127.0.0.1/` |
| A page is refused: the response set the session cookie | Something on that page starts a session for visitors who are not signed in (a form, a basket). Leave the page out or change it |
| A page is refused: debug output or form token | Debug output is on for the build's address, or a form shows a token to anonymous visitors. Switch debug output off for that address; leave the form out or set **Forms** to hide |
| Another job of this target is running | Only one build, publish or rollback of a target runs at a time. Wait for it |

## The push fails

| Message | Cause and fix |
|---|---|
| Push state **pending** | The commit and tag are kept; the cronjob or `exp:static:push` tries again. Fix the cause below, then wait for the cronjob or run `./console exp:static:push <target>` |
| Authentication failed, 403 | The token expired, lacks **Contents: Read and write**, or is not allowed on this repository. Make a new one and update it in Setup > Secrets ([Connect to GitHub](connect-to-github-token-key-or-app.md)) |
| Permission denied (publickey) | The deploy key is not added to the repository, or without write access |
| Rejected, non-fast-forward | Someone else pushed to the branch. Static publishing never forces a push: keep the branch for the site alone, or pull the other change into the target's folder |
| Over the size limit | The build is larger than 1 GB or has a file over 100 MB. See [Images, files and downloads](images-files-and-downloads.md#size) |

## The site does not show

| Symptom | Cause and fix |
|---|---|
| 404 on `github.io` after the first publish | GitHub Pages is not switched on for the branch yet. Wizard step 4, **Set up on GitHub** (again, now that the branch exists), or the repository's **Settings > Pages** |
| Pages without styles below `/<repository>/` | A wrong base path. Use relative links ([Links and base path](links-and-base-path.md)) |
| The old version still shows | GitHub Pages needs a minute or two; the box **On GitHub** in History shows its build state. Reload without the browser cache |
| Your domain is "not secure" | GitHub issues the certificate after the DNS is right; it can take up to a day ([Use your own domain](use-your-own-domain.md)) |
| A page is missing | It is left out, beyond the crawl depth, reachable only with a query string, or not readable by Anonymous. The build report lists skipped pages |

## Still stuck

Read the log of the job, run `./console exp:static:build <target> --dry-run` to see the build without changing
anything, and see the [FAQ](faq.md) and the guide [Host static sites on GitHub Pages](../../guides/host-static-sites-on-github-pages.md#troubleshooting).

## Next step

[History and rollback](history-and-rollback.md): go back to the last good publish while you fix it.
