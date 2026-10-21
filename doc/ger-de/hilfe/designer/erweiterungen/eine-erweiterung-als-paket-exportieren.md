---
title: "Eine Erweiterung als Paket exportieren"
audience: designer, admin
category: theme-extensions
order: 360
summary: Die Dateien einer Theme-Erweiterung mit dem Assistenten Extension-Export oder mit ezpm in ein Exponential-Paket packen - das Exportieren in package.ini [ExtensionExportSettings] einschalten, die Geheimnisprüfung und die Schritte.
keywords: Extension-Export, ezextension, package.ini, ExtensionExportSettings, ezpm add, Erweiterungspaket, Geheimnisprüfung, Export, extension export, extension package, secret check
type: help
---
# Eine Erweiterung als Paket exportieren

Diese Seite ist für Designer, die jemandem ein Theme als eine einzige `.ezpkg`-Datei übergeben wollen, zum Beispiel an
eine Installation, die Composer nicht verwendet.

## Bevor Sie beginnen: Das Exportieren ist standardmäßig aus

`settings/package.ini [ExtensionExportSettings]` sagt: Das Exportieren einer Erweiterung als Paket (Setup > Extensions,
der Paket-Assistent, ezpm.php add) ist standardmäßig ausgeschaltet. Bis ein Administrator es einschaltet, listet der
Assistent jede Erweiterung auf, aber keine lässt sich wählen, jeweils mit dem Grund. Wie Sie es einschalten, beschreibt
[Erweiterungsliste und Downloads](../../../funktionen/6.0/extension-liste-und-downloads.md); tun Sie es auf einer
Entwicklungsinstallation.

Jeder Weg des Exportierens verweigert außerdem eine Erweiterung, die geheime Werte oder Schlüsselmaterial enthält
([Die Geheimnisprüfung](geheimnispruefung-vor-dem-paketieren.md)).

## Schritt für Schritt im Browser

1. Öffnen Sie **Setup > Pakete > Neues Paket erstellen** und wählen Sie **Extension-Export**.
2. **Extensions, die inkludiert werden sollen**: Haken Sie Ihr Theme an. Die Tabelle zeigt seine Version, die letzte
   Änderung und Einzelheiten; eine Erweiterung, die nicht exportiert werden darf, ist mit ihrem Grund deaktiviert.
3. **Paket Informationen**: Name (Kleinbuchstaben, Ziffern und `_`), Zusammenfassung, Version, Lizenz, Format (Classic
   XML oder JSON).
4. **Paket Betreuer** und **Paket-Changelog**.
5. Prüfen Sie alles und drücken Sie **Paket erstellen**. Das Paket wird in das Repository `local` geschrieben, und seine
   Seite öffnet sich.
6. **Herunterladen** gibt Ihnen `<name>-<version>.ezpkg`.

## Auf der Kommandozeile

```bash
php ezpm.php create mytheme_pkg 'My Theme as a package' 1.0.0 install
php ezpm.php add mytheme_pkg ezextension mytheme
php ezpm.php export mytheme_pkg -d var/tmp
```

`php ezpm.php help add` listet die Elementtypen und Optionen auf. Siehe [Exportieren und importieren mit ezpm](exportieren-mit-ezpm.md).

## Das Ergebnis prüfen

```bash
tar tzf var/tmp/mytheme_pkg-1.0.0.ezpkg | head -20
```

Erwartet: `package.xml` und die Dateien des Themes unter dem Erweiterungsordner des Pakets; keine `.bak`-, `.env`- oder
Schlüsseldateien.

## Verwandte Seiten

- [Der Paketerstellungs-Assistent Schritt für Schritt](paketerstellungs-assistent-schritt-fuer-schritt.md)
- [Composer-Paket oder .ezpkg](composer-oder-ezpkg.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
