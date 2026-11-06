---
title: Migrating URLs
audience: admin, website-creator
category: website-creation
order: 445
summary: Keep the old addresses of a site working after a move to Exponential - collect them, build a mapping file, import it with a preview and a dry run, test it, undo it if needed, and export it for the web server or a backup.
keywords: migrating URLs, migration, relaunch, old addresses, redirect, 301, URL map, mapping file, import, export, undo, dry run, sitemap, 404, CSV, Excel, htaccess, nginx, URL translator, URL wildcards, exp:url:import, exp:url:export
type: help
---
# Migrating URLs

When you move a site to Exponential, or give it a new structure, its old addresses stop working unless you send
them somewhere. Links on other sites, bookmarks and search results then end on a "page not found", and search
engines drop pages that had a good place. This tutorial takes you through keeping them working, from the list of old
addresses to a tested import that you can take back at any time.

You need an administrator account (or a role with `content/urltranslator_import`) and, for the command line steps,
a shell in the installation's root. Every step works in the administration as well as on the command line.

[Help](README.md) · Background: [Importing and exporting URL maps](../features/6.0/url-map-import-export.md) ·
Example files: [URL map examples](../examples/url-maps/README.md)

## Contents

1. [Collect the old addresses](#1-collect-the-old-addresses)
2. [Build the mapping file](#2-build-the-mapping-file)
3. [Import it with a preview and a dry run](#3-import-it-with-a-preview-and-a-dry-run)
4. [Test the old addresses](#4-test-the-old-addresses)
5. [Undo an import](#5-undo-an-import)
6. [Export for the web server or a backup](#6-export-for-the-web-server-or-a-backup)

## 1. Collect the old addresses

Start with every address the old site had, before you switch it off. Two places have them:

- **The old site's sitemap**, usually `https://www.old-site.com/sitemap.xml`. It lists the pages the site wanted
  search engines to find. Save the addresses in a text file:

  ```bash
  curl -s https://www.old-site.com/sitemap.xml | grep -o '<loc>[^<]*</loc>' \
      | sed -e 's#<loc>##' -e 's#</loc>##' > var/tmp/old-urls.txt
  ```

- **The web server's access log**, after the switch: the addresses visitors still ask for and that now answer
  "404 Not Found", the most asked first. With the usual Apache or nginx log format:

  ```bash
  awk '$9 == 404 {print $7}' /path/to/access_log | sort | uniq -c | sort -rn | head -50
  ```

  This catches what the sitemap misses: old addresses with a query (`/index.php?id=12`), files, and addresses other
  sites link to. Look at it again a few days after the switch.

If the old site's web server had its own redirects (an `.htaccess` file or an nginx configuration), keep that file:
Exponential reads it directly in step 3.

## 2. Build the mapping file

A mapping file lists each old address and its new one. The simplest is a CSV file with two columns, which any
spreadsheet program opens and saves:

```text
old_url,new_url
/about-us.html,/fitness
/recipes/hummus.html,/recipes/home-made-hummus
/index.php?id=12,/recipes
/old-recipes/*,/recipes/{1}
```

Turn the list from step 1 into the start of one, with the host taken off and the new address left empty:

```bash
{ echo 'old_url,new_url'; sed -e 's#^https\{0,1\}://[^/]*##' -e 's#$#,#' var/tmp/old-urls.txt; } > var/tmp/old-site.csv
```

Then open it in a spreadsheet program and fill in the `new_url` column. To find the new address of a page, open the
page on the new site and copy the path from the address bar, or use the page's node ID (`node:163`), which keeps
working when the page moves later.

Tips for the mapping:

- **Group what moved together into a wildcard.** `/old-recipes/*` to `/recipes/{1}` covers every old recipe whose
  last part stayed the same; then you only list the ones that changed.
- **Leave out what has no new page.** A redirect to the front page for every lost page confuses visitors and search
  engines; a "page not found" is the honest answer for content that is gone.
- **Mark temporary moves.** For a page that will come back, set the `kind` column to `redirect-temporary`, so search
  engines keep the old address.
- **Old addresses with a query** (`/index.php?id=12`) work as they are: they are matched on the path and the query.
- **Full old addresses** (`https://www.old-site.com/about-us.html`) are fine too; you tell the import which
  siteaccess the old host stands for.

The columns and kinds are explained in [Importing and exporting URL maps](../features/6.0/url-map-import-export.md#kinds-and-statuses).
Excel (`.xlsx`), LibreOffice (`.ods`), JSON and the old site's Apache or nginx rules are read as well. Ready-made
examples: [URL map examples](../examples/url-maps/README.md).

## 3. Import it with a preview and a dry run

### In the administration

1. Open **Setup** > **URL translator** and click **Import**.
2. Choose the file (or paste the rows), and the **Siteaccess of bare paths**: the site the old addresses belong to.
   If the file has full addresses of the old host, enter it under **Old hosts**, for example `www.old-site.com = site`.
3. Leave **When an old address has an entry already** on keeping the entry, the first time.
4. Click **Preview**. Each row is listed with its state. Look at every state other than **New**:
   - **Broken target**: the new address does not exist. Correct it in the file and import it again.
   - **Conflict**: the old address is a page of the new site, or an address of the system. It is never imported, and
     does not need to be: the address works already.
   - **Not importable**: the row's notes say what is wrong with it.
   - **Unknown host**: map the host under **Plan again**.
5. Click **Dry run**. It checks every row against the site, exactly as the import would, and counts what it would do,
   without writing anything.
6. Click **Import** and confirm who you are ("Verify it's you"). The import gets a number; you need it to undo it.

The screens are described one by one in [URL maps: import, export and undo](../guides/url-maps.md).

### On the command line

```bash
./console exp:url:import var/tmp/old-site.csv -s site --dry-run --report=var/tmp/plan.csv
```

This plans every row, writes nothing, prints the counts and the rows that would not be imported, and writes the whole
plan to `var/tmp/plan.csv` to open in a spreadsheet. `-s` is the siteaccess the bare paths belong to. Correct the
file and run the dry run again until only the rows you expect are left out. Then import:

```bash
./console exp:url:import var/tmp/old-site.csv -s site
```

For an old site's rules, pass the file as it is: `./console exp:url:import old-site.htaccess -s site`. When the file
has full addresses of a host that is not this installation's, add `--host=www.old-site.com=site`.

## 4. Test the old addresses

Right after the import, ask the site for every old address and look at the answer. A permanent redirect answers
`301` with the new address:

```bash
while IFS=, read -r old new rest; do
    [ "$old" = old_url ] && continue
    printf '%s %s\n' "$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "https://www.example.com$old")" "$old"
done < var/tmp/old-site.csv
```

Replace `https://www.example.com` with the new site's address. Every line should start with `301` (or `302`, `307`
for temporary redirects), or `200` for an `alias`. A `404` means the address was left out or not imported: look it up
in the plan of step 3. On the URL wildcards page, **Try an address** shows where a single address goes.

Check the web server's 404 list from step 1 again after a few days, and import a second file for what turns up.

## 5. Undo an import

An import can be taken back as a whole, at any time:

- In the administration: **Setup** > **URL translator** > **Import history**, open the import and click **Undo
  import**.
- On the command line: `./console exp:url:batches` lists the imports with their numbers;
  `./console exp:url:undo 3` undoes import 3.

Undo removes every entry the import created and puts back every entry it replaced. An entry that was changed by hand
after the import is kept; tick **Also undo the entries changed after the import** (`--force` on the command line) to
undo it too.

## 6. Export for the web server or a backup

The URL map of the site can be written out again, in any format the import reads:

- In the administration: **Setup** > **URL translator** > **Export**, choose the format and download the file.
- On the command line:

  ```bash
  ./console exp:url:export --output=var/tmp/url-map.xlsx
  ./console exp:url:export --kind=wildcard,redirect --format=nginx > var/tmp/redirects.nginx
  ```

Use an export:

- **as a backup** before a large import or a change of the site's structure;
- **to edit** the map in a spreadsheet and import it again (choosing to replace the entries that are there);
- **for the web server**: the Apache and nginx exports are rules that answer the redirects before PHP runs. Entries
  only Exponential can answer (aliases, `node:` targets) are written as `#exp:` comments, which the server ignores.

## Related pages

- [Importing and exporting URL maps](../features/6.0/url-map-import-export.md)
- [URL map examples](../examples/url-maps/README.md)
- [URL maps: import, export and undo](../guides/url-maps.md)
- [How do I redirect a group of old addresses (URL wildcards)?](manual/how-do-i-redirect-old-addresses.md)
- [URL wildcards and custom aliases](creators/website-creators/url-wildcards.md)
- [Redirects and moved content](creators/website-creators/redirects-and-moved-content.md)
- [The URL map file formats](../specifications/6.0/url-map-file-formats.md)
