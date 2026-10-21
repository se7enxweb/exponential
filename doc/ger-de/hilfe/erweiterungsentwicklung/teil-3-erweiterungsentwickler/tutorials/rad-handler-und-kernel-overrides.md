---
title: "Mit RAD: Handler und Kernel-Overrides"
audience: developer, app-creator
category: extension-development
order: 80
summary: Der Handler-Assistent schreibt einen Mail-Transport, der über eine Einstellung gewählt wird; der Assistent für Kernel-Overrides schreibt eine Kopie mit ihrer Herkunft und einer Abweichungsprüfung. Beides anhand echter Ausgabe.
keywords: RAD, Handler-Assistent, Mail-Transport, Handler, Kernel-Override, Abweichungsprüfung, Herkunft, handler wizard, setup/handlerextension, mail transport, TransportAlias, handler, kernel override wizard, setup/kerneloverride, kernel_override, checkdrift, md5, provenance
type: help
---
# Mit RAD: Handler und Kernel-Overrides

Tutorial 8. Zwei Assistenten ändern das Verhalten des Systems über Ihren eigenen Code hinaus: Der **Handler-Assistent**
(`/setup/handlerextension/<kind>`) schreibt eine Klasse, die das System statt seiner Voreinstellung verwendet, gewählt
über eine einzige Einstellung, und der **Assistent für Kernel-Overrides** (`/setup/kerneloverride`) schreibt das letzte
Mittel, einen Ersatz für eine Kernel-Klasse. Die Ausgabe unten wurde von den Generatoren der Assistenten erzeugt
(`expHandlerWizard::files()`, `expKernelOverrideWizard::files()`); jede PHP-Datei hat `php -l` bestanden.

## Teil A: ein Mail-Transport

### 1. Das Formular

Öffnen Sie `/setup/handlerextension/mail`. Die Seite erklärt die Art anhand des Rezepts des Assistenten: „Wie E-Mails
das System verlassen. Standardmäßig wird E-Mail an PHP übergeben, das sie an den Rechner übergibt. Ein eigener Transport
kann sie über eine API versenden, in eine Warteschlange stellen oder auf einem Rechner, der nichts versenden darf, auf
die Festplatte schreiben.“ Sie nennt die Basisklasse (`eZMailTransport`, `lib/ezutils/classes/ezmailtransport.php`) und
die Einstellung (`site.ini [MailSettings] TransportAlias`).

| Feld | Wert |
|---|---|
| Name der Extension | `mysite_mail` |
| Alias | `mysitelog` |
| Klassenname | `mysiteLogMailTransport` |
| Titel | `Log mail transport` |

### 2. Die Dateien

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

### 3. Schreiben, dann auswählen

Ersetzen Sie den Rumpf durch den protokollierenden Transport aus
[Handler, die über Einstellungen benannt werden](../handler-ueber-einstellungen-benennen.md).
Der Rezepthinweis des Assistenten ist wichtig: „Setzen Sie Transport im selben Abschnitt auf den Alias, sonst wird
weiterhin der Standard verwendet.“ Setzen Sie ihn nur auf dem Entwicklungsserver:

```bash
./console exp:ini set site.ini/MailSettings/Transport mysitelog global
```

> **Warnung**
> So wie erzeugt gibt `sendMail()` `true` zurück, ohne etwas zu senden. Sobald `Transport` den Alias nennt, wird jede
> E-Mail stillschweigend verworfen, bis Sie die Methode schreiben. Schreiben Sie sie, bevor Sie sie auswählen.

## Teil B: ein Kernel-Override

### 1. Das Formular

Öffnen Sie `/setup/kerneloverride`. Nennen Sie die zu ersetzende Kernel-Klasse und, verpflichtend, den **Grund**: warum
nichts Leichteres funktioniert. Der Assistent lehnt eine Klasse ab, die bereits von etwas anderem überschrieben wird
(„Zwei Overrides einer Klasse lassen sich nicht kombinieren“).

### 2. Die Dateien

Für `eZSection`:

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

Die Kopie beginnt mit einem Kopfbereich, der ihre Herkunft festhält:

```text
 * ##  KERNEL OVERRIDE - this file replaces a kernel class                  ##
 *
 * eZSection, copied from
 *
 *     kernel/classes/ezsection.php
 *     md5 <checksum of the kernel file when copied>
 *     copied <date>
```

gefolgt vom Grund und von „What you have taken on“ (was Sie damit übernommen haben): „This is not a subclass. There is
no parent to call and nothing is inherited“ (dies ist keine Unterklasse; es gibt keine Elternklasse zum Aufrufen, und
nichts wird geerbt). `bin/checkdrift.php` vergleicht nach jedem Upgrade die Kernel-Datei mit dieser Prüfsumme.

### 3. Einschalten

Die erzeugte README führt die Schritte auf: die Erweiterung aktivieren; Overrides in `config.php` mit
`define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );` erlauben; die Override-Zuordnung mit
`php bin/php/ezpgenerateautoloads.php -o` erzeugen; die Caches leeren. Führen Sie dann bei jedem Deployment die
Abweichungsprüfung aus.

Lesen Sie
[Kernel-Klassen-Overrides: das letzte Mittel](../kernel-overrides-der-letzte-ausweg.md),
bevor Sie das verwenden.

## Verwandte Seiten

- Zurück: [Mit RAD: eine Erweiterung nur mit Einstellungen](rad-einstellungs-erweiterung.md)
- Weiter: [Von Hand: die kleinste Erweiterung in fünf Minuten](von-hand-minimale-erweiterung.md)
- [Handler, die über Einstellungen benannt werden](../handler-ueber-einstellungen-benennen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
