---
title: Die auf Ihrer Website installierten Erweiterungen finden
audience: user, developer, designer, app-creator, admin
category: extension-development
order: 70
summary: Setup > Extensions, Info > Über und die Kommandozeile - was installiert ist und welche Erweiterung hinter einer Seite, einem Template, einer Einstellung oder einem Operator steht.
keywords: installierte Erweiterungen, Extensions, Über, welche Erweiterung, Design-Erweiterungen, Modul-Repositories, installed extensions, setup extensions, about, ezinfo, extension.xml, exp:ini scopes, extension_counts, which extension, design extensions, module repositories
type: help
---
# Die auf Ihrer Website installierten Erweiterungen finden

Teil 1, Kapitel 7. Bevor Sie anderswo nach Ideen suchen, sehen Sie sich Ihre eigene Installation an: Sie bringt
wahrscheinlich schon Dutzende Erweiterungen mit, einige davon ausgeschaltet. Dieses Kapitel zeigt drei Wege, sie
aufzulisten, und wie Sie herausfinden, welche Erweiterung hinter einer Seite, einem Template oder einer Einstellung
steht.

## Weg 1: Setup > Extensions

Öffnen Sie **Setup > Extensions** (`/setup/extensions`). Die Seite hat eine einzige Liste: zuerst die aktiven
Erweiterungen, in Ladereihenfolge und nummeriert, dann die inaktiven nach Namen. Jede Karte zeigt Name, Version,
Beschreibung, Lizenz und Website (aus `extension.xml`, sonst aus `ezinfo.php`), ob der Ordner ein Git-Checkout ist,
die deklarierten Abhängigkeiten (**Benötigt**, **Verwendet**, **Erweitert**) und **Benötigt von**. **Details** fügt den
Autor, die Designs, die Siteaccesses verwenden, die letzte Änderung und Downloads hinzu.

Der Filter über der Liste (**Alle**, **Aktiv**, **Inaktiv**, **Probleme**) und die Sortierung werden in der Adresse
festgehalten, sodass `/setup/extensions/(show)/active` ein Lesezeichen für die aktiven ist. Alles auf der Seite wird in
[Die Extensions-Seite](../../../anleitungen/extensions-seite.md) erklärt.

> **Tipp**
> **Inaktiv** ist der Ort, an dem sich Ideen verstecken. Eine Erweiterung, die auf der Festplatte liegt, aber
> ausgeschaltet ist, wurde meist aus einem Grund installiert; lesen Sie ihre Beschreibung, bevor Sie dasselbe noch
> einmal bauen.

## Weg 2: Info > Über

**Info > Über** (`/ezinfo/about`) listet die im aktuellen Siteaccess aktiven Erweiterungen in der Reihenfolge auf, in
der der Kernel sie lädt, mit der Version und der Lizenz, die jede angibt. Die Zahlen auf dem Dashboard, auf „Über“ und
auf dem Update-Dashboard stammen alle aus einer Funktion, `eZExtension::extensionCounts()`, und jede Seite sagt, was
sie zählt (hier aktiv, irgendwo aktiv, im Dateisystem).

## Weg 3: die Kommandozeile

```bash
ls extension/
./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
./console exp:ini scopes extension --allow-root-user
```

Der letzte Befehl listet einen Geltungsbereich pro Erweiterung auf und kennzeichnet die inaktiven, zum Beispiel
`extension:autonotifications   extension   extension/autonotifications/settings  (inactive extension)`.

## Welche Erweiterung steht dahinter?

### Eine Seite (Modul)

Die Adresse `/<module>/<view>` nennt ein Modul. Erweiterungen geben mit `ExtensionRepositories[]` in
`[ModuleSettings]` von `module.ini` an, wo ihre Module liegen:

```bash
./console exp:ini where module.ini/ModuleSettings/ExtensionRepositories --allow-root-user
```

Jede Zeile nennt die Erweiterung, die ein Modul-Repository hinzugefügt hat. Danach zeigt `ls extension/<ext>/modules/`
ihre Module.

### Ein Template

