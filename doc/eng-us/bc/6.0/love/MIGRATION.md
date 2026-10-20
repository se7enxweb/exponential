# Moving a site from LoveStack

**Most sites do not need to do anything.** If you run Exponential and never ran LoveStack,
nothing on this page applies to you. The features Exponential took over from LoveStack are part
of 6.0.15, switched off where they could change how an existing site behaves.

This page is for those who run **LoveStack** (Mugo Web's distribution of the legacy kernel,
`mugoweb/ezpublish-legacy`) and want to move a site to Exponential 6, and for developers who used
a LoveStack-only API in their own code. A LoveStack database is close to Exponential's, so the
move is mostly an ordinary upgrade with two extra commands. The upgrade guide has the same path
in short: [Coming from LoveStack](../../../guides/upgrading.md#coming-from-lovestack).

## Step 1 — Do you need to do anything?

Run this from your installation root:

```sh
grep -l -i "lovestack" lib/version.php composer.json composer.lock 2>/dev/null
```

| What it prints | What it means | What to do |
|---|---|---|
| **Nothing** | this installation is not LoveStack | nothing; you are done |
| `lib/version.php` (and maybe `composer.*`) | this installation runs LoveStack | read on |

To check a database rather than the code (for example a copy you were given):

```sql
SELECT name, value FROM ezsite_data
 WHERE name IN ( 'ezpublish-version', 'db-schema-version' );
```

`lovestack` and a `db-schema-version` row mean a LoveStack database; a number such as
`6.0.15stable` means Exponential. Once Exponential is installed (step 3), one command does both
checks and also lists the settings and extensions that need attention:

```sh
./console exp:lovestack check
```

## Step 2 — Before you start

1. **Back up** the code, the database and the storage directory:

   ```sh
   tar czf ../site-code-backup.tar.gz --exclude=var/cache --exclude='var/*/cache' .
   mysqldump --single-transaction --routines mydatabase > ../site-db-backup.sql
   tar czf ../site-storage-backup.tar.gz var/storage var/*/storage
   ```

   Replace `mydatabase` (use `pg_dump` for PostgreSQL). Keep the backups until the moved site
   has run for a while.

2. **Check PHP.** Exponential 6 runs on PHP 8.1 to 8.5. LoveStack's `composer.json` allows PHP
   7.3–7.4 and 8.1–8.3, so a site on PHP 7 moves to PHP 8 at the same time: `php -v`.

3. **List your own code.** Everything you or your agency wrote lives in your own extensions,
   `settings/override/` and `settings/siteaccess/`. Those are what you carry over; the kernel,
   `extension/ezjscore`, `extension/ezoe` and `extension/ezformtoken` come with Exponential.

   ```sh
   ls extension/ settings/override/ settings/siteaccess/
   ```

## Step 3 — Install Exponential next to the old site

Install Exponential in a **new** directory, as the installation guide describes, for example:

```sh
cd /var/www
composer create-project se7enxweb/exponential exponential
```

Do not unpack Exponential over the LoveStack directory: files that LoveStack moved (for example
the `ezenum` datatype, which LoveStack keeps in `extension/legacy_2018`) would be left in two
places. Then copy your own parts across:

```sh
OLD=/var/www/lovestack-site
NEW=/var/www/exponential
cp -a $OLD/extension/myextension     $NEW/extension/       # once per own extension
cp -a $OLD/settings/override/.        $NEW/settings/override/
cp -a $OLD/settings/siteaccess/.      $NEW/settings/siteaccess/
cp -a $OLD/var/storage                $NEW/var/            # or var/<site>/storage
```

Now run the check in the new directory, against the copied settings and the old database:

```sh
./console exp:lovestack check
```

Each line is marked `FOUND` (something to do; the line below says what), `info` (present, and
Exponential works with it as it is), `clear` (not present) or `unknown` (the engine does not
tell). It ends with `OK` (exit code 0) or `NEEDS ATTENTION` (exit code 1); `--json` gives a
machine-readable report. It writes nothing.

## Step 4 — Settings

### 4.1 Remove `legacy_2018`

LoveStack moved the `ezenum` datatype into an extension called `legacy_2018`. Exponential keeps
`ezenum` in the kernel, so the extension must not be active (two classes called `eZEnumType`
would collide):

```sh
grep -rn "legacy_2018" settings/override settings/siteaccess
```

Remove every `ActiveExtensions[]=legacy_2018` and `ActiveAccessExtensions[]=legacy_2018` line
it prints. Content using `ezenum` keeps working. Do not copy the extension directory.

### 4.2 Rename or remove LoveStack-only settings

`exp:lovestack check` lists them, with the Exponential name to use. In short:

| LoveStack setting | In Exponential |
|---|---|
| `site.ini [Session] SameSite=` | `site.ini [Session] CookieSameSite=` (default `Lax`; `None` needs `Secure`). Until you rename it, Exponential reads `SameSite` and logs a deprecation note |
| `site.ini [HTTPHeaderSettings] SetContentLengthHeader=` | remove: Velocity, FrankenPHP and the HTTP cache send `Content-Length` themselves |
| `site.ini [DebugSettings] UsedTemplateEditUrl=` | same name and meaning ([L10](../lovestack.md#l10--edit-links-in-the-list-of-used-templates)) |
| `site.ini [Cache_ezjscore] Maxage=` | remove: packed files are cleared by `./console exp:cache` |
| `image.ini [ValidUploadFormats] MIMEList[]` | `image.ini [ImageUploadSettings] AllowedMIMETypes[]` ([L05](../lovestack.md#l05--uploaded-images-are-checked-by-their-content)) |
| `error.ini [HTTPError-<code>] HeaderList[]` | same name and meaning ([L29](../lovestack.md#l29--response-headers-for-error-pages)) |
| `ezfind.ini [SearchSettings] SortBy[]` (ezjscore search) | same name and meaning ([L30](../lovestack.md#l30--the-order-of-search-results-in-the-editors-dialogs)) |
| `ezjscore.ini [eZJSCore] LocalScriptBasePath`, `[YUI3] LoaderOptions` | remove: Exponential's ezjscore ships no YUI |
| `ezjscore.ini [ezjscServer_ezjscnode] Functions[]` | same; to require the policy, also set `RequirePolicy=enabled` ([L01](../lovestack.md#l01--node-data-calls-can-require-a-policy)) |
| `menu.ini [Leftmenu_my] PolicyList_edit_profile` | remove: not read by Exponential's admin menu |
| `content.ini [CustomTagSettings] IsHidden[]` | same name and meaning ([L22](../lovestack.md#l22--hidden-custom-tags)) |
| `[Include path/*.ini]` inside an INI file | same syntax ([L28](../lovestack.md#l28--include--inside-settings-files)) |
| `ezoe.ini [EditorSettings] Plugins[]=custom_tags_controls`, `design.ini EditorCSSFileList[]=mugo_editor.css`, `EditorDialogCSSFileList[]=mugo_editor_dialog.css` (if your own settings repeat them) | remove; set `ezoe.ini [EditorSettings] CustomTagControls=enabled` instead ([L23](../lovestack.md#l23--custom-tag-controls-in-the-rich-text-editor)) |

### 4.3 Permission defaults

LoveStack requires a policy for two things that an upgraded Exponential site keeps open. To keep
LoveStack's behaviour, set both in your overrides:

- **Registration.** Add to `settings/override/site.ini.append.php`:

  ```ini
  [RoleSettings]
  PolicyOmitListExclude[]=user/register
  ```

  and give the Anonymous role *User / Register* if visitors should still register
  ([L02](../lovestack.md#l02--registration-can-require-a-policy)).
- **Node data calls.** Add to `settings/override/ezjscore.ini.append.php`:

  ```ini
  [ezjscServer_ezjscnode]
  RequirePolicy=enabled
  ```

  Roles that had *ezjscore / call* with `ezjscnode` under LoveStack keep working
  ([L01](../lovestack.md#l01--node-data-calls-can-require-a-policy)).

## Step 5 — Database

1. **Run Exponential's upgrade SQL** for your engine, in order. LoveStack's schema starts from
   the legacy kernel's 5.4 schema, so both 6.0 files apply:

   ```sh
   mysql mydatabase < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
   mysql mydatabase < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
   ```

   For PostgreSQL:

   ```sh
   psql mydatabase < update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
   psql mydatabase < update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
   ```

   The columns LoveStack added (`ezuser.password_hash` with 255 characters,
   `ezcontentobject_trash.trashed`) are the same in Exponential and are not touched.

2. **Record the database as Exponential's:**

   ```sh
   ./console exp:lovestack migrate            # dry run: prints what it would write
   ./console exp:lovestack migrate --apply
   ```

   It sets `ezsite_data` `ezpublish-version` from `lovestack` to this release's version
   (`6.0.15stable`), which Exponential's upgrade check and about page read, and records
   LoveStack's schema version as `lovestack-schema-version`. LoveStack's own `db-schema-version`
   row stays as a record. It never changes content, users or settings; a second run changes
   nothing; a database that does not come from LoveStack is refused. Below the rows it prints the
   settings and extensions still to change, from step 4.

3. **Keep what LoveStack's schema step 4 added.** The unique index on `ezuser.login` stays:
   Exponential works with it, and new Exponential installations have it too
   ([L04](../lovestack.md#l04--one-account-per-login)). The
   `ezcontentobject_attribute.data_text NOT NULL DEFAULT ''` column can stay as well:
   Exponential stores an empty string there, never NULL; the same content operations were
   tested on such a database and on an Exponential one with the same results.

## Step 6 — Clear caches and check

```sh
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
./console exp:lovestack check
```

The check should now end with `OK`. Then open the site and the administration, and check:

- the front page, a few content pages and a search;
- log in and out in the administration; edit and publish an object with an image;
- `var/log/error.log` and `var/<site>/log/error.log` stay quiet.

## Step 7 — Your own PHP code

If your extensions call a LoveStack-only API, this is the Exponential one:

| LoveStack API | Exponential API |
|---|---|
| `eZURI::build( $url, $full )` | the same, plus `$htmlEscape` ([L25](../lovestack.md#l25--ezuribuild-builds-a-site-url-in-one-call)) |
| `eZMailTransport::send( $mail, $transportType )` | the same ([L26](../lovestack.md#l26--one-message-with-another-mail-transport)) |
| `eZTemplateDesignResource::setKeyValue( $key, $value, $override )` | the same ([L31](../lovestack.md#l31--templates-can-tell-layout-views-apart)) |
| `eZSiteData::create()`, `fetchByName()`, `createNew()` | the same |
| `fetch( 'content', 'site_data', … )` in templates | `fetch( 'content', 'site_data', hash( 'name', … ) )` for names listed in `site.ini [SiteDataSettings] TemplateReadable[]` ([L14](../lovestack.md#l14--site-data-in-templates-and-in-the-administration)) |
| `eZDebug::writeDeprecated()`, `eZDebug::LEVEL_DEPRECATED` | the same ([L07](../lovestack.md#l07--php-deprecations-get-their-own-log)) |
| `{MyResolver}` in a class name pattern, `eZNamePatternResolver::instance()` | the same pattern; the class implements `expNamePatternResolverInterface` and is listed in `content.ini [NamePatternSettings] ResolverClasses[]`; Exponential keeps the constructor and adds `eZNamePatternResolver::resolve()` ([L18](../lovestack.md#l18--names-made-by-a-php-class)) |
| `eZDBInterface` integer limits | `MAX_INT`, `MIN_INT` per driver and `$db->clampInteger()` ([L34](../lovestack.md#l34--very-large-numbers-no-longer-stop-the-search-index)) |
| `rawurlencode` / `rawurldecode` template operators | the same ([L27](../lovestack.md#l27--rawurlencode-and-rawurldecode-template-operators)) |

Find calls in your code:

```sh
grep -rnE "eZURI::build|setKeyValue\(|writeDeprecated|LEVEL_DEPRECATED|MAX_INT|MIN_INT|'site_data'|rawurl(en|de)code|eZNamePatternResolver::instance" \
     extension/myextension design/mydesign --include=*.php --include=*.tpl
```

## About LoveStack

LoveStack is developed by Mugo Web and published at <https://github.com/mugoweb/ezpublish-legacy>
and on Packagist as [`mugoweb/ezpublish-legacy`](https://packagist.org/packages/mugoweb/ezpublish-legacy).
Its own upgrade notes are in its
[`update/README.md`](https://github.com/mugoweb/ezpublish-legacy/blob/e61e9fdf0ca464197b79935ef784ed459721aa87/update/README.md).
Thanks to Mugo Web, whose documented schema steps (`update/database/mysql/lovestack/1.sql` to
`4.sql`) made it possible to recognise a LoveStack database this precisely.

Moving the other way, or running both, is not covered: the two projects share their origin but
not their release lines.
