---
title: Zu Exponential wechseln
audience: businesses, project-manager, admin
category: business-solutions
order: 70
summary: Was ein Unternehmen wissen muss, bevor es eine Website auf Exponential 6 umzieht - von einem älteren Release aus den Jahren von eZ Publish, von eZ Platform oder Ibexa, von einem anderen Hoster oder von einem anderen System wie WordPress oder Drupal - die Wege, die das Installationshandbuch dokumentiert, die Aufwandstreiber, ein Plan in Phasen und die Entscheidungen, die nur Sie treffen können.
keywords: Wechsel, Migration, zu Exponential migrieren, eZ Publish 4.x, 5.x, eZ Platform, Ibexa, WordPress, Drupal, Joomla, Magento, Shopify, Hoster wechseln, Staging, Rückfallplan, Umstellung, Weiterleitungen, Plan, switching, migration, migrate to Exponential, move host, staging, rollback, cut-over, redirects, plan
type: help
---
# Zu Exponential wechseln

Diese Seite ist für Inhaber und Führungskräfte, die erwägen, eine bestehende Website auf Exponential 6 umzuziehen, und
für den Projektleiter, der das plant. Sie erklärt die dokumentierten Wege, was den Aufwand bestimmt und wie Sie einen
Wechsel planen, der das Geschäft nicht gefährdet. Die technischen Schritte stehen im Installationshandbuch; diese Seite
sagt Ihnen, welches Kapitel passt und was Sie entscheiden müssen.

## Inhalt

