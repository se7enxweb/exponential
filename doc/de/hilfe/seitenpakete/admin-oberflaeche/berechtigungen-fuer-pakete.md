---
title: Berechtigungen für Pakete
audience: admin, hosting
category: install-upgrade
order: 107
summary: Jede Richtlinie, die eine Paketseite oder eine Schaltfläche öffnet - die Funktionen des Moduls package und ihre Einschränkungen Type, CreatorType und Role, update/ezupdate und update/manage von ezupdate, package/export_extension - mit Beispielen für Rollen und den Schaltern, die die Seiten unabhängig von Rollen sperren.
keywords: Berechtigungen, Richtlinien, Rollen, package/list, package/read, package/import, package/install, package/remove, package/create, package/export, package/export_extension, package/edit, Einschränkung Type, CreatorType, Einschränkung Role, update/ezupdate, update/manage, ManagedByDeployment, permissions, policies, roles, Type limitation, Role limitation
type: help
---
# Berechtigungen für Pakete

Diese Seite ist für Administratoren, die entscheiden, wer Pakete ansehen, importieren, installieren, erstellen und entfernen darf. Das
Installieren eines Pakets ändert Inhalte und Einstellungen; diese Richtlinien gehören also nur zu vertrauenswürdigen Rollen. Die Rolle
Administrator mit allen Funktionen hat jede davon.

![Rollen, wo die Richtlinien für Pakete vergeben werden](../../../../eng-us/help/images/help/sp-perms.webp)

## Inhalt

