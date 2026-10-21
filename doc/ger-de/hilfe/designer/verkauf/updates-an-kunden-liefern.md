---
title: "Updates an zahlende Kunden liefern"
audience: designer, developer
category: theme-sales
order: 140
summary: Wege, Theme-Releases zu Kunden zu bringen - öffentliche Releases auf Packagist, ein privates Git-Repository mit Composer, Pakete als Dateien oder die Installation durch Sie - mit der Funktionsweise jedes Wegs und wann welcher Zugang endet.
keywords: Updates, Auslieferung, privates Repository, Composer, Private Packagist, Satis, Deploy-Keys, Zugang, Kunden, updates, delivery, private repository, private packagist, deploy keys, access, customers
type: help
---
# Updates an zahlende Kunden liefern

Diese Seite ist für Designer, die Updates oder Wartung verkaufen. Wählen Sie pro Angebot einen Lieferweg und
dokumentieren Sie ihn.

## Die Möglichkeiten

| Weg | Wie der Kunde aktualisiert | Hinweise |
|---|---|---|
| Öffentliches Release auf Packagist | `composer update yourvendor/mytheme` | Am einfachsten; der Kunde bezahlt für Support, nicht für Zugang |
| Privates Git-Repository | Composer mit einem `vcs`-Repository und einem Zugangstoken oder Deploy-Key | Der Zugang kann mit dem Abonnement enden; die Kopien, die er hat, bleiben unter der GPL seine |
| Ein privates Composer-Repository | Composer mit Ihrer Repository-URL und Zugangsdaten | Für viele Pakete; Sie betreiben oder mieten das Repository |
| `.ezpkg`- oder Archivdateien | Von Hand importieren oder kopieren | Für Kunden ohne Composer |
| Sie installieren es | Nichts; Sie rollen aus | Braucht Zugang zu seinem Server; vereinbaren Sie es schriftlich |

## Schritt für Schritt: ein privates Repository mit Composer

1. Legen Sie ein privates Repository für die Fassung des Kunden an (oder geben Sie ihm Lesezugriff auf Ihres).
2. Geben Sie dem Kunden einen Zugang nur zum Lesen: einen Deploy-Key oder ein auf dieses Repository beschränktes Token.
3. Sagen Sie ihm, er soll es seinem Projekt hinzufügen:

   ```bash
   composer config repositories.mytheme vcs https://github.com/yourvendor/mytheme-pro
   composer require yourvendor/mytheme-pro:~1.0.0
   ```

   mit dem Zugang in der `auth.json` von Composer auf seiner Seite gespeichert, nie committet.
4. Taggen Sie dort jedes Release wie öffentlich ([Die Version vor dem Tag erhöhen](../veroeffentlichung/version-vor-dem-tag-erhoehen.md)).
5. Endet das Abonnement, entfernen Sie den Zugang. Sagen Sie es dem Kunden vorher.

## Regeln

- Zugangsdaten gehören in `auth.json` oder in die Umgebung des Servers, nie in ein Repository oder in einen E-Mail-Verlauf,
  den andere lesen.
- Releases sind auch hier endgültig: Verschieben Sie auch in einem privaten Repository nie einen Tag.

## Verwandte Seiten

- [Wartungsabonnements](wartungsabonnements.md)
- [Was Käufer erhalten](was-kaeufer-erhalten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