Schalten Sie `ShowTemplatePathComments` ein (siehe [Ihre erste Stunde als Entwickler](ihre-erste-stunde-als-entwickler.md));
ein Pfad unter `extension/<ext>/design/...` nennt die Erweiterung. Die Liste der Erweiterungen, deren Designs
durchsucht werden:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions --allow-root-user
```

### Eine Einstellung

```bash
./console exp:ini where <file>/<Block>/<Variable> --allow-root-user
```

listet jede Datei auf, die sie setzt; Dateien unter `extension/<ext>/settings/` nennen die Erweiterung.

### Ein Template-Operator

Erweiterungen registrieren Template-Operatoren über `ExtensionAutoloadPath[]` in `[TemplateSettings]` von `site.ini`,
das das Template-System auf ihre `autoloads/eztemplateautoload.php` verweist:

```bash
./console exp:ini where site.ini/TemplateSettings/ExtensionAutoloadPath --allow-root-user
grep -l "'my_operator'" extension/*/autoloads/eztemplateautoload.php
```

### Ein Datentyp oder ein Workflow-Ereignis

```bash
./console exp:ini where content.ini/DataTypeSettings/ExtensionDirectories --allow-root-user
./console exp:ini where workflow.ini/EventSettings/ExtensionDirectories --allow-root-user
```

## Die eigenen Dateien einer Erweiterung lesen

Sobald Sie die Erweiterung kennen, beantwortet ihr Ordner den Rest:

| Datei | Sagt Ihnen |
|---|---|
| `extension.xml` | Name, Version, Lizenz, Website, Abhängigkeiten |
| `ezinfo.php` | Dasselbe bei älteren Erweiterungen, dazu Copyright-Zeilen, die frühere Autoren nennen |
| `README.md`, `doc/` | Was sie tut, wie man sie installiert, ihre Einstellungen |
| `composer.json` | Den Paketnamen auf Packagist, die PHP-Anforderung, Lizenz, Autoren |
| `LICENSE`, `LICENSE.md`, `doc/LICENSE` | Den Lizenztext, den Sie beachten müssen, wenn Sie den Code wiederverwenden |
| `settings/*.ini.append.php` | Jede Einstellung, die sie hinzufügt oder ändert, meist mit Kommentaren |
| `git -C extension/<ext> log --oneline` | Ihre Historie, wenn sie ein Klon ist |

Viele Erweiterungen, die mit der Distribution ausgeliefert werden, haben auch eine Seite in der Dokumentation:
[der Erweiterungskatalog](../../../../eng-us/features/6.0/extensions/README.md) und
[was eine neue Installation mitbringt](../../../../eng-us/features/6.0/default-extension-distribution.md).

## Aus Templates

Ein Template kann dieselben Zahlen lesen, die das Dashboard zeigt:

```text
{def $counts = fetch( 'setup', 'extension_counts', hash( 'all_siteaccesses', true() ) )}
```

Ohne `all_siteaccesses` werden die Arten weggelassen, die die Einstellungen jedes Siteaccess lesen, was es schnell
hält; siehe [Die Extensions-Seite](../../../anleitungen/extensions-seite.md).

## Übung

1. Öffnen Sie `/setup/extensions/(show)/inactive` und wählen Sie eine inaktive Erweiterung.
2. Lesen Sie ihre `README.md` oder `extension.xml`. Was fügt sie hinzu: ein Modul, einen Datentyp, Operatoren, ein
   Design?
3. Finden Sie eine Einstellung, die sie mitbringt: `ls extension/<ext>/settings/`.

Jetzt kennen Sie einen weiteren Baustein, der schon auf Ihrer Festplatte liegt.

## Verwandte Seiten

- Zurück: [Den Code lesen, den Sie schon haben](den-vorhandenen-code-lesen.md)
- Weiter: [Der Erweiterungskatalog von se7enxweb](der-se7enxweb-erweiterungskatalog.md)
- [Die Extensions-Seite](../../../anleitungen/extensions-seite.md)
- [Erweiterungsliste: sortieren, untersuchen und herunterladen](../../../../eng-us/features/6.0/extension-list-and-downloads.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
