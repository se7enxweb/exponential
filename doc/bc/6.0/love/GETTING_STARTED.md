# Getting started

This page is for everyone, whatever you know about Exponential or LoveStack.

## What is this about?

The legacy kernel originally published by eZ Systems has more than one continuation today. Two of
them are **Exponential** (this project) and **LoveStack** (Mugo Web's distribution,
`mugoweb/ezpublish-legacy`). LoveStack added a number of valuable features and fixes over the
years. Exponential 6.0.15 brings 36 of them into the Exponential distribution, rebuilt in its own
code and tested, so that every Exponential site can use them together with Exponential's own
features. The [overview](../lovestack.md) describes each one.

## Does this concern me?

Run this in your installation's root directory:

```sh
./console exp:lovestack check
```

On an installation that has not got the console yet, a quick look tells the same:

```sh
grep -l -i "lovestack" lib/version.php composer.json composer.lock 2>/dev/null
```

| You are | Read |
|---|---|
| on Exponential (or another descendant of the legacy kernel), never on LoveStack | nothing more is needed. Read [What changes on my Exponential site?](#what-changes-on-my-exponential-site) for the features you may want to switch on |
| on LoveStack, or moving a LoveStack database | [Moving a site from LoveStack](MIGRATION.md) |
| a developer who used a LoveStack-only method | [API.md](API.md) and [step 7 of the migration](MIGRATION.md#step-7--your-own-php-code) |

## What changes on my Exponential site?

After the upgrade to 6.0.15:

- **plain bug fixes and safety checks** are on for every site: logging out ends the session and
  its cookie (L03), uploaded images and package thumbnails are checked by their content (L05,
  L06), and a dozen smaller fixes;
- **a few small visible changes**: PHP deprecations go to `var/log/deprecated.log` (L07), log
  files are rotated at 2 MB (L08), a template warning for operator parameters without a default
  (L09), a Copy button on the admin full view (L15);
- **everything that could change what visitors or editors see is off** until you switch it on:
  the policy for node data calls (L01), closed registration (L02), the unique login index (L04),
  location audit records (L13), required alternative text (L16), trimmed text fields (L17),
  custom tag controls (L23), the URL options (L32, L33) and the rest.

A **new installation** gets the safer setting where the two differ: L01, L02, L04 and L23 are on.

[FEATURES.md](FEATURES.md) lists the default of every feature, and the
[overview](../lovestack.md#feature-by-feature) says how to switch each one on.

## Who is each feature for?

| You are | Look at |
|---|---|
| an **editor** | the Copy button (L15), required alternative text (L16), trimmed text fields (L17), the Locations window fix (L20), hidden custom tags and custom tag controls (L22, L23) |
| an **administrator** | the policies for node data and registration (L01, L02), unique logins (L04), image checks (L05, L06), logs and deprecations (L07, L08, L12), location audit records (L13), site data (L14), error page headers (L29), the URL options (L32, L33) |
| a **developer** or **designer** | the template warning and edit links (L09, L10), database error numbers (L11), names made by PHP (L18), class filter default (L19), `eZURI::build()` (L25), per-message mail transport (L26), `rawurlencode` (L27), settings includes (L28), the `layout` key (L31) |

## Glossary

| Word | Meaning |
|---|---|
| LoveStack | Mugo Web's distribution of the legacy kernel, `mugoweb/ezpublish-legacy` |
| Exponential | this CMS, the continuation of the legacy kernel maintained by 7x, `se7enxweb/exponential` |
| L01 … L36 | the numbers of the features brought over, used on every page of this documentation |
| Opt-in | a feature that is off until you enable it in a setting |
| Upgrade default | what an existing installation gets after the upgrade; new installations may get a different, safer value |
| INI override | your settings in `settings/override/*.ini.append.php` or a siteaccess, which win over the shipped `settings/*.ini` |
