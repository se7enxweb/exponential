---
title: Lastspitzen planen
audience: businesses, admin, project-manager, sales-marketing
category: business-solutions
order: 130
summary: Wie Sie eine Exponential-Website auf einen Sale, eine Kampagne, einen Newsletter-Versand oder eine Erwähnung in der Presse vorbereiten, vom Kalender und einer Messung Ihrer eigenen Seiten über Caches, Vorwärmen und einen Änderungsstopp bis zu einem Plan für den Tag selbst.
keywords: Lastspitze, Besucheransturm, Sale, Kampagne, Newsletter-Versand, Kapazität, Benchmark, vorladen, vorwärmen, Änderungsstopp, Last, peak traffic, traffic spike, sale, campaign, newsletter send, capacity, benchmark, exp:benchmark, preload, warm, change freeze, load
type: help
---
# Lastspitzen planen

Diese Seite richtet sich an Inhaber, Marketingverantwortliche und den Betrieb, die wissen, dass ein arbeitsreicher Tag
bevorsteht: ein saisonaler Sale, eine Produkteinführung, ein Newsletter an alle Mitglieder, eine Erwähnung im Fernsehen
oder in der Presse. Sie gibt einen Plan, der Wochen vorher beginnt und mit einer Nachbesprechung endet. Shop-Inhaber
finden die shopspezifischen Einzelheiten unter
[Shop-Leistung zu Spitzenzeiten](../shops/shop-leistung-zu-spitzenzeiten.md).

## Inhalt

