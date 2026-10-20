---
title: Pakete (.ezpkg) und der Paket-Ersteller
audience: developer, designer, app-creator
category: extension-development
order: 100
summary: Der andere Weg der Verbreitung - .ezpkg-Pakete, erstellt mit dem Paket-Ersteller in der Administration oder mit ezpm.php - Erweiterungsexporte, Exporte von Inhaltsklassen, Site-Styles - und wann Sie sie neben Composer verwenden.
keywords: Paket, Pakete, ezpkg, Paket-Ersteller, Erweiterungsexport, Export von Inhaltsklassen, Site-Style, Import, Composer, package, package creator, /package/create, ezpm.php, extension export, content class export, site style, import
type: help
---
# Pakete (.ezpkg) und der Paket-Ersteller

Teil 7, Kapitel 10. Vor Composer wurden Erweiterungen und Inhalte als Pakete ausgetauscht: `.ezpkg`-Archive, die die
Administration erstellen, hochladen und installieren kann. Exponential 6 unterstützt sie weiterhin, und für manche
Aufgaben sind sie nach wie vor das richtige Werkzeug. Dieses Kapitel erklärt, wann Sie sie verwenden und wie Sie
eines erstellen. Die vollständige Anleitung ist
[Pakete: was sie enthalten, wo sie liegen und wie Sie sicher mit ihnen umgehen](../../../anleitungen/pakete.md).

## Composer oder ein Paket?

| Sie verbreiten | Verwenden Sie |
|---|---|
| Eine Erweiterung (PHP, Templates, Einstellungen) | Composer ([Composer und Packagist](composer-und-packagist.md)) |
| Inhaltsklassen, die ein Theme oder eine Erweiterung erwartet | Pakete, mitgeliefert in der Erweiterung (wie sevenx_themes_simple es in `packages/` tut) |
| Beispiel- oder Demo-Inhalte | Pakete |
| Eine Erweiterung für Websites, die nicht mit Composer installiert wurden | ein Paket mit dem Erweiterungsexport, zusätzlich zu Composer |

## Ein Paket in der Administration erstellen

**Neues Paket erstellen** auf der Seite der Pakete öffnet `/package/create`, mit einem Assistenten je Art, die Ihre
Rolle verwenden darf: Export von Inhaltsobjekten, Export von Inhaltsklassen, Erweiterungsexport und Site-Style. Der
Assistent fragt nach den Bestandteilen, dann nach Name, Zusammenfassung, Version, Lizenz und Maintainer sowie dem
Format, in dem das Paket gespeichert wird (klassisches XML oder JSON), und schreibt das Paket in das Repository
`local`. An der Website ändert sich nichts. Die Richtlinie `package/create` steuert, wer ihn verwenden darf.

Laden Sie es dann von der Seite des Pakets herunter (Richtlinie `package/export`): Das ist die `.ezpkg`-Datei.

## Pakete in der Shell erstellen und verwalten

```bash
php ezpm.php list                                   # the packages of the repositories
php ezpm.php export mytheme_classes -d var/tmp      # write an .ezpkg (only reads the package)
php ezpm.php import var/tmp/mytheme_classes.ezpkg   # the same archive checks as the upload page
php ezpm.php help                                   # every command
```

(Fügen Sie `--allow-root-user` hinzu, wenn Sie sie als root ausführen.) `ezpm.php` liegt im Wurzelverzeichnis der
Installation.

## Pakete in Ihrer Erweiterung

Für ein Theme oder eine Erweiterung, deren Templates Inhaltsklassen erwarten:

1. Legen Sie die Klassen auf einer Entwicklungs-Website an.
2. Exportieren Sie sie mit dem Assistenten für den Export von Inhaltsklassen; setzen Sie Version und Lizenz des Pakets
   passend zur Erweiterung.
3. Legen Sie die `.ezpkg`-Dateien in den Ordner `packages/` der Erweiterung.
4. Sagen Sie Benutzern in der README, wie sie sie importieren und installieren (die Upload-Seite der Paketliste oder
   `ezpm.php import`).

Versionieren Sie die Paketdateien mit der Erweiterung: Eine geänderte Klasse bedeutet eine neue Paketversion im
nächsten Release.

## Sicherheit

Der Kernel prüft jedes hochgeladene oder importierte Archiv: Pfade, die aus dem Paket hinauszeigen, Links und
Gerätedateien werden abgewiesen ([die Anleitung](../../../anleitungen/pakete.md), Abschnitte 6 und 12). Erstellen Sie
Pakete mit dem Assistenten oder `ezpm.php export`, nie, indem Sie ein Verzeichnis mit Links darin von Hand packen.
Legen Sie keine Geheimnisse oder personenbezogenen Daten in Pakete: Beispielinhalte müssen erfunden sein.

## Verwandte Seiten

- Zurück: [filelist.md5 und die Upgrade-Prüfung](filelist-md5-und-die-upgrade-pruefung.md)
- Weiter: [Vor einem Release testen](vor-einem-release-testen.md)
- [Anleitung zu Paketen](../../../anleitungen/pakete.md)
- [Ein Theme verbreiten](../../../../eng-us/help/extension-development/part-6-theme-developers/distributing-a-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
