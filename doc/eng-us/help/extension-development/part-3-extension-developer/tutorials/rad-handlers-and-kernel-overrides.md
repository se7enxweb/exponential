---
title: "With RAD: handlers and kernel overrides"
audience: developer, app-creator
category: extension-development
order: 80
summary: The Handler wizard writes a mail transport chosen by a setting; the Kernel override wizard writes a copy with its provenance and a drift check. Both from real output.
keywords: RAD, handler wizard, setup/handlerextension, mail transport, TransportAlias, handler, kernel override wizard, setup/kerneloverride, kernel_override, checkdrift, md5, provenance
type: help
---
# With RAD: handlers and kernel overrides

Tutorial 8. Two wizards change system behavior beyond your own code: the **Handler extension** wizard
(`/setup/handlerextension/<kind>`) writes a class the system uses in place of its default, chosen by one setting, and
the **Kernel override** wizard (`/setup/kerneloverride`) writes the last resort, a replacement of a kernel class. The
output below was produced by the wizards' generators (`expHandlerWizard::files()`, `expKernelOverrideWizard::files()`);
every PHP file passed `php -l`.

## Part A: a mail transport

### 1. The form

Open `/setup/handlerextension/mail`. The page explains the kind from the wizard's recipe: "How mail leaves the
system. The default hands mail to php, which hands it to the machine. A transport of your own can send it through an
api, queue it, or write it to disk on a machine that must not send anything." It names the base class
(`eZMailTransport`, `lib/ezutils/classes/ezmailtransport.php`) and the setting (`site.ini [MailSettings] TransportAlias`).

| Field | Value |
|---|---|
| Extension name | `mysite_mail` |
| Alias | `mysitelog` |
| Class name | `mysiteLogMailTransport` |
| Title | `Log mail transport` |

### 2. The files

```text
.gitignore
LICENSE
README.md
classes/mysitelogmailtransport.php
composer.json
doc/examples.php
extension.xml
ezinfo.php
settings/site.ini.append.php
```

```ini
[MailSettings]
TransportAlias[mysitelog]=mysiteLogMailTransport
```

```php
class mysiteLogMailTransport extends eZMailTransport
{
    /**
     * Sends one mail and says whether it went. Returning false is how a
     * failure is reported; nothing else is read.
     */
    public function sendMail( eZMail $mail )
    {
        // Not written yet. Returning true keeps the system
        // working the way it did before this handler was switched on.
        return true;
    }
}
```

### 3. Write it, then select it

Replace the body with the logging transport of [Handlers named by settings](../handlers-named-by-settings.md). The
wizard's recipe note matters: "Set Transport in the same section to the alias, or the default is still used." Set it on
the development server only:

```bash
./console exp:ini set site.ini/MailSettings/Transport mysitelog global
```

> **Warning**
> As generated, `sendMail()` returns `true` without sending anything. Once `Transport` names the alias, every mail
> is silently dropped until you write the method. Write it before you select it.

## Part B: a kernel override

### 1. The form

Open `/setup/kerneloverride`. Name the kernel class to replace and, required, the **reason**: why nothing lighter
works. The wizard refuses a class that something already overrides ("Two overrides of one class do not combine").

### 2. The files

For `eZSection`:

```text
.gitignore
LICENSE
README.md
bin/checkdrift.php
composer.json
extension.xml
ezinfo.php
kernel_override/ezsection.php
```

The copy starts with a header that records its provenance:

```text
 * ##  KERNEL OVERRIDE - this file replaces a kernel class                  ##
 *
 * eZSection, copied from
 *
 *     kernel/classes/ezsection.php
 *     md5 <checksum of the kernel file when copied>
 *     copied <date>
```

followed by the reason and "What you have taken on": "This is not a subclass. There is no parent to call and nothing
is inherited". `bin/checkdrift.php` compares the kernel file with that checksum after every upgrade.

### 3. Switching it on

The generated README lists the steps: activate the extension; allow overrides in `config.php` with
`define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );`; generate the override map with
`php bin/php/ezpgenerateautoloads.php -o`; clear the caches. Then run the drift check in every deployment.

Read [Kernel class overrides: the last resort](../kernel-overrides-the-last-resort.md) before you use this.

## Related pages

- Previous: [With RAD: a settings-only extension](rad-settings-extension.md)
- Next: [By hand: the smallest extension in five minutes](by-hand-minimal-extension.md)
- [Handlers named by settings](../handlers-named-by-settings.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
