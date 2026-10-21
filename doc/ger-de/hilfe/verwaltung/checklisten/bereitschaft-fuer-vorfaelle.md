---
title: Bereitschaft für Vorfälle
audience: government, admin, hosting, project-manager
category: public-sector
order: 140
summary: Was eine öffentliche Stelle bereithält, bevor ein Vorfall ihre Exponential-Website trifft - Kontakte, Runbooks, Wartungsmodus, Protokolle und Audit, Datensicherungen, Kommunikation und Meldepflichten -, damit die erste Stunde mit Handeln verbracht wird, nicht mit Suchen.
keywords: Vorfall, Sicherheitsvorfall, Reaktion auf Vorfälle, Bereitschaft, Ausfall, Kompromittierung, Runbook, Wartungsmodus, Prüfprotokoll, Protokolle, Kontakte, Kommunikation, NIS2, Meldung, öffentlicher Sektor, incident, incident response, readiness, outage, compromise, runbook, maintenance mode, audit trail, logs, contacts, communication, reporting, public sector
type: help
---
# Bereitschaft für Vorfälle

Diese Checkliste richtet sich an die Verantwortlichen der Website, die Website-Administration und den Hoster einer
öffentlichen Stelle. Ein Vorfall ist jedes Ereignis, das die Website lahmlegt, ihre Daten beschädigt oder sie einem
Angriff aussetzt. Die Runbooks für Hoster enthalten die technischen Schritte
([Runbooks für Hoster](../../hosting/runbooks/README.md)); diese Seite prüft, ob Ihre Organisation bereit ist, sie zu
nutzen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Meldepflichten (zum
> Beispiel nach der [NIS2-Richtlinie](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) oder den Regeln der DSGVO zur
> Meldung von Datenschutzverletzungen) hängen von Ihrer Organisation ab; prüfen Sie sie mit Ihrer Rechtsberatung.

## Inhalt

1. [Menschen und Kontakte](#menschen-und-kontakte)
2. [Runbooks](#runbooks)
3. [Werkzeuge, zu denen Sie greifen können](#werkzeuge-zu-denen-sie-greifen-können)
4. [Beweise](#beweise)
5. [Kommunikation und Meldung](#kommunikation-und-meldung)
6. [Übung](#übung)

## Menschen und Kontakte

- [ ] Eine benannte Leitung für Vorfälle und eine Vertretung, auch außerhalb der Bürozeiten erreichbar.
- [ ] Kontakte des Hosters, des Supportdienstleisters und Ihres Sicherheitsteams, mit den Zeiten, die sie abdecken.
- [ ] Wer entscheiden darf, die Website vom Netz zu nehmen.
- [ ] Die Kontaktliste außerhalb der Website selbst aufbewahrt (die Website kann ausgefallen sein).

## Runbooks

- [ ] Der Hoster weiß, wo seine Runbooks liegen: [Website ausgefallen](../../hosting/runbooks/website-nicht-erreichbar.md), [Langsame Website](../../hosting/runbooks/langsame-website.md), [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md), [Festplatte voll](../../hosting/runbooks/festplatte-voll.md), [Aus einer Datensicherung wiederherstellen](../../hosting/runbooks/aus-einem-backup-wiederherstellen.md), [Ablauf von Zertifikaten](../../hosting/runbooks/ablauf-von-zertifikaten.md), [Velocity startet nicht](../../hosting/runbooks/velocity-startet-nicht.md).
- [ ] Ihre websitespezifischen Ergänzungen schriftlich festgehalten: wo sie läuft, welche Engine, welche Datenbank.
- [ ] [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md) ist der Website-Administration bekannt.

## Werkzeuge, zu denen Sie greifen können

- [ ] **Wartungsmodus**: **Setup > Wartung** oder von einer Shell `php bin/php/maintenance.php on --message="..." --until=2h --allow-admin`; das Löschen von `var/maintenance.json` beendet ihn, wenn nichts anderes mehr funktioniert ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).
- [ ] **Sitzungen**: Melden Sie nach einer vermuteten Kompromittierung eines Kontos unter **Setup > Sitzungen** jeden Browser ab ([Sitzungen](../../../anleitungen/sitzungen.md)).
- [ ] **Konten**: Deaktivieren Sie ein kompromittiertes Konto ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).
- [ ] **Caches**: Leeren Sie nach einer Korrektur den richtigen Cache, `php bin/php/ezcache.php --clear-id=<id>` oder **Setup > Cache-Verwaltung** ([Caches](../../../anleitungen/caches.md)).
- [ ] **Fehlerreferenzen**: Fehlermeldungen für Besucher tragen eine Referenz, die Sie nachschlagen können ([Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).
- [ ] **Anfragefilter**: request-shield kann missbräuchliche Clients vor dem Kernel sperren oder bremsen ([Sicherheitshärtung](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Beweise

- [ ] Das Prüfprotokoll ist eingeschaltet und seine Schlüssel sind gesichert; prüfen Sie seine Kette mit `./console exp:audit verify` ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).
- [ ] `var/log/security.log` zeichnet Sondierungen und Angriffe auf ([Anfrageprotokolle](../../../funktionen/6.0/anfrageprotokolle.md)).
- [ ] Protokolle werden lange genug für eine Untersuchung aufbewahrt und während eines Vorfalls vom Server kopiert ([Protokollanzeige](../../../funktionen/6.0/log-betrachter.md)).
- [ ] Bekannt ist, dass ein kompromittierter Server nicht gelöscht wird, bevor die Beweise gesichert sind ([Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)).

## Kommunikation und Meldung

- [ ] Ein vorbereiteter Hinweis für die Wartungsseite der Website und für soziale Medien.
- [ ] Ihre Meldepflichten aufgelistet, mit Fristen und Empfängern.
- [ ] Ein Weg, Schwachstellen der Software vertraulich an das Projekt zu melden, wie `SECURITY.md` es beschreibt ([Sicherheitsstandards](../../../standards/sicherheit.md#offenlegung-von-schwachstellen)).

## Übung

- [ ] Ein Wiederherstellungstest in diesem Jahr ([Bereitschaft für Datensicherungen](bereitschaft-fuer-datensicherungen.md)).
- [ ] Eine Planspielübung: Gehen Sie ein Runbook mit den Menschen auf der Kontaktliste durch.
- [ ] Erkenntnisse aus jedem Vorfall festgehalten und die Checkliste aktualisiert.

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Bereitschaft für Notfallwarnungen](bereitschaft-fuer-notfallwarnungen.md)
- [Checkliste Sicherheit](checkliste-sicherheit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
