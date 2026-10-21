---
title: Warum Sie den Ausstieg vor der Unterzeichnung planen
audience: government, project-manager
category: public-sector
order: 20
summary: Warum eine öffentliche Stelle vor der Vertragsunterzeichnung planen sollte, wie sie einen Lieferanten oder ein CMS verlässt - die Risiken ohne Plan, was die offenen Formate und die Lizenz von Exponential möglich machen, was noch Vertragsbedingungen braucht und wann der Ausstiegsplan zu überprüfen ist.
keywords: Ausstiegsplan, Ausstiegsstrategie, Anbieterabhängigkeit, Lock-in, Lieferantenwechsel, Kontinuität, Portabilität, Open Source, Vertragsende, Übergang, Umkehrbarkeit, exit plan, exit strategy, vendor lock-in, supplier change, continuity, portability, open source, contract end, transition, reversibility
type: help
---
# Warum Sie den Ausstieg vor der Unterzeichnung planen

Diese Seite richtet sich an Projektleitungen und Beschaffungsstellen. Jeder Vertrag endet, und jedes System wird eines
Tages ersetzt. Ein vor der Unterzeichnung geschriebener Ausstiegsplan kostet wenig; einer, der am Vertragsende
improvisiert wird, kostet Zeit, Geld und manchmal Daten. Diese Seite erklärt, warum, was Exponential bereits möglich macht
und was noch mit Lieferanten vereinbart werden muss.

## Inhalt
1. [Was ohne Plan schiefgehen kann](#was-ohne-plan-schiefgehen-kann)
2. [Was Exponential möglich macht](#was-exponential-möglich-macht)
3. [Was noch Vertragsbedingungen braucht](#was-noch-vertragsbedingungen-braucht)
4. [Zwei Arten des Ausstiegs](#zwei-arten-des-ausstiegs)
5. [Wann der Ausstiegsplan zu überprüfen ist](#wann-der-ausstiegsplan-zu-überprüfen-ist)

## Was ohne Plan schiefgehen kann

| Situation | Folge |
|---|---|
| Der Lieferant hält die einzige Kopie des eigens entwickelten Codes | Sie können die Website niemand anderem übergeben |
| Sicherungen enthalten die generierten Schlüssel nicht | Audit-Einträge lassen sich nicht mehr verifizieren; Abmeldelinks in E-Mails funktionieren nicht mehr ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)) |
| Niemand kennt das Inhaltsmodell oder die Einstellungen | ein neuer Lieferant muss die Website neu entdecken |
| Der Hoster löscht Daten zum Vertragsende, bevor Sie sie haben | Datenverlust |
| Alte Adressen werden nicht umgeleitet | defekte Links von anderen Websites, Suchmaschinen und Drucksachen |
| Personenbezogene Daten werden ohne Regeln exportiert | eine Datenschutzverletzung |

## Was Exponential möglich macht

- **Die Lizenz.** Die GNU GPL Version 2 oder neuer erlaubt jedem, die Software auszuführen, zu untersuchen und zu
  ändern, sodass ein anderer Lieferant dieselbe Installation übernehmen kann ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)).
- **Offene Speicherung.** „Ihre Inhalte gehören Ihnen. Exponential speichert sie in einem offenen Datenbankschema und in
  gewöhnlichen Dateien und hat Werkzeuge, die sie in einfachen Formaten ausgeben“ ([Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md)).
- **Exportwerkzeuge.** Ein selbstbeschreibendes Website-Archiv, Pakete, Datenbank-Dumps, Layouts, Formulardaten,
  Einwilligungsnachweise und das Audit-Protokoll lassen sich exportieren (siehe [Formate für den Datenexport](formate-fuer-den-datenexport.md)).
- **Dokumentierte Umzüge.** Der Umzug derselben Website zu einem anderen Hoster ist Schritt für Schritt dokumentiert
  ([Eine Website zwischen Hostern umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)).

## Was noch Vertragsbedingungen braucht

Die Software macht den Ausstieg möglich; nur der Vertrag sorgt dafür, dass er stattfindet:

- Lieferung des Quellcodes der Templates und Erweiterungen, die Sie bezahlt haben;
- Lieferung vollständiger Sicherungen, Einstellungen und Schlüssel, auf Anforderung und am Ende;
- Dokumentation des Inhaltsmodells, der Einstellungen und der Integrationen;
- Unterstützung beim Ausstieg: Stunden, Sätze und Dauer nach Vertragsende;
- Zeitpunkt der Datenlöschung durch den Lieferanten, nachdem Sie den Empfang bestätigt haben.

Siehe [Zu vermeidende Lock-in-Klauseln](zu-vermeidende-lock-in-klauseln.md).

## Zwei Arten des Ausstiegs

| Ausstieg | Sie behalten | Seiten |
|---|---|---|
| Wechsel des Lieferanten oder Hosters, dieselbe Software | die Website, wie sie ist | [Wechsel zu einem anderen Anbieter](wechsel-zu-einem-anderen-anbieter.md) |
| Wechsel des Systems | die Inhalte, Benutzer und Adressen | [Wechsel zu einem anderen System](wechsel-zu-einem-anderen-system.md) |

Die meisten Ausstiege sind von der ersten Art. Planen Sie für beide.

## Wann der Ausstiegsplan zu überprüfen ist

- Bei der Vertragsunterzeichnung (der Plan ist eine Anlage).
- Nach dem Livegang, sobald die echte Sicherung und der echte Export getestet wurden.
- Jedes Jahr und nach jeder größeren Änderung (neue Integration, neue Website, neue Erweiterung).
- Vor Vertragsende, früh genug, um eine neue Ausschreibung in der Zeit durchzuführen, die Ihre Vergaberegeln verlangen.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Vorlage für einen Ausstiegsplan](vorlage-fuer-einen-ausstiegsplan.md)
- [Beschaffung: Übersicht](../README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
