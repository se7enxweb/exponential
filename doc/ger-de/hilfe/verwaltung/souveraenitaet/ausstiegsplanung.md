---
title: Ausstiegsplanung
audience: government, project-manager, admin
category: public-sector
order: 210
summary: Wie Sie einen Ausstieg planen, dokumentieren und testen - eine Exponential-Website zu einem anderen Dienstleister oder Host umziehen oder von Exponential weg migrieren, mit dem, was Sie zusammentragen, wen Sie einbeziehen, und einem Test, der beweist, dass der Plan funktioniert.
keywords: Ausstiegsplan, Ausstiegsstrategie, Exit, Dienstleister wechseln, Migration, Übergabe, Datensicherung, Wiederherstellungstest, Export, Vertragsende, Souveränität, Kontinuität, exit plan, exit strategy, change provider, handover, backup, restore test, contract end, sovereignty, continuity
type: help
---
# Ausstiegsplanung

Diese Seite richtet sich an Projektleitungen und Administratoren. Eine öffentliche Stelle sollte jeden Dienstleister
und am Ende auch jede Plattform geordnet verlassen können. Bei Exponential erlaubt die Lizenz das; der Plan macht es
praktikabel. Der Bereich Beschaffung dieser Hilfe enthält Vorlagen für Ausstiegspläne
([Ausstiegspläne](../beschaffung/ausstiegsplaene/README.md)).

## Inhalt

1. [Zwei Arten von Ausstieg](#zwei-arten-von-ausstieg)
2. [Was Sie zusammentragen](#was-sie-zusammentragen)
3. [Dienstleister oder Host wechseln](#dienstleister-oder-host-wechseln)
4. [Die Plattform verlassen](#die-plattform-verlassen)
5. [Den Plan testen](#den-plan-testen)

## Zwei Arten von Ausstieg

| Ausstieg | Aufwand | Hauptrisiko |
|---|---|---|
| Gleiche Plattform, anderer Dienstleister oder Host | gering bis mittel | fehlende Teile (Code, Einstellungen, Dateien, Konten) |
| Eine andere Plattform | hoch | Konvertierung der Inhalte, besonders von Rich Text, und verlorene Funktionen |

## Was Sie zusammentragen

- Das Code-Repository mit jeder eigens entwickelten Erweiterung und jedem Design, und `composer.lock`.
- Die Overrides der Einstellungen (`settings/override/`, Einstellungen der Siteaccesses), wobei Geheimnisse getrennt und
  sicher übergeben werden.
- Einen Datenbank-Dump und die Dateien unter `var/<site>/storage/`
  ([Datensicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
- Die Audit-Einträge und ihre Schlüssel, wenn Sie sie aufbewahren müssen
  (`settings/override/audit.ini.append.php`).
- Die Liste der Cronjobs, die E-Mail-Einstellungen, DNS, Zertifikate und Konten.
- Das Dokument zur Installation (siehe „Das Wissen im Haus halten“).

## Dienstleister oder Host wechseln

Der Umzug einer Installation zwischen Hosts ist in der Hilfe zum Hosting dokumentiert
([Eine Website zwischen Hosts umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)). Kurz gesagt: das
Veröffentlichen einfrieren, die Datensicherung erstellen, beim neuen Host wiederherstellen, DNS umstellen, prüfen, den
alten Host für eine gewisse Zeit schreibgeschützt behalten. Die Datenbank kann bei Bedarf im selben Umzug wechseln
([Datenbankunterstützung](../../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)).

## Die Plattform verlassen

1. Erfassen Sie die Inhaltsklassen und die genutzten Funktionen (Workflows, Formulare, Sprachen).
2. Exportieren Sie die Inhalte pro Klasse (siehe „Inhalte und Pakete exportieren“) und die Dateien.
3. Planen Sie die Konvertierung des Rich-Text-XML in das Zielformat.
4. Behalten Sie die Adressen: Erstellen Sie eine Weiterleitungstabelle von den alten URL-Aliassen zu den neuen Adressen
   ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md#relaunch-einer-website-ein-weiterleitungsplan)).
5. Archivieren Sie, was als Schriftgut aufbewahrt werden muss, bevor das alte System abgeschaltet wird.

## Den Plan testen

Einmal im Jahr oder vor einer Vertragsverlängerung: Stellen Sie die letzte Datensicherung auf einem getrennten Rechner
allein anhand des dokumentierten Materials wieder her, durch jemanden, der die Website nicht eingerichtet hat. Halten
Sie fest, wie lange es gedauert hat und was gefehlt hat, und verbessern Sie die Dokumentation.

## Verwandte Seiten

- [Anbieterabhängigkeit vermeiden](anbieterabhaengigkeit-vermeiden.md)
- [Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)
- [Abschied von Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
