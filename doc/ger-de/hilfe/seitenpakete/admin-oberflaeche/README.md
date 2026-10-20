---
title: Pakete in der Administration
audience: admin, developer, hosting
category: install-upgrade
order: 100
summary: Die Seiten der Administration, die Pakete auf einer installierten Website behandeln - die Paketliste und die Seite eines Pakets, Importieren, Installieren und Deinstallieren, der Paketassistent und die Paketserver und Abrufe von ezupdate - mit den Richtlinien, die jede braucht, und wo jede erklärt wird.
keywords: Administration, Admin, Setup > Pakete, package/list, package/view, package/upload, package/install, package/uninstall, package/create, ezupdate, update/servers, update/packages, Berechtigungen, Richtlinien, administration, Setup > Packages, permissions, policies
type: help
---
# Pakete in der Administration

Dieser Abschnitt ist für Administratoren, die sich im Browser um die Pakete einer installierten Website kümmern, und für Entwickler und
Hoster, die wissen wollen, was diese Seiten tun, bevor sie Redakteure daran lassen. Er beantwortet: wo die Paketseiten sind, was jede
ändert und welche Richtlinie sie öffnet.

![Setup, Pakete: die Repositorys und ihre Pakete](../../../../en/us/help/images/help/sp-gui-list.webp)

Seitenpakete selbst werden vom Setup-Assistenten, dem Kickstarter oder `exp:install` installiert, nicht von diesen Seiten
([Wie der Setup-Assistent Pakete nutzt](../grundlagen/wie-der-setup-assistent-pakete-nutzt.md)). Was Sie hier danach sehen, ist das, was diese
Installer importiert haben, plus alles, was Sie hochladen, abrufen oder erstellen.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Zwei Module](#zwei-module)
3. [Was die Website ändern kann](#was-die-website-ändern-kann)
4. [Wenn die Konfiguration vom Ausrollen verwaltet wird](#wenn-die-konfiguration-vom-ausrollen-verwaltet-wird)

## Die Seiten

| Seite | Wo | Erklärt auf |
|---|---|---|
| Paketliste | **Setup > Pakete**, `/package/list[/<repository>]` | [Die Paketliste und die Paketseite](die-paketliste-und-die-paketseite.md) |
| Die Seite eines Pakets | `/package/view/full/<name>[/<repository>]` | derselben Seite |
| Importieren (Hochladen) | **Neues Paket importieren**, `/package/upload` | [Ein Paket importieren](ein-paket-importieren.md) |
| Installieren, Deinstallieren | `/package/install/<name>`, `/package/uninstall/<name>` | [Installieren und Deinstallieren](installieren-und-deinstallieren.md) |
| Erstellen | **Neues Paket erstellen**, `/package/create` | [Der Paketassistent](der-paketassistent.md) |
| Paketserver | **Setup > Updates and packages > Package servers**, `/update/servers` | [Paketserver in ezupdate](paketserver-in-ezupdate.md) |
| Die Pakete eines Servers, Abrufen | `/update/packages/<server>` | [Pakete mit ezupdate abrufen](pakete-mit-ezupdate-abrufen.md) |
| Wer was darf | Rollen und Richtlinien | [Berechtigungen für Pakete](berechtigungen-fuer-pakete.md) |

Die Referenz für jedes Detail der Seiten des Kernels ist [Pakete](../../../anleitungen/pakete.md); dieser Abschnitt ist der aufgabenorientierte
Einstieg.

## Zwei Module

| Modul | Kommt mit | Tut |
|---|---|---|
| `package` (`kernel/package/module.php`) | dem Kernel | listet, zeigt, vergleicht, lädt herunter, lädt hoch, installiert, deinstalliert, erstellt und entfernt Pakete in den lokalen Repositorys unter `var/storage/packages/` |
| `update` (`extension/ezupdate/modules/update/module.php`) | der Erweiterung ezupdate (`se7enxweb/ezupdate` in `composer.json`) | Composer-Updates und die `.ezpkg`-Paketserver: listet die Pakete eines Servers auf und ruft eines ins lokale Repository ab |

ezupdate ruft ab; die Seiten des Kernels installieren. Ein abgerufenes Paket erscheint in der Paketliste wie ein hochgeladenes.

## Was die Website ändern kann

| Aktion | Ändert |
|---|---|
| Ansehen, Vergleichen, Herunterladen, Prüfen | nichts |
| Importieren (Hochladen) oder Abrufen | fügt ein Paketverzeichnis unter `var/storage/packages/` hinzu; Inhalte und Einstellungen der Website bleiben, wie sie sind |
| Installieren | führt die Installationselemente des Pakets aus: Klassen, Inhalte, Dateien, Einstellungen, Erweiterungen, wie das Paket es sagt |
| Deinstallieren | führt die Deinstallationselemente aus: Es entfernt, was die Elemente auf dieser Website angelegt haben, Inhalte eingeschlossen, und lässt sich nicht rückgängig machen |
| Entfernen | löscht das Paketverzeichnis; was es installiert hat, bleibt |
| Einen Paketserver hinzufügen | schreibt `settings/override/ezupdate.ini.append.php` |

> **Warnung**
> Installieren und deinstallieren Sie zuerst auf einer Kopie der Website. Beides ändert Inhalte und Einstellungen, und die Paketseiten
> bieten kein Rückgängigmachen an.

## Wenn die Konfiguration vom Ausrollen verwaltet wird

Mit `site.ini [ConfigurationSettings] ManagedByDeployment=enabled` kommen Einstellungen, Templates und Code nur aus dem Ausrollen
(`kernel/classes/expmanagedconfiguration.php`). Dann sind `package/upload`, `package/install`, `package/uninstall`, `package/create` und
`package/export` in einer Web-Anfrage gesperrt wie ein ausgeschaltetes Modul, die Aktionen zum Entfernen in der Liste werden verweigert,
und Einstellungsdateien werden nicht aus dem Browser geschrieben. Die Liste, die Seite eines Pakets und **Vergleichen** bleiben offen. Die
Kommandozeile (`ezpm.php`) funktioniert weiter.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Pakete](../../../anleitungen/pakete.md)
- [ezupdate: Updates und Pakete in der Administration](../../../../en/us/features/6.0/extensions/ezupdate.md)
- [Die Seiten Über und Pakete](../../../../en/us/features/6.0/about-and-package-pages.md)
- [Ein eigenes Seitenpaket erstellen](../eigene/README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
