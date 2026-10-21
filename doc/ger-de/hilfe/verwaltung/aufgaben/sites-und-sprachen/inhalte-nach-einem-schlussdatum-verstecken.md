---
title: Inhalte nach einem Schlussdatum verstecken
audience: government, admin
category: public-sector
order: 430
summary: Aufgabe für Administratoren - Beteiligungsverfahren, Stellenangebote oder Aufrufe automatisch verstecken, wenn ihr Schlussdatum vorbei ist, mit dem Cronjob hide.php und den HideSettings in content.ini, wobei der Inhalt als Schriftgut erhalten bleibt (anders als bei unpublish.php, das ihn entfernt).
keywords: Schlussdatum, verstecken, Beteiligungsverfahren, Stellenangebote, Cronjob, Schriftgut, closing date, hide, hide.php, HideSettings, HideDateAttributeList, consultations, vacancies, cronjob, records
type: help
---
# Inhalte nach einem Schlussdatum verstecken

Diese Aufgabe richtet sich an Administratoren. Der Cronjob `hide.php` (im globalen Teil) versteckt Inhalte, deren Datum
vorbei ist, so wie **Verstecken** in der Administration; der Inhalt und seine Historie bleiben erhalten. Hintergrund:
[Zeitgesteuertes Veröffentlichen und Sperrfristen](../../transparenz-und-informationsfreiheit/zeitgesteuertes-veroeffentlichen-und-sperrfristen.md)
und [Cronjobs](../../../../anleitungen/cronjobs.md).

## Schritte

1. Stellen Sie sicher, dass die Klasse (zum Beispiel `consultation`) ein Datumsfeld hat, zum Beispiel `close_date`.
2. In `settings/override/content.ini.append.php`:

   ```ini
   <?php /*
   [HideSettings]
   RootNodeList[]
   RootNodeList[]=<node id of the consultations folder>
   HideDateAttributeList[]
   HideDateAttributeList[consultation]=close_date
   */ ?>
   ```

3. Leeren Sie den INI-Cache; stellen Sie sicher, dass der globale Teil der Cronjobs läuft (`php runcronjobs.php` ohne
   Namen eines Teils).

Verwenden Sie dafür nicht `unpublish.php` (`[UnpublishSettings]`): Es entfernt den Knoten, wenn ein `unpublish_date`
vorbei ist.

## Prüfen

Nach dem Schlussdatum und dem nächsten Lauf des Cronjobs ist das Element in der Administration als versteckt markiert
und wird Besuchern nicht mehr gezeigt; Suchmaschinen, die die Sichtbarkeit speichern (Solr), werden ebenfalls
benachrichtigt.

## Probleme

| Problem | Lösung |
|---|---|
| Nichts wird versteckt | der Cronjob läuft nicht, oder der Ordner steht nicht in `RootNodeList[]` |
| Das Element muss wieder öffentlich sein (verlängerte Frist) | ändern Sie das Datum und zeigen Sie es wieder an |

## Verwandte Seiten

- [Veröffentlichte Informationen zurücknehmen und berichtigen](../../transparenz-und-informationsfreiheit/veroeffentlichte-informationen-zuruecknehmen-und-berichtigen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
