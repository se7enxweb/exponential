# 5. The setup wizard

The setup wizard installs Exponential from a browser, one page per step. It starts by itself on a fresh installation
because `settings/site.ini` ships with `[SiteAccessSettings] CheckValidity=true`; it holds the site in maintenance
mode for everyone but the browser that started it; it walks through the steps of `kernel/setup/steps/ezstep_data.php`
in order; and its last step writes the database, the site package and the settings, then switches the wizard off.
This chapter describes every page as the templates in `design/standard/templates/setup/init/` draw it: what it is
for, every field, the checks applied to the answers and the exact messages shown when an answer is refused. It also
covers how a `kickstart.ini` pre-fills or skips pages, what the wizard writes, and how to restart a wizard that
stopped half-way.

[Previous: 4. Choosing an install method](04-choosing-an-install-method.md) | [Next: 6. The kickstarter](06-kickstarter.md) |
[Contents](README.md)

---

## 5.1 How the wizard starts

`settings/site.ini` contains:

```ini
[SiteAccessSettings]
CheckValidity=true
```

On every request the web kernel (`kernel/private/classes/ezpkernelweb.php`) checks that value. While it is the string
`true`, the request is handed to the module `setup`, view `init` (the view code is
`kernel/private/classes/views/setup/ezsetup.php`, entry point `kernel/setup/ezsetup.php`), with:

- the page layout `[SetupSettings] PageLayout` (`setup_pagelayout.tpl`) and the design `[SetupSettings]
  OverrideSiteDesign` (`standard`);
- no session, no user object and no database connection;
- a temporary siteaccess named `setup`.

`CreateSites`, the last working step, writes `CheckValidity=false` into `settings/override/site.ini.append.php`. From
then on the site is served normally and the wizard can no longer be reached.

To start the wizard:

1. Install the code and its dependencies ([chapter 3](03-getting-the-code.md)) and make `settings/`, `var/` and
   `design/` writable by the user the web server runs as.
2. Serve the installation root with a web server ([chapter 8](08-serving-the-site.md)). Exponential Velocity is the
   recommended engine; for a quick local start `php bin/php/console exp:velocity start --engine=php` serves the site
   with PHP's built-in server.
3. Open the site's address in a browser. Whatever path you open, the wizard's first page appears.

Every page has the same frame (`design/standard/templates/setup_pagelayout.tpl`): the step's form in the middle, a
**Help** panel with the step's own help text (`<step>_help.tpl`), a **Summary** panel that fills in as you go
(System, Image system, Mail, Database, Language, Site), and a progress bar reading "N% completed".

### How the wizard remembers your answers

The wizard keeps no server-side session. Every answer collected so far is carried from page to page as hidden form
fields named `P_<group>-<key>` (`design/standard/templates/setup/persistence.tpl`). Two practical consequences:

- **Use HTTPS for the wizard on any machine reached over a network.** The database password and the administrator
  password travel in those hidden fields with every page.
- **Reloading the first page starts over.** A plain request (no form posted) is always the Welcome page with nothing
  remembered.

### The buttons

`design/standard/templates/setup/init/navigation.tpl` draws the buttons. Not every page shows all of them.

| Button | Shown on | Effect |
|---|---|---|
| **Next >** | every page except the error page of Create sites | checks this page's answers; on success shows the next page, otherwise this page again with the problem |
| **< Back** | every page except Welcome and the error page of Create sites | shows the previous page, skipping pages that need no input |
| **Finetune** | Welcome and System check when an optional test failed; System finetuning | shows (or re-runs) the optional tests |
| **Refresh** | Site details | shows the page again with the current values |
| **Retry** | the error page of Create sites | runs the installation step again |

## 5.2 The browser hold and maintenance mode

While the wizard runs, the site is in **maintenance mode** for everybody except the browser that started it
(`kernel/classes/expMaintenance.php`):

1. The first request of a run (a request without a posted step) calls `expMaintenance::beginWizard()`. It writes the
   marker `var/maintenance.json` with the reason `setup`, the run id of the setup log, the SHA-256 hash of a random
   token and a **lease** that ends 30 minutes later (`expMaintenance::WIZARD_LEASE = 1800` seconds). The browser gets
   the token as the cookie `exp_setup_wizard` (path `/`, HttpOnly, SameSite=Lax, Secure when the request came over
   HTTPS).
