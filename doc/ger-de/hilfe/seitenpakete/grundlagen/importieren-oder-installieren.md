---
title: "Importieren oder installieren"
audience: admin, developer, hosting
category: install-upgrade
order: 22
summary: Die vier Dinge, die mit einem Paket geschehen können - Importieren, Installieren, Deinstallieren und Entfernen -, was jedes ändert, was install_type="import" bedeutet, wie der installierte Zustand festgehalten wird und welcher Befehl oder welche Seite was tut.
keywords: importieren, installieren, deinstallieren, entfernen, install_type, eZPackage::import, eZPackage::install, eZPackage::uninstall, eZPackage::remove, Tabelle ezpackage, installiert, ezpm import, ezpm install, ezpm delete, package/upload, package/install, package/uninstall, import, install, uninstall, remove, installed
type: help
---
# Importieren oder installieren

Diese Seite ist für Administratoren, die Pakete unter **Setup > Pakete** oder mit `ezpm` handhaben, und für Entwickler, die `install_type`
setzen. Sie beantwortet die Frage, die rund um Pakete die meiste Verwirrung stiftet: Was ist der Unterschied zwischen Importieren und
Installieren, und was hinterlassen Deinstallieren und Entfernen?

## Inhalt

1. [Vier Verben](#vier-verben)
2. [Importieren](#importieren)
3. [Installieren](#installieren)
4. [Der Installationstyp](#der-installationstyp)
5. [Deinstallieren](#deinstallieren)
6. [Entfernen](#entfernen)
7. [Welches Werkzeug was tut](#welches-werkzeug-was-tut)
8. [Häufige Situationen](#häufige-situationen)
9. [Verwandte Seiten](#verwandte-seiten)

## Vier Verben

| Verb | Ändert das Repository | Ändert die Website | Code |
|---|---|---|---|
| **Importieren** | fügt das Paketverzeichnis hinzu | nein | `eZPackage::import()` |
| **Installieren** | nein | ja: führt die Installationselemente aus | `eZPackage::install()` |
| **Deinstallieren** | nein | ja: führt die Deinstallation der Elemente aus | `eZPackage::uninstall()`, die Ansicht `package/uninstall` |
| **Entfernen** | löscht das Paketverzeichnis | nein | `eZPackage::remove()` |

Die Website ist die Datenbank, `extension/`, `design/` und `settings/`; das Repository ist `var/storage/packages/`
([Das lokale Paket-Repository](das-lokale-paket-repository.md)).

## Importieren

Importieren nimmt ein Archiv `.ezpkg`, prüft es und entpackt es nach `var/storage/packages/<vendor directory>/<name>/`. Nach einem Import
lässt sich das Paket auflisten, ansehen, exportieren und installieren; auf der Website hat sich nichts geändert. Der Import weist ein Paket
ab, dessen Name ungültig ist oder das bereits existiert (`STATUS_INVALID_NAME`, `STATUS_ALREADY_EXISTS`); die Downloads des
Setup-Assistenten entfernen zuerst eine ältere Kopie, wenn sie sie ersetzen müssen. Details:
[Die Datei .ezpkg](die-datei-ezpkg.md#wie-exponential-ein-archiv-liest).

## Installieren

Installieren führt das Paket auf der Website aus (`eZPackage::installUnaudited()`):

1. Zuerst jedes in `<requires>` aufgeführte Paket installieren, rekursiv.
2. Hier erfolgreich anhalten, wenn `install_type` nicht `install` ist.
3. Die Elemente von `<install>` der Reihe nach ausführen, jedes in einer eigenen Datenbanktransaktion, jedes durch seinen Handler
   ([Installationselemente und Handler](installationselemente-und-handler.md)). Das erste scheiternde Element stoppt die Installation, es sei
   denn, `continue-on-error` ist gesetzt.
4. Wenn jedes Element erfolgreich war, das Paket als installiert vermerken: eine Zeile in der Tabelle `ezpackage` mit Name, Version
   `<number>-<release>` und Datum.

Jede Installation wird im Prüfprotokoll als `system.package.install` festgehalten. Mit `as_drafts` (`ezpm install --as-drafts`, **Als
Entwürfe installieren** in der Administration) laufen nur Elemente für Inhaltsobjekte, als Entwürfe, und das Paket wird nicht als
installiert markiert, sodass es erneut als Entwürfe installiert werden kann
([Als Entwürfe installieren](../../../../eng-us/bc/6.0/package-json-format.md#install-as-drafts)).

> **Hinweis**
> Ein Paket erneut zu installieren führt seine Elemente erneut aus; `install()` überspringt kein bereits installiertes Paket. Die
> Installationsseite der Administration warnt davor.

## Der Installationstyp

`install_type` ist ein Attribut des Wurzelelements: `<package ... install_type="import">`. Es hat zwei Werte:

| Wert | Bedeutung | Genutzt von |
|---|---|---|
| `install` (der Standard, wenn das Attribut fehlt) | Die Elemente werden bei der Installation ausgeführt; das Paket lässt sich deinstallieren | Pakete für Inhaltsklassen, Inhaltsobjekte und Erweiterungen |
| `import` | Das Paket wird nur im Repository aufbewahrt: Die Installation führt seine Anforderungen aus, aber keine eigenen Elemente, vermerkt nichts, und die Deinstallation tut nichts | jedes Seitenpaket des Index von 6.0.15 und die Seitenstil-Pakete von 7x sowie `ezwebin_` / `ezflow_` |

Für ein Paket `import` sagt die Paketseite der Administration: „Hier gibt es nichts zu installieren: Dieses Paket hat den Installationstyp
„import“. Ein Site-Paket wird vom Setup-Assistenten zusammen mit den Paketen importiert, die es benötigt, und nicht einzeln installiert.“
Nach einem Hochladen öffnet sich ein solches Paket auf seiner Seite statt auf der Installationsseite.
`ezpm create <name> <summary> <version> <install|import>` setzt den Wert für ein neues Paket; alles andere wird zu `install`.

## Deinstallieren

Deinstallieren gilt nur für ein Paket mit `install_type="install"`, das als installiert vermerkt ist. Es ruft `uninstall()` jedes Handlers
auf und löscht dann den Vermerk der Installation.

- **Deinstallieren** in der Administration (`/package/uninstall/<name>`) geht die Elemente von `<uninstall>` in umgekehrter Reihenfolge
  durch, fragt, wenn ein Handler eine Entscheidung braucht (zum Beispiel beim Entfernen einer Klasse, die Objekte hat), und braucht die
  Richtlinie `package/install` für den Typ des Pakets.
- `eZPackage::uninstall()` in PHP geht die Elemente von `<install>` durch.
- `ezpm` hat keinen Befehl zum Deinstallieren.

Was entfernt wird, hängt vom Handler ab: Klassen, Objekte und Erweiterungen werden entfernt; ab 6.0.15 auch die Dateien, die die Installation
angelegt hat und die seitdem niemand geändert hat; Änderungen an SQL und INI haben keine eigene Deinstallation und bleiben. Deinstallieren
lässt sich nicht rückgängig machen. Jede Deinstallation eines installierten Pakets wird im Prüfprotokoll als `system.package.uninstall`
festgehalten.

## Entfernen

Entfernen löscht das Paketverzeichnis aus dem Repository und löscht seinen Vermerk der Installation. Es deinstalliert **nicht**: Klassen,
Inhalte, Erweiterungen und Dateien, die das Paket installiert hat, bleiben auf der Website, und das Paket lässt sich danach nicht mehr unter
**Setup > Pakete** deinstallieren. In der Administration führt die Bestätigungsseite auf, was entfernt wird, welche Pakete es verlangen und
ob der Installer es als Quelle nutzt; siehe [Anleitung zu Paketen, 9. Pakete entfernen](../../../anleitungen/pakete.md#9-pakete-entfernen).
Auf der Kommandozeile: `php ezpm.php delete <name>` (Aliase `del`, `remove`, `rm`).

## Welches Werkzeug was tut

| Werkzeug | Importieren | Installieren | Deinstallieren | Entfernen |
|---|---|---|---|---|
| Setup-Assistent, Kickstarter, `exp:install` | das Seitenpaket und seine Anforderungen | die Anforderungen mit `install_type="install"` | | |
| Setup > Pakete | **Hochladen** (`package/upload`) | **Installieren** (`package/install/<name>`) | **Deinstallieren** (`package/uninstall/<name>`) | **Entfernen** in der Liste oder auf der Paketseite |
| `ezpm.php` | `import <file>` | `install <name>` | | `delete <name>` |
| ezupdate | ruft Pakete von Paketservern ins Repository ab | | | |

Der Teil von ezupdate ist in [Pakete mit ezupdate abrufen](../admin-oberflaeche/pakete-mit-ezupdate-abrufen.md) beschrieben.

## Häufige Situationen

| Sie wollen ... | Tun Sie |
|---|---|
| ein Seitenpaket offline verfügbar haben | es und seine Anforderungen importieren; nicht installieren |
| einer laufenden Website ein Paket mit Inhaltsklassen hinzufügen | es importieren, dann installieren |
| ein importiertes Paket durch das neuere des Servers ersetzen | es entfernen, dann das neue Archiv importieren |
| eine Installation rückgängig machen | unter **Setup > Pakete** deinstallieren, bevor Sie das Paket entfernen |
| das Repository aufräumen | entfernen; die Website behält, was installiert wurde |

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Ein Paket importieren](../admin-oberflaeche/ein-paket-importieren.md)
- [Installieren und Deinstallieren](../admin-oberflaeche/installieren-und-deinstallieren.md)
- [Anleitung zu Paketen, 7. Installieren und Deinstallieren](../../../anleitungen/pakete.md#7-installieren-und-deinstallieren)
- [ezpm, der Paketmanager auf der Kommandozeile](../../../../eng-us/features/6.0/ezpm-package-manager-cli.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