1. [Das Modul package](#das-modul-package)
2. [Die Einschränkungen](#die-einschränkungen)
3. [Erweiterungen exportieren](#erweiterungen-exportieren)
4. [Das Modul update (ezupdate)](#das-modul-update-ezupdate)
5. [Beispiele für Rollen](#beispiele-für-rollen)
6. [Schalter, die die Seiten trotzdem sperren](#schalter-die-die-seiten-trotzdem-sperren)

## Das Modul package

Aus `kernel/package/module.php`:

| Funktion | Öffnet | Eingeschränkt durch |
|---|---|---|
| `package/list` | die Paketliste (`package/list`) | Type |
| `package/read` | die Seite eines Pakets (`package/view`), seine Dateien (`package/viewfile`), **Vergleichen** (`package/compare`) | Type |
| `package/export` | **Herunterladen** (`package/export`) | Type |
| `package/import` | **Neues Paket importieren** (`package/upload`) | Type |
| `package/install` | **Installieren**, **Neu installieren** (`package/install`) und **Deinstallieren** (`package/uninstall`); die Importaktionen von **Vergleichen** | Type |
| `package/remove` | **Ausgewählte entfernen**, **Entfernen** | Type |
| `package/create` | **Neues Paket erstellen** (`package/create`) | Type, CreatorType, Role |
| `package/edit` | definiert, aber keine Ansicht des Moduls fragt danach | Type |
| `package/export_extension` | eine Erweiterung als Paket exportieren (siehe unten) | keine |

Jedes Formular dieser Seiten trägt das Formular-Token; ein Absenden ohne es wird mit 403 abgewiesen
([Pakete, Abschnitt 10](../../../anleitungen/pakete.md#10-berechtigungen)).

## Die Einschränkungen

| Einschränkung | Werte | Wirkung |
|---|---|---|
| Type | `package.ini [PackageSettings] TypeList[]`: Inhaltsobjekt (`contentobject`), Inhaltsklasse (`contentclass`), Seitenstil (`sitestyle`), Website (`site`) | die Richtlinie gilt nur für Pakete dieser Typen (`eZPackage::canUsePackagePolicyFunction()`); eine Richtlinie ohne sie gilt für jeden Typ |
| CreatorType | die Ersteller aus `package.ini [CreationSettings]`: `ezcontentobject`, `ezcontentclass`, `ezextension`, `ezstyle` | welche Assistenten **Neues Paket erstellen** anbietet |
| Role | `package.ini [MaintainerSettings] RoleList[]`: lead, developer, designer, contributor, tester | welche Betreuerrollen der Assistent Sie vergeben lässt; ohne eine wird der Schritt für den Betreuer ausgelassen |

Die Prüfungen gelten pro Paket: `package/install`, eingeschränkt auf `contentclass`, lässt eine Rolle ein Klassenpaket installieren, aber
kein Inhaltspaket, und die Installationsseite fragt die Richtlinie für den eigenen Typ des Pakets, nicht für irgendeinen Typ.

> **Hinweis**
> Eine Einschränkung Type bietet nur die Typen aus `TypeList[]` an. Pakete anderer Typen (der Index von 6.0.15 führt auch Pakete
> `extension` auf) sind nur mit einer Richtlinie ohne Einschränkung Type erreichbar.

## Erweiterungen exportieren

Nach `package/export_extension` wird gefragt, wenn eine Erweiterung in der Administration exportiert wird und
`package.ini [ExtensionExportSettings] RequirePolicy=enabled` (der Standard) gilt. Unabhängig von Rollen muss die Erweiterung auch in
`AllowedExtensions[]` stehen (standardmäßig leer: keine), und eine Erweiterung mit geheimen Werten oder Schlüsselmaterial wird immer
abgewiesen. Die Kommandozeile läuft ohne Benutzer, und nach der Richtlinie wird dort nicht gefragt
([Eine Erweiterung als Paket exportieren](../../../../eng-us/help/designers/extensions/extension-export-package.md)).

## Das Modul update (ezupdate)

Aus `extension/ezupdate/modules/update/module.php`:

| Funktion | Öffnet |
|---|---|
| `update/ezupdate` | die Seiten von ezupdate: Übersicht, installierte Pakete, Suche auf Packagist, die Pakete eines Servers (`update/packages`), Läufe, Förderung; Prüfungen und Probeläufe |
| `update/manage` | Änderungen: Aktualisieren und Installieren mit Composer, **Fetch** von `.ezpkg`-Paketen und die Seite **Package servers** (`update/servers`) mit Hinzufügen und Entfernen von Servern |

Laut `extension/ezupdate/doc/SECURITY.md` braucht jede Seite `update/ezupdate` und jede Änderung `update/manage`, geprüft bei jeder Anfrage
in der Ansicht, nicht nur durch das Ausblenden von Schaltflächen.

## Beispiele für Rollen

| Rolle | Richtlinien |
|---|---|
| Leser von Paketen (Prüfer, Support) | `package/list`, `package/read` |
| Inhalts-Paketierer | die obigen, `package/export`, `package/create` eingeschränkt auf CreatorType `ezcontentobject` und `ezcontentclass` |
| Paket-Installierer auf einer Staging-Website | die obigen, `package/import`, `package/install` |
| Betreuer eines Paketservers | `update/ezupdate`, `update/manage` |
| Aufräumer | `package/list`, `package/read`, `package/remove` |

Vergeben Sie `package/install` und `update/manage` sparsam: Beide ändern die Website, und eine Deinstallation lässt sich nicht rückgängig
machen.

## Schalter, die die Seiten trotzdem sperren

| Schalter | Sperrt |
|---|---|
| `site.ini [ConfigurationSettings] ManagedByDeployment=enabled` | in einer Web-Anfrage: `package/upload`, `package/install`, `package/uninstall`, `package/create`, `package/export`, die Aktionen zum Entfernen und jedes Schreiben einer Einstellungsdatei (also auch das Hinzufügen eines Servers in ezupdate); die Liste, die Seite eines Pakets und **Vergleichen** bleiben offen (`kernel/classes/expmanagedconfiguration.php`) |
| `ezupdate.ini [UpdateSettings] AllowUpdate`, `AllowInstall` (beide standardmäßig `disabled`) | Composer-Updates und -Installationen aus der Administration; das Abrufen von `.ezpkg`-Paketen ist nicht betroffen |

## Verwandte Seiten

- [Pakete in der Administration](README.md)
- [Pakete, Abschnitt 10](../../../anleitungen/pakete.md#10-berechtigungen)
- [ezupdate: Updates und Pakete in der Administration](../../../../eng-us/features/6.0/extensions/ezupdate.md)
- [Der Paketassistent](der-paketassistent.md)
- [Pakettypen](../grundlagen/pakettypen.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
