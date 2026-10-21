---
title: Archiv und Ablauf
audience: government, admin, website-creator, project-manager
category: public-sector
order: 80
summary: Was mit alten Nachrichten und Bekanntmachungen auf einer Exponential-Site geschieht - automatisches Verstecken mit hide.php, Verschieben in den Papierkorb mit unpublish.php, Archive nach Jahr, Kennzeichnungen mit Objektzuständen und was als Schriftgut zu behalten ist.
keywords: Archiv, Ablauf, Datum zum Verstecken, Papierkorb, Objektzustände, Nachrichtenarchiv, Schriftgut, archive, expiry, hide.php, unpublish.php, HideSettings, UnpublishSettings, unpublish_date, hide date, trash, object states, news archive, records
type: help
---
# Archiv und Ablauf

Diese Seite richtet sich an Administratoren, Site-Ersteller und Projektleiter, die entscheiden, was mit Nachrichten und
Bekanntmachungen geschieht, wenn sie alt sind. Exponential hat zwei Cronjob-Skripte, die nach Datum handeln, dazu das
Verstecken, Zustände und den Papierkorb. Quellen: [Cronjobs](../../../../anleitungen/cronjobs.md),
[Objektzustände](../../../../anleitungen/objektzustaende.md) und der Cronjob-Code in
`kernel/private/classes/cronjobs/hide.php` und `unpublish.php`.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die beiden Skripte](#die-beiden-skripte)
2. [hide.php einrichten](#hidephp-einrichten)
3. [unpublish.php einrichten](#unpublishphp-einrichten)
4. [Archive nach Jahr](#archive-nach-jahr)
5. [Was zu behalten ist](#was-zu-behalten-ist)

## Die beiden Skripte

| Skript | Was es tut | Einstellungen | Umkehrbar |
|---|---|---|---|
| `hide.php` | versteckt Knoten, deren Datumsattribut vorbei ist (wie **Verstecken**), und benachrichtigt die Suchmaschine | `content.ini [HideSettings] RootNodeList[]`, `HideDateAttributeList[<class>]=<attribute>` | ja: wieder zeigen |
| `unpublish.php` | verschiebt Objekte der aufgeführten Klassen, deren Attribut `unpublish_date` vorbei ist, in den Papierkorb (im Audit als Entfernen in den Papierkorb festgehalten) | `content.ini [UnpublishSettings] RootNodeList[]`, `ClassList[]`; das Attribut muss `unpublish_date` heißen | ja, aus dem Papierkorb, bis er geleert wird |

Beide gehören zum globalen Teil der Cronjobs (`[CronjobSettings] Scripts[]`); sie tun nichts, bis ihre Einstellungen
Wurzelknoten nennen. Führen Sie eines zum Testen von Hand aus:
`php runcronjobs.php -s <siteaccess> --script=hide.php`.

## hide.php einrichten

1. Geben Sie der Klasse ein Attribut vom Typ Datum und Zeit, zum Beispiel `hide_date`.
2. In `settings/override/content.ini.append.php`:

   ```ini
   [HideSettings]
   RootNodeList[]=<node id of the Notices folder>
   HideDateAttributeList[notice]=hide_date
   ```

3. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`.
4. Legen Sie eine Test-Bekanntmachung mit einem vergangenen Datum zum Verstecken an; führen Sie das Skript aus; die
   Bekanntmachung ist versteckt.

Ein leeres Datum wird ignoriert (der Filter verlangt einen Wert größer als 0).

## unpublish.php einrichten

```ini
[UnpublishSettings]
RootNodeList[]=<node id of the folder>
ClassList[]=<class identifier>
```

Die Klasse braucht ein Attribut mit dem Bezeichner `unpublish_date`. Bevorzugen Sie für Inhalte des öffentlichen
Sektors `hide.php`: Versteckte Inhalte bleiben, wo sie waren, mit ihren Versionen, und lassen sich später leichter
nachweisen; Inhalte im Papierkorb verschwinden endgültig, wenn der Papierkorb geleert wird (**Papierkorb leeren** oder
`./console exp:trashpurge`).

## Archive nach Jahr

| Muster | Verwendung |
|---|---|
| Nachrichten bleiben in Jahresordnern sichtbar | Nachrichten gehören zum öffentlichen Nachweis; alte Elemente bleiben auffindbar |
| Ein Hinweis „Dies ist ein archiviertes Element“ für Elemente älter als ein Zeitraum | eine Template-Bedingung auf das Veröffentlichungsdatum |
| Ein Objektzustand „Archiviert“ | eine Zustandsgruppe mit Aktuell und Archiviert; Templates zeigen ihn; Listen aktueller Nachrichten filtern ihn heraus ([Objektzustände](../../../../anleitungen/objektzustaende.md)) |
| Bekanntmachungen nach ihrem Ende versteckt | sie sind keine gültigen Informationen mehr |

## Was zu behalten ist

| Inhalt | Übliche Praxis |
|---|---|
| Nachrichten und Pressemitteilungen | veröffentlicht lassen; sie werden zitiert |
| Bekanntmachungen und Warnmeldungen | nach dem Ende verstecken; als Schriftgut behalten |
| Stellenangebote, Ausschreibungen | nach der Frist verstecken; behalten, wie es Ihre Schriftgutregeln sagen |
| Veranstaltungsseiten | nach Jahr behalten oder verstecken |

Die Website ist selten das amtliche Archiv. Übergeben Sie Schriftgut gemäß Ihrem Plan an Ihr Schriftgutsystem; die
Anleitungen zu [Schriftgut und Archivierung](../../schriftgut-und-archivierung/README.md) in dieser Hilfe für Behörden gehen
tiefer.

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [Das Muster für dringende Bekanntmachungen](das-muster-fuer-dringende-bekanntmachungen.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
