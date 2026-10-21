---
title: INI-Einstellungen in der Administration
audience: admin, website-creator, designer, developer
category: build-two-ways
order: 410
summary: Die Seiten unter Setup > INI-Einstellungen; wie sie die Schichten einer INI-Datei zeigen, welche Datei gewinnt, wie ein Array bearbeitet wird, wo eine Änderung gespeichert wird; das Werkzeug ohne Code hinter Kopf- und Fußzeilenmenü.
keywords: INI-Einstellungen, Einstellungen anzeigen, Einstellungen bearbeiten, INI-Schichten, Override, Siteaccess-Einstellungen, Erweiterungseinstellungen, Array, Zurücksetz-Zeile, Speichern in, menu.ini, Ini settings, settings view, settings edit, INI layers, siteaccess settings, extension settings, reset line, Save it in
type: help
---
# INI-Einstellungen in der Administration

**Setup > INI-Einstellungen** (Bereich *System*) zeigt und ändert die Einstellungen jedes Siteaccess ohne Terminal. Es
ist das Gegenstück der Administration zu `./console exp:ini`.

## Inhalt

1. [Eine Datei wählen](#eine-datei-wählen)
2. [Die Schichten lesen](#die-schichten-lesen)
3. [Eine Einstellung bearbeiten](#eine-einstellung-bearbeiten)
4. [Arrays und die Zurücksetz-Zeile](#arrays-und-die-zurücksetz-zeile)
5. [Wo die Änderung gespeichert wird](#wo-die-änderung-gespeichert-wird)

## Eine Datei wählen

Wählen Sie die **INI-Datei** und den **Siteaccess**, dann **Einstellungen anzeigen**. Die Adresse wird zu
`/settings/view/<siteaccess>/<file>`, sodass sich die Seite als Lesezeichen speichern lässt.

![Die Seite Einstellungen mit der Auswahl von INI-Datei und Siteaccess und der Schaltfläche Einstellungen anzeigen](../../../eng-us/help/build-two-ways/images/hm-11-settings-pick-file.webp)

Die Seite erklärt, wie Einstellungen funktionieren: *Jede INI-Datei wird in Schichten gelesen: zuerst settings/&lt;file&gt;.ini,
dann die Dateien der aktiven Extensions und des Siteaccess, dann settings/override. Eine spätere Datei gewinnt: Sie ersetzt
einen einfachen Wert und ergänzt ein Array, außer sie leert es zuerst.* Geheimnisse (Passwörter, Schlüssel, Tokens) werden nie angezeigt.

## Die Schichten lesen

Jede Einstellung zeigt ihren wirksamen Wert und unter **In N Dateien gesetzt** jede Datei, die sie setzt, in der
Lesereihenfolge. Eine Datei, deren Werte verworfen werden, ist als **überschrieben** markiert, die wirksame mit **Elemente wirksam**.

![Die Einstellung MainMenuID: die Erweiterung sevenx_themes_media für site ist überschrieben, die Datei des Siteaccess site ist wirksam](../../../eng-us/help/build-two-ways/images/hm-03-settings-mainmenuid.webp)

Kennzeichen: **kein Standard** (die `settings/<file>.ini` des Kernels setzt sie nicht), **Liste mit 6** (ein Array mit
sechs Elementen). Die Seite kann außerdem eine Datei durchsuchen, die gegenüber dem Standard geänderten Einstellungen
auflisten und mit einem anderen Siteaccess vergleichen.

## Eine Einstellung bearbeiten

**Bearbeiten** in einer Zeile öffnet `/settings/edit/<siteaccess>/<file>/<Block>/<Setting>/<placement>`: noch einmal
die Kette, dann **Neuer Wert** mit **Typ** (Liste, Wahr/Falsch, Enabled/Disabled, String, Numeric) und **Wert**.

## Arrays und die Zurücksetz-Zeile

Die eigene Hilfe der Seite: *Ein Element pro Zeile: =Wert fügt ein Element hinzu, [Schlüssel]=Wert setzt einen Schlüssel.
Lassen Sie die erste Zeile leer, um das Array vor diesen Elementen zu leeren; ohne sie werden sie zu dem hinzugefügt, was
frühere Dateien liefern.*

![Das Feld Wert von MainMenuID mit leerer erster Zeile und einer Zeile =id pro Menüeintrag](../../../eng-us/help/build-two-ways/images/hm-04-settings-edit-add-192.webp)

## Wo die Änderung gespeichert wird

**Speichern in** bietet an:

| Auswahl | Datei |
|---|---|
| Nur Siteaccess site | `settings/siteaccess/site/<file>.ini.append.php` |
| Alle Siteaccesses (globaler Override) | `settings/override/<file>.ini.append.php` |
| In einer Extension | `extension/<name>/settings/...` |

Das Speichern schreibt nur diese Datei, leert die INI-Caches und kehrt mit einer Meldung *Gespeichert: ...* zu den
Einstellungen der Datei zurück. Ein Wert, der in Templates verwendet wird, braucht außerdem das Leeren von
**Template & Inhalt** ([Caches nach einer Änderung](caches-nach-einer-aenderung.md)).

## Verwandte Seiten

- [Das Kopfmenü in der Administration](kopfmenue-in-der-administration.md)
- [exp:ini für Site-Entwickler](exp-ini-fuer-site-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