1. [Welcher Weg Ihrer ist](#welcher-weg-ihrer-ist)
2. [Warum jetzt wechseln](#warum-jetzt-wechseln)
3. [Was den Aufwand bestimmt](#was-den-aufwand-bestimmt)
4. [Ein Plan in Phasen](#ein-plan-in-phasen)
5. [Entscheidungen, die nur Sie treffen können](#entscheidungen-die-nur-sie-treffen-können)
6. [Risiken und wie Sie sie verringern](#risiken-und-wie-sie-sie-verringern)

## Welcher Weg Ihrer ist

| Ihre Website läuft heute auf | Weg | Lesen |
|---|---|---|
| Exponential 6.0.x, und nur der Hoster ändert sich | ein Umzug, keine Migration | [Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md) |
| Einem älteren 6.0.x-Release | ein Update innerhalb der Linie | [Upgrade, Teil C](../../../anleitungen/aktualisieren.md#teil-c-von-einer-beliebigen-60x-auf-den-aktuellen-stand) |
| 4.x (aus den Jahren von eZ Publish) | eine Migration auf 6 | [14. Migration von der 4.x-Linie](../../../installation/14-migration-von-4x.md) |
| 5.x mit dem Legacy-Kernel | eine Migration auf 6 | [15. Migration vom 5.x-Legacy-Stack](../../../installation/15-migration-von-5x-legacy.md) |
| eZ Platform oder Ibexa DXP/OSS | zwei Ziele: Exponential Platform Nexus oder der Legacy-Kernel von Exponential 6.0 | [16. Migration von eZ Platform und Ibexa](../../../installation/16-migration-von-ez-platform-und-ibexa.md) |
| WordPress, Drupal, Joomla, Magento, Shopify, Contentful, AEM, Website-Baukästen, statischem HTML | eine Migration der Inhalte mit Export und Import | [Migration zu und von Exponential](../../../installation/migration/README.md) |

Die Sicht der Hoster auf dieselben Wege steht in [Kunden migrieren: Überblick](../../hosting/migrationen/README.md).

Für die Migration der Inhalte aus einem anderen System liest die Erweiterung 7x SQLI Import die Exportdateien von
WordPress, Drupal, Joomla, Shopify und WooCommerce, Tabellen und APIs, ordnet sie ohne Programmierung Ihren
Inhaltstypen zu und zeigt einen Probelauf, bevor etwas veröffentlicht wird
([7x SQLI Import: hier beginnen](../../sqli-import/README.md)).

## Warum jetzt wechseln

- **Alte Versionen bekommen keine Sicherheitskorrekturen.** „5.x, 4.x, 3.x and older“ werden nicht unterstützt; 6.0
  ist die gepflegte Linie (`SECURITY.md`). Eine Website aus den Jahren von eZ Publish läuft ohne Korrekturen.
- **Aktuelles PHP.** Exponential unterstützt PHP 8.0 bis 8.5 (`README.md`), sodass die Website auch auf ein
  unterstütztes PHP wechseln kann.
- **Ein gemeinsames Erbe in der Datenbank.** Bei Websites mit 4.x und 5.x Legacy werden das Inhaltsmodell und ein
  Großteil der Datenbank übernommen; die Kapitel erklären, was sich ändert und was nicht
  ([14.1 Was sich ändert und was nicht](../../../installation/14-migration-von-4x.md#141-was-sich-ändert-und-was-nicht)).
- **Keine Lizenzgebühr** nach dem Wechsel (`README.md`, „License“) und keine Anbieterbindung
  ([Wofür Sie nicht bezahlen](../kosten/wofuer-sie-nicht-bezahlen.md)).

## Was den Aufwand bestimmt

Aus dem Installationshandbuch (zum Beispiel [14.15 Wie lange es dauert](../../../installation/14-migration-von-4x.md#1415-wie-lange-es-dauert)):

| Treiber | Warum er wichtig ist |
|---|---|
| Größe der Inhaltstabellen | Dump, Wiederherstellung, Konvertierungen und Neuindizierung wachsen mit den Zeilen |
| Anzahl und Größe der gespeicherten Dateien | das Kopieren von `var/` braucht Zeit |
| Ihre eigenen Erweiterungen und Ihr PHP-Code | die Portierung auf PHP 8 wächst mit den Zeilen eigenen Codes, nicht mit der Größe der Datenbank |
| Templates und Design | alte Designs brauchen vielleicht Überarbeitung; Twig-Templates von eZ Platform werden auf dem Legacy-Kernel zu TPL |
| Suche | die Neuindizierung ist oft der längste einzelne Schritt |
| Sprachen und URLs | URL-Aliase pro Knoten und Sprache müssen neu aufgebaut und geprüft werden |
| Integrationen | Zahlung, Single Sign-on, Feeds und APIs müssen neu getestet werden |

Bei einem anderen System liegt der Aufwand darin, seine Inhalte auf das Inhaltsmodell von Exponential abzubilden und
URLs mit Weiterleitungen funktionsfähig zu halten ([Migration zu und von Exponential](../../../installation/migration/README.md)).

> **Hinweis**
> Das Projekt nennt keine Stunden- oder Preisangaben für eine Migration, und diese Seite auch nicht. Messen Sie die
> Dauer in einem Probelauf und planen Sie anhand der Messung, wie das Installationshandbuch empfiehlt.

## Ein Plan in Phasen

1. **Bestandsaufnahme.** Listen Sie Inhaltstypen, Sprachen, Erweiterungen, Integrationen und Besucheraufkommen auf.
   Die Kapitel des Handbuchs beginnen mit diesem Schritt.
2. **Wählen Sie den Weg und bei Bedarf einen Partner** ([Einen Partner auswählen](einen-partner-auswaehlen.md)).
3. **Proben Sie auf einer Staging-Kopie.** Führen Sie die gesamte Migration auf einer Kopie durch und messen Sie jeden
   Schritt.
4. **Prüfen Sie.** Kontrollieren Sie Inhalte, URLs, Formulare, den Shop und Anmeldungen auf der Kopie; die Kapitel
   haben Abschnitte zur Prüfung und Checklisten.
5. **Planen Sie die Umstellung.** Ein Bearbeitungsstopp für Inhalte, ein Wartungsfenster mit Wartungsmodus und der
   letzte Durchlauf.
6. **Halten Sie einen Rückfallplan bereit.** Die alte Website und ihre Backups bleiben bereit, bis die neue abgenommen
   ist ([14.14 Rückfallplan](../../../installation/14-migration-von-4x.md#1414-rollback-plan)).
7. **Gehen Sie live** mit der [Checkliste Go-live](../vorlagen/checkliste-go-live.md).
8. **Nachsorge.** Beobachten Sie in den ersten Wochen Logs, 404-Fehler und die Suche.

## Entscheidungen, die nur Sie treffen können

- [ ] Welche Inhalte umziehen und welche archiviert oder verworfen werden.
- [ ] Ob das Design beibehalten oder zur selben Zeit erneuert wird (beides gleichzeitig erhöht das Risiko).
- [ ] Wann der Bearbeitungsstopp und das Wartungsfenster stattfinden, mit Blick auf Ihre geschäftigen Zeiten.
- [ ] Wer das Ergebnis abnimmt und nach welchen Kriterien
      ([Qualitätsschranken und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md)).
- [ ] Wie Sie Kunden und Mitarbeiter informieren.
- [ ] Bei Websites mit eZ Platform und Ibexa: welches der beiden Ziele passt.

## Risiken und wie Sie sie verringern

| Risiko | Verringern Sie es durch |
|---|---|
| Verlorene Inhalte oder kaputte Links | Probelauf, Prüflisten, Weiterleitungen für geänderte URLs |
| Längere Ausfallzeit als geplant | gemessenen Probelauf, ein Fenster mit Reserve |
| Eigener Code, der unter PHP 8 nicht läuft | Portierung vor der Umstellung ([14.9](../../../installation/14-migration-von-4x.md#149-eigene-extensions-von-php-5-auf-php-8-portieren)) |
| Passwörter, die nicht mehr funktionieren | die Abschnitte der Kapitel zu Benutzern und Passwort-Hashes |
| Unvollständige Suche nach dem Wechsel | eingeplante Neuindizierung, oder Abschluss nach dem Wechsel, wenn das vertretbar ist |
| Kein Weg zurück | einen getesteten Rückfallplan und aufbewahrte Backups |

## Verwandte Seiten

- [17. Migrationsreferenz](../../../installation/17-migrationsreferenz.md)
- [Kunden von älteren Versionen migrieren (für Hoster)](../../hosting/migrationen/kunden-von-aelteren-versionen-migrieren.md)
- [Budget für Aktualisierungen und Wartung](../kosten/budget-fuer-aktualisierungen-und-wartung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
