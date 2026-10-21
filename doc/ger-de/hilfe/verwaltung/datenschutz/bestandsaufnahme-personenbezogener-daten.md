---
title: Wo personenbezogene Daten in Exponential liegen
audience: government, admin, project-manager, developer
category: public-sector
order: 40
summary: Eine Bestandsaufnahme für öffentliche Stellen über die Orte, an denen eine Exponential-Installation personenbezogene Daten speichert - Benutzerkonten, Besuche, Sitzungen, Formulareinsendungen, E-Mail-Einstellungen und Einwilligungsprotokoll, Newsletter-Daten, Audit-Trail, Protokolle, Suchbegriffe, Bestellungen, Inhalte und Datensicherungen - mit dem Werkzeug, das jeden davon liest, exportiert oder entfernt.
keywords: personenbezogene Daten, Datenbestand, Datenlandkarte, Benutzerkonten, ezuser, ezuservisit, Sitzungen, Informationssammlung, Einwilligungsprotokoll, Newsletter, Audit-Trail, Protokolle, Suchstatistiken, Bestellungen, Datensicherungen, Verzeichnis von Verarbeitungstätigkeiten, öffentlicher Sektor, personal data, data inventory, data map, records of processing
type: reference
---
# Wo personenbezogene Daten in Exponential liegen

Diese Seite richtet sich an Datenschutzbeauftragte, Administratoren und Entwickler, die die Datenlandkarte einer
Website erstellen. Jede Zeile nennt einen Ort, an dem eine Installation personenbezogene Daten aufbewahrt, das
Werkzeug, das sie liest oder entfernt, und die standardmäßig geltende Aufbewahrung. Sie ist die Grundlage für das
Verzeichnis von Verarbeitungstätigkeiten, für Auskunftsanträge und für den Aufbewahrungsplan.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Bestandsaufnahme](#die-bestandsaufnahme)
2. [Orte außerhalb von Exponential](#orte-außerhalb-von-exponential)
3. [Die Bestandsaufnahme aktuell halten](#die-bestandsaufnahme-aktuell-halten)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Bestandsaufnahme

| Ort | Enthält | Lesen / exportieren | Entfernen | Standardaufbewahrung |
|---|---|---|---|---|
| Benutzerkonten (Benutzer-Inhaltsobjekte, `ezuser`) | Name, Anmeldename, E-Mail, was immer die Benutzerklasse hinzufügt | der Knoten des Benutzers; Benutzer > Benutzerkonten prüfen (CSV) | Entfernen am Knoten (endgültig, kein Papierkorb) | keine |
| Besuche (`ezuservisit`) | letzter Besuch, Zahl der Anmeldungen | Benutzerkonten prüfen | mit dem Konto | keine |
| Sitzungen | Sitzungsdaten während der Anmeldung | Setup > Sitzungen (mit `ezpSessionHandlerDB`) | Setup > Sitzungen; Zeitüberschreitung | `SessionTimeout` (3 Tage) |
| Formulareinsendungen (Informationssammlungen) | was Besucher eingegeben haben | Setup > Gesammelte Informationen, CSV-Export | Setup > Gesammelte Informationen, Entfernen | keine: manuell |
| E-Mail-Einstellungen und Einwilligungsprotokoll | Adresse, Entscheidungen, Einwilligungsverlauf mit IP-Adresse | Setup > E-Mail-Einstellungen; `exp:mail:preferences export` | `exp:mail:preferences erase` | Einwilligungsprotokoll 1095 Tage für Personen, die nicht mehr da sind |
| Sperrliste | gesalzener Hash einer Adresse | Prüfen durch Eingabe der Adresse | Aufheben | standardmäßig unbegrenzt |
| Newsletter (cjw_newsletter, falls aktiv) | Abonnenten, Status, Statistiken pro Person mit Einwilligung | Newsletter-Benutzerliste, Abonnentenexport | Löschung der Person; Newsletter-Werkzeuge | je nach Erweiterung |
| Audit-Trail | Handelnde, gekürzte Adressen, Handlungen an Benutzern | Reiter Audit; `exp:audit export --subject-user=<id>` | nicht gelöscht; nach 90 Tagen pseudonymisiert | 90 Tage live, 730 archiviert |
| Protokolle (`404.log`, `security.log`, `auth.log`, `error.log` ...) | maskierte Adressen, User-Agents, URLs | Setup > Logdateien (maskiert) | Rotation und Bereinigung | 30 / 90 / 30 Tage für 404, Sicherheit, CSP; andere nur nach Größe |
| Suchstatistiken | Suchbegriffe, die Besucher eingegeben haben | Setup > Suchstatistiken | Statistik zurücksetzen | keine; standardmäßig aus |
| Shop-Bestellungen (falls genutzt) | Kundendaten | Shop-Ansichten | Shop-Werkzeuge | keine |
| Inhalte | Namen und Bilder von Personen in veröffentlichten Inhalten | Inhaltsbaum | Redakteure | Ihr Aufbewahrungsplan |
| Papierkorb | entfernte Inhalte, einschließlich der darin enthaltenen personenbezogenen Daten | `content/trash` | Papierkorb leeren | bis er geleert wird |
| Versionen | frühere Versionen von Inhalten | Seite der Versionen | Versionen entfernen | Grenze der Versionsgeschichte |
| Datensicherungen | alles oben Genannte | Ihr Sicherungssystem | Ihre Rotation | Ihre Rotation |

Quellen: [Datenschutzstandards](../../../../eng-us/standards/privacy.md), [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md),
[Sitzungen](../../../anleitungen/sitzungen.md), [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md),
[E-Mail-Einstellungen für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md), [Audit](../../../../eng-us/bc/6.0/audit.md),
[Protokollrotation](../../../../eng-us/features/6.0/log-rotation.md), [URLs und Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken),
[Die Ansicht des Papierkorbs](../../../../eng-us/bc/6.0/trash.md).

## Orte außerhalb von Exponential

- Die Zugriffs- und Fehlerprotokolle des Webservers und von PHP-FPM.
- Die eigenen Protokolle und der Antwort-Cache von Velocity.
- Das Protokoll von request-shield, falls genutzt.
- Protokolle des Mailservers und Postfächer für Unzustellbarkeitsmeldungen.
- Dienste von Drittanbietern, die Ihre Erweiterungen aufrufen.
- Exporte, die Menschen erstellt haben: CSV-Dateien mit Benutzern, Einwilligungsprotokollen und Formulareinsendungen
  auf irgendeinem Computer.

## Die Bestandsaufnahme aktuell halten

Überprüfen Sie sie, wenn Sie eine Erweiterung aktivieren, eine Inhaltsklasse mit personenbezogenen Feldern hinzufügen,
ein Formular hinzufügen oder das Hosting wechseln. Jeder neue Ort braucht eine Entscheidung über die Aufbewahrung und
eine Zeile in Ihrem Verzeichnis von Verarbeitungstätigkeiten.

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Die DSGVO und Exponential](dsgvo-zuordnung.md)
- [Verantwortlicher, Auftragsverarbeiter und die Software](verantwortlicher-auftragsverarbeiter-und-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