1. [Kennen Sie Ihre Spitzen](#kennen-sie-ihre-spitzen)
2. [Messen statt raten](#messen-statt-raten)
3. [Was sich unter Last ändert](#was-sich-unter-last-ändert)
4. [Wochen vorher](#wochen-vorher)
5. [Tage vorher](#tage-vorher)
6. [Am Tag selbst](#am-tag-selbst)
7. [Nach der Spitze](#nach-der-spitze)
8. [Checkliste](#checkliste)

## Kennen Sie Ihre Spitzen

Viele Spitzen verursacht das Unternehmen selbst, und das heißt, sie lassen sich planen:

| Spitze | Wer zuerst davon weiß |
|---|---|
| Ein Sale oder eine Rabattaktion | Vertrieb und Marketing |
| Ein Newsletter oder Mailing an alle Mitglieder ([Newsletter](../newsletter/README.md)) | Wer ihn versendet; der Versand lässt sich auf eine ruhige Stunde legen |
| Eine Produkteinführung oder der Start einer Veranstaltungsanmeldung | Die Projektleitung |
| Eine Erwähnung in Presse oder Rundfunk | Die Kommunikation; oft mit wenig Vorlauf |
| Saisongeschäft (Feiertage, Beginn eines Schuljahres, Steuerfristen) | Alle, aus dem Vorjahr |

Tragen Sie diese Termine in einen gemeinsamen Kalender ein, den der Betrieb liest. Eine Spitze, von der niemand dem
Betrieb erzählt hat, ist die häufigste Ursache für einen schlechten Tag.

## Messen statt raten

Keine allgemeine Zahl sagt Ihnen, was Ihre Website tragen kann; das entscheiden Ihre Seiten, Ihre Hardware und Ihre
Datenbank. Exponential liefert ein Messwerkzeug mit, `exp:benchmark` ([Benchmarks](../../../anleitungen/benchmarking.md#kurz-gesagt)):

- `php bin/php/console exp:benchmark --allow-root-user` ruft die Startseite, Menüseiten, die Suchseite und die
  Anmeldeseite der Administration ab und gibt Median-, p90- und p99-Zeiten aus.
- `--cold` misst gerenderte Seiten statt Cache-Treffern, was näher an dem liegt, was angemeldete Kunden bekommen.
- `--save=run.json` behält einen Lauf, und `--baseline=run.json` vergleicht damit und endet bei einer Verschlechterung
  mit 1.

Die Anleitung warnt: „Ein einzelner Lauf auf einem ausgelasteten Rechner beweist wenig: Vergleichen Sie Läufe, die
direkt nacheinander gemacht wurden, mit denselben Zahlen, denselben Seiten und derselben PHP-Konfiguration.“ Messen Sie
an einer Kopie der Website oder zu einer ruhigen Stunde, und halten Sie das Ergebnis als Ihre Ausgangsbasis fest.

## Was sich unter Last ändert

| Besucher | Was sie bedient | Was zu planen ist |
|---|---|---|
| Anonyme Besucher auf öffentlichen Seiten | Caches: der Antwortcache, der statische Cache, der View-Cache ([Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md)) | Stellen Sie sicher, dass die Caches eingeschaltet und vorgewärmt sind |
| Angemeldete Kunden, Warenkörbe, Bestellvorgang, Formulare | Gerenderte Seiten; der Antwortcache bedient angemeldete Besucher nie | Genügend PHP-Worker und Datenbankkapazität |
| Suche | Die Suchmaschine oder die eingebaute Suche | Testen Sie die Suche unter Last; sie ist oft die langsamste Seite |
| Redakteure, die während der Spitze veröffentlichen | Veröffentlichungsarbeit, Leeren von Caches | Vermeiden Sie umfangreiche Veröffentlichungen während der Spitze |

Unter SQLite stehen Schreibzugriffe für eine einzige Sperre an; „Für viele Redakteure, die gleichzeitig veröffentlichen,
verwenden Sie MySQL oder PostgreSQL“
([Betrieb einer Site, Ihren Datenbanktreiber kennen](../../../anleitungen/betrieb-einer-website.md#7-den-eigenen-datenbanktreiber-kennen)).
Auch ein stark genutzter Bestellvorgang im Shop schreibt; siehe
[Datenbankwahl für Zuverlässigkeit](datenbankwahl-fuer-zuverlaessigkeit.md).

## Wochen vorher

- [ ] Die Spitze steht im gemeinsamen Kalender, mit voraussichtlicher Uhrzeit und Dauer.
- [ ] Eine Ausgangsmessung existiert, ebenso eine Messung der Seiten, auf die die Kampagne verlinkt.
- [ ] **Setup > Systeminformationen** zeigt keine fehlgeschlagene Prüfung, OPcache ist eingeschaltet, und die
      Speicherlimits sind nicht zu niedrig
      ([Systeminformationen, Zustandsprüfungen](../../../anleitungen/systeminformationen.md#3-zustandsprüfungen)).
- [ ] Die Landingpages sind öffentliche Seiten, die sich cachen lassen; persönliche Inhalte bleiben von ihnen fern.
- [ ] Läuft die Website auf Velocity, sind Worker-Anzahl und Antwortcache mit Ihrem Betrieb durchgesehen
      ([Velocity für Unternehmen](velocity-fuer-unternehmen.md)).
- [ ] Reicht ein Rechner nicht, ist der Hosting-Partner frühzeitig gefragt: Server hinzuzufügen braucht Zeit
      ([Clustering und Hochverfügbarkeit](clustering-und-hochverfuegbarkeit.md)).
- [ ] Jede Aktualisierung ist lange vorher erledigt, nicht in der letzten Woche
      ([Aktualisierungen ohne Überraschungen](aktualisierungen-ohne-ueberraschungen.md)).

## Tage vorher

- [ ] **Änderungsstopp**: keine Deployments, Aktualisierungen oder Änderungen an Einstellungen ab einem vereinbarten Datum
      bis nach der Spitze.
- [ ] Ein frisches Backup ist angelegt, und die letzte Wiederherstellungsübung war erfolgreich
      ([Wiederherstellungsübungen](wiederherstellungsuebungen.md)).
- [ ] Die Inhalte der Kampagne sind veröffentlicht und über die Vorschau geprüft
      ([Vorschau vor dem Veröffentlichen](vorschau-vor-dem-veroeffentlichen.md)).
- [ ] Schwere geplante Arbeit (Importe, Neuaufbau von Indizes, große Inhaltsjobs) ist aus den Spitzenstunden
      herausverlegt ([Cronjobs und Hintergrundjobs](cronjobs-und-hintergrundjobs.md)).
- [ ] Der Störungsplan ist ausgedruckt, und die diensthabenden Personen sind benannt
      ([Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md)).

## Am Tag selbst

1. **Wärmen Sie die Caches vor**, bevor die Spitze beginnt, damit die ersten Besucher nicht für das Rendern bezahlen
   ([Caches vorwärmen](../../../anleitungen/caches-vorwaermen.md)):

   ```bash
   php bin/php/preload.php --siteaccess=site --allow-root-user
   ```

   Verwenden Sie den Namen Ihres eigenen öffentlichen Siteaccess.
2. **Beobachten Sie**: den externen Monitor, die Fehlerübersicht und unter Velocity das Dashboard unter `/Q/dashboard`,
   das laufende Anfragen, Worker und Statuscodes zeigt
   ([Velocity Control Panel](../../../../eng-us/features/6.0/velocity-control-panel.md)).
3. **Leeren Sie nicht alle Caches** während der Spitze, es sei denn, etwas stimmt nicht; jedes vollständige Leeren lässt
   die nächsten Besucher auf das Rendern warten.
4. **Halten Sie eine Ausweichnachricht bereit**: Wenn die Website nicht mithalten kann, ist ein kurzes Wartungsfenster
   mit einer klaren Nachricht und einem voraussichtlichen Ende besser als Fehler
   ([Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md)).

## Nach der Spitze

- Vergleichen Sie, was passiert ist, mit dem Plan: Antwortzeiten aus dem Monitoring, Fehler in der Übersicht,
  Beschwerden von Kunden.
- Notieren Sie die verkehrsreichste Stunde und wie sich die Website verhalten hat; das ist die Planungsgrundlage für
  das nächste Jahr.
- Heben Sie den Änderungsstopp auf und holen Sie verschobene Arbeit nach.
- Hatte die Website zu kämpfen, besprechen Sie mit Ihrem Partner, ob Caching, Worker, die Datenbank oder mehr Server
  helfen würden, und messen Sie nach jeder Änderung erneut.

## Checkliste

- [ ] Die Termine der Spitzen sind dem Betrieb Wochen vorher bekannt.
- [ ] Eine gemessene Ausgangsbasis existiert; die Kampagnenseiten wurden gemessen.
- [ ] Die Caches sind vor der Spitze eingeschaltet und vorgewärmt.
- [ ] Ein Änderungsstopp und ein frisches Backup decken die Spitze ab.
- [ ] Jemand beobachtet während der Spitze und kennt den Störungsplan.
- [ ] Danach wird eine kurze Nachbesprechung geschrieben.

## Verwandte Seiten

- [Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md)
- [Shop-Leistung zu Spitzenzeiten](../shops/shop-leistung-zu-spitzenzeiten.md)
- [Runbook: langsame Website](../../hosting/runbooks/langsame-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
