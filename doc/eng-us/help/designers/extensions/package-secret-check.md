---
title: "The secret check before an extension is packaged"
audience: designer, developer
category: theme-extensions
order: 370
summary: Why an export of your theme can be refused for holding secret values or key material, what the check looks for, how to fix a real finding, and how package_check.ini names a documentation example the check cannot tell from a real value.
keywords: secret check, packRefusal, package_check.ini, AllowedFindings, secrets, password, API key, private key, key material, extension export
type: help
---
# The secret check before an extension is packaged

This page is for designers whose theme export was refused with a list of findings. Since Exponential 6.0.15 no
package takes an extension that holds secret values or key material: **Setup > Extensions**, the package wizard and
`ezpm.php add` all ask the same check (`eZExtensionPackageHandler::packRefusal()`). The page names the file and the
setting or kind of secret, never the value.

## What is refused

| Finding | Meaning |
|---|---|
| `<file>: <Setting>` | A setting whose name marks a secret (`Password`, `ApiKey`, `ClientSecret`, `Token` ...) has a value, or another setting carries a password inside an address |
| `<file>: (private key)` | A private key block in any file. Always refused |
| `<file>: (bearer token)`, `(API key)`, `(constant)`, `(address with a password)` | The shape of a secret in any text |
| `<file>: key material` | A key file by its name: `*.key`, `*.p12`, `.env`, `id_rsa`, `credentials*` and others. Always refused |
| `<file>: copy of a settings file` | A backup such as `*.bak`, `*.orig`, `*~`. Always refused: delete the copy |

Settings files are read as text, commented lines included. A `node_modules` directory is neither packed nor read.

## Step by step: a real finding

1. Read the finding: file and setting.
2. Remove the value from the theme. A theme never needs a real password or key; the installation supplies them in
   its own `settings/override/`.
3. If the value was ever committed and pushed, consider it public: have it changed where it is used.
4. Document in the README which setting the installation must fill.
5. Export again.

## Step by step: an example the check cannot tell apart

A documentation comment with a key-shaped example value, for instance:

1. Create `package_check.ini` at the root of the theme:

   ```ini
   [PackageCheck]
   # The example key in the documentation comment
   AllowedFindings[]=settings/mytheme.ini.append.php:ApiKey
   ```

2. One finding per line, written as the refusal names it: `<path below the extension>:<setting name or (kind)>`. A
   path ending in `/` covers that directory.
3. Export again.

It only leaves out what it names. Key files, a private key block, a copy of a settings file and a link are always
refused. Placeholders (`...`, `<your key>`, `%password%`, `enabled`, `none`) pass without any entry. An active setting
is taken at its word: `Password=changeme` is refused.

## Related pages

- [The secret check of extension packages](../../../bc/6.0/extension-package-secret-check.md)
- [A .gitignore for themes](gitignore-for-themes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