2. Every further wizard request renews the lease for another 30 minutes.
3. Every other visitor, and every request without the cookie, is answered by the front controller before any setting
   or the database is read: HTTP **503**, a `Retry-After` header, `Cache-Control: no-store`, `X-Robots-Tag: noindex`,
   and the page `share/maintenance.html` (or the first active extension's `errors/maintenance.html`) with the title
   **"The site is being set up"** and the text "It is being installed right now and will be here in a few minutes."
4. The **Finished** page ends the hold: the marker is removed and the cookie cleared.
5. A wizard that is abandoned does not keep the site offline: once the lease has run out, the marker no longer
   counts and the next visitor starts a new wizard.

The hold exists so that a second visitor cannot start a second wizard that would abandon or install over the first,
and so that nobody meets a database that is half built.

## 5.3 The pages, step by step

The wizard runs the steps of `eZStepData::$StepTable` in order. A step whose `init()` decides that nothing needs to
be asked is passed without a page; the table below says when that happens.

| # | Page title | Step class | Shown when |
|---|---|---|---|
| 1 | Welcome to Exponential | `Welcome` | always (unless `kickstart.ini` has `[welcome]` with `Continue=true`) |
| 2 | System check | `SystemCheck` | only when a critical test fails |
| 3 | System finetuning | `SystemFinetune` | only when you pressed **Finetune** |
| 4 | Outgoing Email | `EmailSettings` | always |
| 5 | Choose database system | `DatabaseChoice` | when more than one database extension is loaded |
| 6 | Database initialization | `DatabaseInit` | always |
| 7 | Language support | `LanguageOptions` | always |
| 8 | Site package | `SiteTypes` | always |
| 9 | Package language options | `PackageLanguageOptions` | when the packages hold languages you did not choose |
| 10 | Site access configuration | `SiteAccess` | always |
| 11 | Site details | `SiteDetails` | always |
| 12 | Site administrator | `SiteAdmin` | unless the database action is "Leave the data and do nothing" |
| 13 | Site security | `Security` | only when the site is not in virtual host mode and there is no `.htaccess` |
| 14 | Registration | `Registration` | always |
| — | (Creating sites) | `CreateSites` | only when the installation fails |
| 15 | Finished | `Final` | always |

### 5.3.1 Welcome

**Purpose.** Greets you, offers the language of the wizard itself, and runs the optional tests in the background.

**Screen.** The heading "Welcome to Exponential *version*". When every optional test passed, the text reads: "Welcome
to the Exponential content management system and development framework. This wizard will help you set up
Exponential. Click *Next* to continue." When an optional test failed, the text adds: "Your system is not optimal, if
you wish you can click the *Finetune* button. This will present hints on how to fix these issues. Click *Next* to
continue without finetuning.", and a **Finetune** button appears.

| Field | Form name | Values |
|---|---|---|
| Select installation language | `eZSetupWizardLanguage` | the languages the installation has locales for; the browser's preferred language is preselected |

**What it does.** Sets the translation the rest of the wizard is shown in. Nothing is written.

### 5.3.2 System check

**Purpose.** Runs the critical tests. When all pass, the page is not shown and the wizard moves on.

The critical tests are listed in `settings/site.ini [SetupSettings] CriticalTests`, their parameters in
`settings/setup.ini`:

| Test | What it checks | Parameter in `setup.ini` |
|---|---|---|
| `directory_permissions` | the web server can write the listed directories | `[directory_permissions] CheckList`: `design`, `extension`, `settings`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/admin`, `var`, `var/cache` and its subdirectories, `var/log`, `var/storage`, `var/storage/original`, `var/storage/reference`, `var/storage/variations`, `var/autoload` |
| `phpversion` | the PHP version | `[phpversion] MinimumVersion` |
| `database_extensions` | at least one database extension | `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8`, `Require=one` |
| `image_conversion` | GD or ImageMagick | `[image_conversion] TestList=imagegd_extension;imagemagick_program`, `Require=one` |
| `safe_mode`, `magic_quotes_runtime` | legacy PHP settings that must be off | |
| `memory_limit` | `memory_limit` | `[memory_limit] MinMemoryLimit=64M` (`-1` passes) |
| `execution_time` | `max_execution_time` | `[execution_time] MinExecutionTime=30` (`0` passes) |
| `allow_url_fopen` | `allow_url_fopen` is on | |
| `php_session` | the `session` extension | |
| `file_upload` | `file_uploads` is on and the upload directory is writable | |
| `zlib_extension`, `dom_extension`, `iconv_extension`, `mbstring_extension`, `intl_extension`, `xsl_extension` | the PHP extensions | |
| `timezone` | `date.timezone` is set in `php.ini` (UTC chosen there passes; UTC only as the fallback fails) | |
| `ezcversion` | the Zeta Components | `[ezcversion] TestClass=ezcBaseFile`, `MinimumVersion=2008.2` |

**Screen.** "System check", then "There are some important issues that have to be resolved. A list of issues /
problems is presented below. Each section contains a description and a suggested / recommended solution." and
"Once the problems / issues are fixed, you may click the *Next* button to continue. The system check will be run
again. If everything is okay, the setup will go to the next stage. If there are problems, the system check page will
reappear." Under **Issues**, each failed test has its own explanation (`design/standard/templates/setup/tests/
<test>_error.tpl`). The directory permissions issue, for example, is titled "Insufficient directory permissions",
says "Exponential cannot write to some important directories, without this the setup cannot finish and parts of
Exponential will fail.", lists "The affected directories are: ..." and gives ready-made **Shell commands** (and
**Alternative shell commands** for when you cannot change the owner).

| Field | Form name | Effect |
|---|---|---|
| Ignore this test (one per failed test) | `<test>_Ignore` | the test is left out of the next run. "Some issues may be ignored by checking the *Ignore this test* checkbox(es); however, this is not recommended." |

Fix the causes on the server and press **Next**; the tests run again.

> **Note.** The PHP version test compares with `setup.ini [phpversion] MinimumVersion`, which reads `8.0.0`: the
> same minimum Composer enforces (`composer.json` requires `^8.0`); see [chapter 2](02-requirements.md).

### 5.3.3 System finetuning

**Purpose.** Shows the optional tests that failed, with advice. It appears only after **Finetune** was pressed.

The optional tests are `[SetupSettings] OptionalTests`: `variables_order`, `php_magicquotes`, `curl_extension`,
`imagegd_extension`, `imagemagick_program` (looks for `convert` in `/bin`, `/sbin`, `/usr/bin`, `/usr/sbin`,
`/usr/local/bin`, `/usr/local/sbin`), `database_all_extensions` (all of `sqlite3`, `mysqli`, `pgsql`, `mongodb`),
`php_register_globals`, `texttoimage_functions` (`imagettftext`, `imagettfbbox`) and `open_basedir`.

**Screen.** "System finetuning", "There are some issues that should be resolved to get maximum performance and
features. ..." and "If you do not want to fix these issues just click *Next*." Press **Finetune** to run the tests
again after a fix, or **Next** to go on. Nothing here blocks the installation.

### 5.3.4 Outgoing Email

**Purpose.** How the site sends e-mail.

**Screen.** "Outgoing Email", "This section is used to configure how Exponential delivers its outgoing Email.", the
two options ("Direct delivery through transfer agent (must be available on the server)." and "Indirect delivery
using an SMTP relay server.") and the note "SMTP is recommended for MS Windows users."

| Field | Form name | Values |
|---|---|---|
| Email delivery | `eZSetupEmailTransport` | `1` **Sendmail/MTA** (preselected), `2` **SMTP** (on Windows SMTP is the only choice) |
| Server name | `eZSetupSMTPServer` | SMTP host |
| Username (optional) | `eZSetupSMTPUser` | |
| Password (optional) | `eZSetupSMTPPassword` | |

**Validation.** None: the values are taken as entered and not tested.

**Written by Create sites** to `settings/override/site.ini.append.php`: `[MailSettings] Transport=sendmail`, or
`Transport=SMTP` with `TransportServer`, `TransportUser` and `TransportPassword`.

### 5.3.5 Choose database system

**Purpose.** Which database system the site uses. The list holds the systems whose PHP extension the system check
found, limited to the ones the installer knows (`eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php`):

| Type | Shown as | Driver | Minimum version |
|---|---|---|---|
| `sqlite3` | SQLite (recommended) | `sqlite3` | 3.0.1 |
| `mysqli` | MySQL Improved | `ezmysqli` | 4.1.1 |
| `pgsql` | PostgreSQL | `ezpostgresql` | 8.0 |
| `mongodb` | MongoDB | `mongodb` | 4.0 |
| `oci8` | Oracle | `ezoracle` (needs the `ezoracle` extension in `extension/`) | 19.0 |

**Screen.** "Choose database system", "Support for the following database systems was detected on your system:",
"Please choose the database system you would like to use." and a radio list. The recommended system is
`setup.ini [DatabaseSettings] DefaultType` (`sqlite3`); it is listed first, preselected and labelled "(recommended)",
with the text: "SQLite is recommended: it needs no database server, and Exponential keeps the whole database in a
single file inside the installation. The other database systems listed below remain available for sites that use a
database server."

When the `sqlite3` extension is missing, the first available system is preselected instead and the page says: "SQLite
is the recommended database system for Exponential, but it cannot be used here: the PHP sqlite3 extension is not
loaded. *X* has been selected instead. To use SQLite, enable the sqlite3 extension in PHP and start the setup wizard
again."

| Field | Form name |
|---|---|
| Database | `eZSetupDatabaseType` |

**When the page is skipped.** When exactly one database extension is loaded, that system is chosen without a page.
Nothing is written.

### 5.3.6 Database initialization

**Purpose.** Where the database is and how to log in. The fields depend on the system chosen.

**Screen.** "Database initialization" and "Please input database access information in the form below."

For a database server:

| Field | Form name | Default (`setup.ini [DatabaseSettings]`) |
|---|---|---|
| Servername | `eZSetupDatabaseServer` | `DefaultServer=localhost` |
| Port | `eZSetupDatabasePort` | `DefaultPort` (empty), `DefaultPort_pgsql=5432` |
| Username | `eZSetupDatabaseUser` | `DefaultUser=root`, `DefaultUser_pgsql=postgres` |
| Password | `eZSetupDatabasePassword` | `DefaultPassword` (empty) |
| Database name | `eZSetupDatabaseName` | `DefaultName=exponential` |
| Socket (optional) | `eZSetupDatabaseSocket` | empty; "If you are using MySQL and do not know what to enter in the socket field, leave it blank" |

A setting `Default<Name>_<type>` in `setup.ini` overrides `Default<Name>` for that database type.

For SQLite there is one field, **Database file name** (`eZSetupDatabaseName`, default `sqlite.db`), and the text "SQLite
keeps the whole database in one file, in the directory var/storage/sqlite3 of this installation. It needs no server,
user or password. The file is created if it does not exist; if it already holds tables, the Site details page asks
what to do with them."

**Validation.** Pressing **Next** connects to the database with the values given:

- For MySQL, PostgreSQL and Oracle with a database name, that database is used directly (no `SHOW DATABASES`
  privilege needed). PostgreSQL tests the login against the named database, which must exist. Without a name, the
  installer lists the databases the user may reach.
- For SQLite the file name is checked before anything opens it: it must match
  `^[A-Za-z0-9][A-Za-z0-9_.-]{0,99}\.(db|db3|sqlite|sqlite3)$`, the directory `var/storage/sqlite3` must be writable
  (SQLite also writes `-wal` and `-shm` files there), and an existing file must be writable and begin with the SQLite
  header.
- PostgreSQL also needs the `digest()` function from the `pgcrypto` extension; the installer tries to create it.

The messages, quoted from `eZStepInstaller::databaseErrorInfo()`:

| Problem | Message |
|---|---|
| SQLite name not valid | "The database file name is not valid. Give a plain file name ending in .db, .db3, .sqlite or .sqlite3, made of letters, digits, dots, dashes and underscores, such as sqlite.db. The file is kept in var/storage/sqlite3." |
| SQLite directory not writable | "The directory *dir* cannot be written by the web server (user *user*). SQLite needs to create the database file there, and the -wal and -shm files it keeps next to it. Give that user write access to the directory (create it first if it does not exist), then try again." |
| SQLite file not writable | "The database file *file* exists but cannot be written by the web server (user *user*). Give that user write access to it, or choose another file name." |
| Not a SQLite file | "The file *file* exists and is not a SQLite database. Choose another file name; the setup does not overwrite it." |
| SQLite cannot be opened | "The SQLite database file *file* could not be opened. See var/log/setup.log and var/log/error.log for the reason." |
| PostgreSQL connection failed | "Could not connect to the PostgreSQL database. Please make sure that the server name, port, username and password are correct, that the database named on this page exists and that this user may connect to it. The server has to accept connections from this host (listen_addresses in postgresql.conf, and pg_hba.conf)." with a link to the PostgreSQL client authentication documentation |
| Other connection failure | "The database would not accept the connection, please review your settings and try again." |
| No database reachable | "The selected user has not got access to any databases. Change user or create a database for the user." |
| `pgcrypto` missing | "The 'digest' function is not available in your database, and Exponential cannot run without it. It comes from the PostgreSQL extension pgcrypto, which the setup could not create. Install the server's contrib package if pgcrypto is missing, then have the owner of the database or a superuser run CREATE EXTENSION pgcrypto; in it, and click Next again." |
| Server too old | "Your database version *version* does not fit the minimum requirement which is *required*. See the requirements page for more information." |

Every refused connection is also written to `var/log/setup.log` with the server, user, database and error code (never
the password). The database systems are covered in depth in [chapter 9](09-databases.md).

### 5.3.7 Language support

**Purpose.** The site's primary language and any additional languages.

**Screen.** "Language support", "Use the radio buttons to choose the default language, and the checkboxes to choose
additional languages. ...", and "The content that comes with the site is written in English (United States). It stays
in English (United States) whatever you choose here, and the site shows it wherever no translation into your
languages exists yet." A list of every language that has a locale in `share/locale` follows, each with a radio button
(primary) and a checkbox (additional); the language of the bundled content is marked "language of the bundled
content".

| Field | Form name |
|---|---|
| Default (radio) | `eZSetupDefaultLanguage` |
| Additional (checkboxes) | `eZSetupLanguages[]` |

On the first visit the browser's most preferred language with a locale is the primary, and its other accepted
languages are ticked.

**Validation** (`eZSetupValidateLanguageChoice()`), shown under "The language choice cannot be used":

| Problem | Message |
|---|---|
| no primary | "Choose a primary language." |
| unknown primary | "The primary language *X* is not a language this installation has a locale for (share/locale)." |
| unknown additional | "The additional language *X* is not a language this installation has a locale for (share/locale)." |
| primary also ticked as additional | "*X* is the primary language and cannot also be an additional language. Uncheck it, or choose another primary language." |

If no common character set can be found (only possible without Unicode support), the page shows "No Unicode support"
with the advice to choose languages that use similar characters or to configure the database for Unicode.

**Written.** A new site always uses UTF-8: `settings/override/i18n.ini.append.php` gets
`[CharacterSettings] Charset=utf-8` as soon as the page is accepted.

### 5.3.8 Site package

**Purpose.** The site package to install ([chapter 4](04-choosing-an-install-method.md#43-site-packages-what-is-installed)).

**Screen.** "Site package", "Please choose a site package you would like to test or base your site on.", the
"Remote repository URL:" being used, and one entry per site package: a radio button, a thumbnail when the package has
one, "*summary* (ver. *version*)", "Imported" or "Not imported", and its **Dependencies**, each "Imported", "Not
imported" or "Unknown". When only one site package is offered, it is preselected. Below the list: **Additional
packages** with a link to the full package archive, and **Upload package** with a file field and an **Upload** button.

| Field | Form name | Notes |
|---|---|---|
| Site package (radio) | `eZSetup_site_type` | `name` for an imported package, `name|url` for one to download |
| Upload package | `PackageBinaryFile` with **Upload** (`UploadPackageButton`) | imports an `.ezpkg` file and shows the page again |

**Validation and messages.**

| Situation | Message |
|---|---|
| Next pressed with no package chosen | "No site package chosen." |
| The package index cannot be downloaded and there is no local `index.xml` | "Retrieving remote site packages list failed. You may upload packages manually." |
| A download failed | "Download of package '*name*' failed. You may upload the package manually." |
| The package is not valid | "Invalid package" |
| A required package could not be fetched | an error naming the package |
| Upload without a file | "No package selected for upload." |
| Upload of something else | "Uploaded file is not an Exponential package" |
| A remote package downloaded | "Package '*name*' and it's dependencies have been downloaded successfully. Press 'Next' to continue." |

The list offers the remote entry of a package when it is not imported yet or the remote version is newer, and the
imported copy otherwise. Choosing a remote entry downloads the package afresh (an older imported copy is replaced),
followed by every package it requires that is missing or older than its `min-version`. The remote index is
`settings/package.ini [RepositorySettings] RemotePackagesIndexURL`.

### 5.3.9 Package language options

**Purpose.** Shown only when the chosen packages contain content in languages you did not choose (the bundled
content's own language, `eng-US`, never counts).

**Screen.** "Package language options", "The languages you have chose for site do not match languages in chosen
packages. To resolve conflict please select language mapping:", and a table **Language** / **Action** with one row per
package language. The actions are:

| Action | Effect |
|---|---|
| **Create language** (preselected) | the language is created and the content stays in it, next to your languages |
| **Map to** *a site language* | the content is relabelled as that language, without translation |
| **Skip content in this language** | the content in that language is not installed |

The note on the page: "Notice: Creating the language keeps the package content in the language it was written in,
next to the languages you chose. Mapping it relabels that content as another language without translating it."

### 5.3.10 Site access configuration

**Purpose.** How a request is matched to a siteaccess. Every installation gets three siteaccesses, `site`, `admin` and
`editor`; this page decides what tells them apart.

**Screen.** "Site access configuration", "Please choose the access method you want to use for your site. The access
method determines how the site will be accessed from within a web browser. If unsure: choose URL.", the explanation of
the three siteaccesses, and the convention for host names (`yourdomain.com`, `admin.yourdomain.com`,
`edit.yourdomain.com`).

| Field | Form name | Values |
|---|---|---|
| Access method | `eZSetup_site_access` | `url` **URL (recommended)**, `port` **Port**, `hostname` **Hostname** |

**What the choice pre-fills on the next page:**

| Method | Site | Admin | Editor |
|---|---|---|---|
| URL | the package identifier, e.g. `sevenx_multisite` | *identifier*`_admin` | `editor` |
| Port | `8080` | `8081` | `8082` |
| Hostname | *identifier*`.`*this host* | *identifier*`-admin.`*this host* | `edit.`*this host* |

Port and host name matching need matching web server configuration ([chapter 8](08-serving-the-site.md)).

### 5.3.11 Site details

**Purpose.** The site's title, address, match values, the sender of the site's optional e-mail, and the database.

**Screen.** "Site details", "This page lets you modify information about the site you have chosen to install. In
addition, it also lets you choose a database for the site.", then the fieldset "Details for site":

| Field | Form name | Notes |
|---|---|---|
| Title | `eZSetup_site_templates_title` | the site name (`[SiteSettings] SiteName`) |
| Site url | `eZSetup_site_templates_url` | the site's address (`SiteURL`, written without the scheme) |
| User path / User port / User hostname | `eZSetup_site_templates_value` | match value of `site` |
| Admin path / Admin port / Admin hostname | `eZSetup_site_templates_admin_value` | match value of `admin` |
| Editor path / Editor port / Editor hostname | `eZSetup_site_templates_editor_value` | match value of `editor`; the page explains what the editor siteaccess is |
| Organisation name | `eZSetup_site_templates_organisation_name` | who sends the optional e-mail; prefilled with the title |
| Postal address | `eZSetup_site_templates_organisation_address` | may stay empty; the e-mail preferences status page then warns |
| Database | `eZSetup_site_templates_database` | the database (server) or file name (SQLite: "A file in var/storage/sqlite3, created if it does not exist.", with the existing files offered in the field's list) |
| Action | `eZSetup_site_templates_existing_database` | shown only when the database already holds tables |

**Validation of the match values.** Path values must match `^[a-zA-Z0-9_]*$`, ports `^[0-9]*$`, host names
`^[a-zA-Z0-9.\-:]*$`. All three must be different, none may be empty, and **`admin` and `user` are reserved and cannot
be used as a match value** in the wizard. A refused value is marked with `*` and one of these warnings is shown:

- "'User path' and 'Admin path' should only contain letters ('a-zA-Z'), digits ('0-9') and underscores ('_'). The
  access values must not be named 'admin' or 'user' and each value must be unique. Please change invalid values on
  site indicated by *"
- "'User port' and 'Admin port' should only contain digits ('0-9'). Please change invalid values on site indicated
  by *"
- "'User hostname' and 'Admin hostname' should only contain letters ('a-zA-Z'), digits ('0-9'), dashes ('-'), dots
  ('.') and colons (':'). Please change invalid values on site indicated by *"

In host name mode an underscore is replaced by a dash in the value shown back to you.

**Validation of the database.** The database is connected to again. When its requirements check fails, the database
message of [section 5.3.6](#536-database-initialization) is shown on this page; when no connection can be made at
all, the wizard returns to Database initialization. When it already holds tables, the page shows the warning "The selected database was not
empty, please choose from the alternatives below." (for SQLite: "The database file *file* already holds *N* tables.")
and the **Action** drop-down:

| Option | Value | Same as `DatabaseAction=` |
|---|---|---|
| Leave the data and add new | `1` | `ignore` |
| Remove existing data | `2` | `remove` |
| Leave the data and do nothing | `3` | `skip` |
| I have chosen a new database | `4` | (shows the page again with the new database name) |

> **Warning.** "Remove existing data" drops the existing tables when the installation runs; on SQLite the file is
> emptied. Choose it only for a database that holds nothing you need, and take a backup first.

### 5.3.12 Site administrator

**Purpose.** The administrator account. Its login is always **admin**. The page is skipped when the database action
is "Leave the data and do nothing", because the existing administrator is kept.

**Screen.** "Site administrator", "This page lets you modify the administrator for your site. This ensures that your
site is secure and has proper name and email set.", then "Administrator settings":

| Field | Form name | Prefilled |
|---|---|---|
| Login | (shown, not editable) | `admin` |
| First name | `eZSetup_site_templates_first_name` | Administrator |
| Last name | `eZSetup_site_templates_last_name` | User |
| Email address | `eZSetup_site_templates_email` | |
| Password | `eZSetup_site_templates_password1` | |
| Confirm password | `eZSetup_site_templates_password2` | |

**Validation and messages** (under "Warning"):

| Problem | Message |
|---|---|
| empty first name | "You need to fill in the first name." |
| empty last name | "You need to fill in the last name." |
| empty e-mail | "You need to fill in an email address." |
| e-mail not valid (`eZMail::validate()`) | "You need to fill in a valid email address." |
| empty password | "You need to fill in a password." |
| the two passwords differ | "Your passwords do not match." |
| shorter than `site.ini [UserSettings] MinPasswordLength` (10) | "The password is too short." |

In the browser wizard you choose the password yourself and an empty one is refused. (The automatic replacement of an
empty or well-known password applies to `kickstart.ini`, [chapter 6](06-kickstarter.md#64-kickstartini-reference).)

**Written.** The administrator's e-mail becomes `[MailSettings] AdminEmail`.

### 5.3.13 Site security

**Purpose.** Advice when the site is not in virtual host mode. The page is skipped when the installation root holds an
`.htaccess` file, or when the site runs without `index.php` in its addresses (virtual host mode).

**Screen.** "Site security", "Your site is not running in a virtual host mode, this is insecure. It is recommended to
run Exponential in virtual host mode. If you do not have the possibility to use virtual host mode, you should follow
the instructions below about how to install an .htaccess file. The .htaccess file tells the web server to restrict
the access to certain files.", followed by the commands to run:

```bash
cd /path/to/the/installation
cp .htaccess_root .htaccess
```

and "If you do not have shell access, you will have to copy the file using an FTP client or ask your hosting provider
to do this for you." Nothing is written by this page. Web server configuration is in
[chapter 8](08-serving-the-site.md) and hardening in [chapter 13](13-security-hardening.md).

### 5.3.14 Registration

**Purpose.** A page about the community. The heading is "Open source software is nothing without a vibrant
community!", followed by links to share.exponential.earth (its blogs, articles and forums). There is no form on the
page: pressing **Next** sends nothing.

### 5.3.15 Creating sites

This step has no page of its own. Pressing **Next** on Registration runs it: it may take a few minutes, and with a
large site package considerably longer. It:

1. connects to the database and, depending on the action, empties it, then installs `share/db_schema.dba` and
   `share/db_data.dba`;
2. installs the site package and the packages it requires, and runs the package's install scripts;
3. sets up the administrator account;
4. writes `settings/siteaccess/site/`, `settings/siteaccess/admin/` and `settings/siteaccess/editor/`, and the
   files in `settings/override/` ([chapter 4](04-choosing-an-install-method.md#44-what-an-installation-writes)),
   including `CheckValidity=false`.

When something fails, the page "Creating sites" appears: "The setup wizard was not able to complete the creation of
your selected sites.", "The following errors were detected:" with a list of coded errors, and "If you think you have
fixed the errors you can try then click the "Retry" button." Only **Retry** is offered.

| Code | Meaning |
|---|---|
| `EZSW-001` | "Failed loading database schema file share/db_schema.dba" |
| `EZSW-002` | "Failed loading database data file share/db_data.dba", or a datatype's own data could not be imported |
| `EZSW-003` | "Failed loading *database* schema handler" |
| `EZSW-004` | "Failed inserting data to *database*" with the database's message |
| `EZSW-005` | "Failed connecting to database *name*" |
| `EZSW-020` to `EZSW-025` | the administrator user could not be fetched, given a new version or published |
| `EZSW-040` | "Failed to initialize site package '*name*'" |
| `EZSW-041` | "Could not fetch site package: '*name*'" |
| `EZSW-050`, `EZSW-051` | a required package could not be fetched or installed |
| `EZSW-070` | a user preference could not be created |
| `EZSW-080` | "The post-install of site package '*name*' stopped at step *N* of *M* (*function*): the steps after it did not run" |
| `EZSW-081` | "The editor siteaccess could not be made from settings/siteaccess/admin" |

`var/log/setup.log` holds the details of the failure, with a hint beside known problems.

### 5.3.16 Finished

**Purpose.** Shows where the new site is. Reaching this page ends the setup run in `var/log/setup.log` and the
maintenance hold.

**Screen.** "Finished", "Exponential has been installed with your select site setup. You will find the username
mentioned in the details below.", a note that the first visit of the user or admin site takes some time (30 to 60
seconds) while Exponential prepares the site, any text the site package adds, and **Site details**: Title, User site,
Admin site, Editor site (each a link) and Username `admin`.

## 5.4 Watching a run: the setup log

The wizard writes one record per run to `var/log/setup.log` (`kernel/classes/expsetuplog.php`). Because the wizard is
one request per page, the first request starts the run and the later ones resume it from `var/log/setup-run.state`.
For every step the log records `>> Step` and `<< Step` lines with the result (for example "accepted", "rejected
(asked again)", "shown"), the errors and warnings the step caused, a hint beside known problems, and after the
installation a set of health checks with a closing RESULT and NEXT line. Passwords are masked. Earlier runs are kept
as `setup.log.1`, `setup.log.2` and so on.

```bash
tail -f var/log/setup.log          # follow the wizard from a shell
```

## 5.5 Language of the wizard, languages of the site

Two different language choices appear in the wizard:

- **The wizard's own language** (Welcome page) only changes the translation the pages are shown in.
- **The site's languages** (Language support page) decide the content languages and the locale of the siteaccesses:
  `[RegionalSettings] Locale`, `ContentObjectLocale` and `SiteLanguageList[]`, and `[ContentSettings]
  TranslationList` for the additional languages. The bundled content stays in `eng-US` and becomes the fallback at the
  end of `SiteLanguageList`.

More: [Translations and languages](../features/6.0/translations-and-languages.md).

## 5.6 How kickstart.ini pre-fills and skips pages

The browser wizard reads `kickstart.ini` in the installation root, through `eZINI::instance( 'kickstart.ini', '.' )`
in the constructor of every step. For each step:

- **No section for the step:** the page is shown as described above.
- **A section with `Continue=false`, or without `Continue`:** the page is shown, pre-filled with the section's values.
- **A section with `Continue=true`:** the step takes the values and the wizard moves on without showing the page.

The sections are named after the steps' identifiers: `welcome`, `email_settings`, `database_choice`,
`database_init`, `language_options`, `site_types`, `site_access`, `site_details`, `site_admin`, `security`,
`registration`. The keys are the subject of [chapter 6](06-kickstarter.md#64-kickstartini-reference). The System
check, System finetuning, Package language options, Create sites and Finished steps have no section.

The values of `kickstart.ini` are used the first time a step is reached. When you go **Back** to a step, the page
shows what you entered instead, so you can correct a value that came from the file.

Two cautions:

- `[registration]` sends nothing unless it says `Send=true`, and even then the report goes only to
  `settings/setup.ini [RegistrationSettings] Receiver`, which is empty by default. The old upstream registration
  address is never used.
- The settings cache keeps a compiled copy of `kickstart.ini` in `var/cache/ini/kickstart-*.php`, and this
  installation's `config.php` switches off the INI modification-time check. After editing `kickstart.ini` by hand
  for the browser wizard, delete those files. (The kickstarter deletes them itself.)

To answer every page yourself, move `kickstart.ini` away before starting the wizard.

## 5.7 Restarting a wizard that stopped

A wizard can stop half-way: a browser closed, a server restarted, a step that keeps failing. What to do depends on how
far it got.

**The installation did not run yet** (you never pressed Next on Registration, or Creating sites failed):

1. Open the site address in the browser that started the wizard. A plain request always shows the Welcome page; the
   answers of the earlier attempt are gone, because they lived in the page.
2. If another browser (or the same browser without its cookie) gets "The site is being set up", either wait until 30
   minutes after the last wizard request, or end the hold from a shell:

   ```bash
   php bin/php/maintenance.php status
   php bin/php/maintenance.php off          # or: php bin/php/console exp:maintenance off
   ```

3. If Creating sites failed, read `var/log/setup.log`, fix the cause, and press **Retry**, or start over at Welcome.
   With a database server, choose **Remove existing data** on Site details so the half-written tables are replaced.

**The installation ran, but you want to run the wizard again** (`CheckValidity` is already `false`):

1. Take a backup of the database and of `settings/override/` and `settings/siteaccess/`.
2. Set `CheckValidity=true` in `settings/override/site.ini.append.php`:

   ```bash
   php bin/php/console exp:ini where site.ini/SiteAccessSettings/CheckValidity
   ```

   shows where the value comes from; edit that file and set `CheckValidity=true`.
3. Clear the INI cache, so the change is read: `php bin/php/ezcache.php --clear-tag=ini`. If Velocity serves the site,
   restart it ([chapter 8](08-serving-the-site.md)).
4. Open the site. The wizard starts at Welcome. On Site details choose what to do with the existing data.

**The site shows only the maintenance page after a failed command-line install**: the kickstarter leaves maintenance on
after a failure. Fix the cause and run it again, or `php bin/php/maintenance.php off`.

---

## References

In this repository:

- [Installing Exponential 6.0](../INSTALL.md), section 5
- [The setup wizard's new look, and the editor siteaccess](../features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Clean install defaults](../features/6.0/clean-install-defaults.md)
- [Maintenance mode](../features/6.0/maintenance-mode.md)
- [Installer logs and seed data](../specifications/6.0/installer-logs-and-seed-data.md)
- [SQLite database](../features/6.0/sqlite-database.md), [SQLite install](../features/6.0/platform-sqlite-install.md),
  [database drivers](../specifications/6.0/database-drivers-sqlite-oracle.md)
- [Translations and languages](../features/6.0/translations-and-languages.md)
- [The console: `exp:ini`](../bc/6.0/console-exp-ini.md)
- [Behaviour changes, 16 to 30 September 2026](../bc/6.0/behaviour-changes-2026-09b.md)
- Code: `kernel/setup/steps/`, `kernel/setup/ezsetupcommon.php`, `kernel/setup/ezsetuptests.php`,
  `kernel/private/classes/views/setup/ezsetup.php`, `kernel/classes/expmaintenance.php`,
  `kernel/classes/expsetuplog.php`; templates in `design/standard/templates/setup/init/` and
  `design/standard/templates/setup/tests/`; settings `settings/setup.ini`, `settings/site.ini [SetupSettings]`,
  `settings/package.ini [RepositorySettings]`

External:

- PHP runtime configuration (`memory_limit`, `max_execution_time`, `file_uploads`, `date.timezone`):
  <https://www.php.net/manual/en/ini.list.php>
- PHP SQLite3: <https://www.php.net/manual/en/book.sqlite3.php>
- PostgreSQL client authentication: <https://www.postgresql.org/docs/current/client-authentication.html>
- PostgreSQL pgcrypto: <https://www.postgresql.org/docs/current/pgcrypto.html>
- HTTP 503 and `Retry-After` (RFC 9110): <https://www.rfc-editor.org/rfc/rfc9110#name-503-service-unavailable>
- Exponential on GitHub: <https://github.com/se7enxweb/exponential>
