---
title: Datenexport und Löschung
audience: customer, admin, hosting
category: customer-success
order: 220
summary: Was ein Shop-Betreiber in Exponential exportieren und löschen kann, und wie - E-Mail-Daten, Konten, Formulareinsendungen, Bestellungen, Newsletter-Datensätze, Logs und Backups - mit einer jährlichen Aufbewahrungsroutine und den Fragen, die Sie mit Ihrem Hoster klären sollten. Keine Rechtsberatung.
keywords: Datenexport, Datenlöschung, Aufbewahrung, Kundendaten löschen, Bestellungen entfernen, Bestellungen archivieren, Export der E-Mail-Einstellungen, gesammelte Informationen, Backups, Aufbewahrungsplan, keine Rechtsberatung, data export, data erasure, retention, delete customer data, archive orders, collected information, retention schedule
type: help
---
# Datenexport und Löschung

Diese Seite ist für den Eigentümer und den Administrator. Sie führt für jede Art von Daten, die ein Shop vorhält, auf,
wie sie exportiert und wie sie gelöscht werden, und gibt eine jährliche Routine vor, damit Daten nicht länger
aufbewahrt werden als beschlossen. Die vollständigen Tabellen der Standardwerte und Einstellungen stehen unter
[Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md); diese Seite ist die Sicht
des Shops darauf.

> **Warnung**
> Keine Rechtsberatung. Wie lange Sie welche Art von Daten aufbewahren müssen oder dürfen, ist eine rechtliche und
> buchhalterische Entscheidung. Fragen Sie Ihren Anwalt, Ihren Steuerberater oder Ihren Datenschutzbeauftragten.

## Exportieren und löschen, nach Art der Daten

| Daten | Export | Löschung | Quelle |
|---|---|---|---|
| E-Mail-Einstellungen und Einwilligung | Der eigene Download der Person; `./console exp:mail:preferences export --email=<address>` | `./console exp:mail:preferences erase --email=<address> --yes` (Einwilligungsprotokoll anonymisiert, Widerruf als Nachweis aufbewahrt) | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md) |
| Benutzerkonto | Unter **Benutzer** einsehen | Den Benutzer entfernen: endgültig, kein Papierkorb, löscht die E-Mail-Einstellungen | [Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md) |
| Bestellungen | Die Bestellungen eines Kunden in der Bestellansicht des Kunden; jeder Beleg als heruntergeladene Datei | Archivieren (`shop/archiveorder`) oder entfernen (`shop/removeorder`) | `kernel/shop/module.php`, [Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md) |
| Formulareinsendungen | **Setup > Gesammelte Informationen**, CSV-Export | Dieselbe Seite: Sammlungen entfernen | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| Newsletter-Abonnenten | Die Administration von `cjw_newsletter` | Die Benutzerliste von `cjw_newsletter` | [cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md) |
| Prüfprotokoll-Einträge | `exp:audit export --subject-user=<id>` | Nicht einzeln; für die Aufbewahrungsfrist aufbewahrt, personenbezogene Felder pseudonymisiert | [Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md) |
| Warenkörbe | Nicht nötig | Vom Cronjob `basket_cleanup.php` entfernt | `settings/cronjob.ini` |
| Backups | Ihr Hoster | Verfallen gemäß Ihrer Backup-Aufbewahrung | [Backups: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |

> **Hinweis**
> Der Shop hat keinen Export aller Bestellungen in eine Tabellenkalkulation und unter seinen Ansichten keinen Befehl,
> der alle Bestellungen eines Kunden entfernt oder anonymisiert (`kernel/shop/module.php` und [Was der eingebaute Shop leistet](../../unternehmen/shops/was-der-eingebaute-shop-kann.md)).
> Für eine einzelne Anfrage findet die Bestellansicht des Kunden dessen Bestellungen über die E-Mail-Adresse
> (`shop/customerorderview`).

## Bestellungen archivieren oder entfernen?

| | Archivieren | Entfernen |
|---|---|---|
| Verschwindet aus der aktiven Bestellliste | ja | ja |
| Kann zurückgeholt werden | ja (`shop/unarchiveorder`) | nein |
| Für die Buchhaltung aufbewahrt | ja | nein |
| Personenbezogene Daten weiter gespeichert | ja | nein |

Die meisten Shops archivieren abgeschlossene Bestellungen nach Ablauf der Rückgabefrist und entfernen sie erst, wenn
die buchhalterische Aufbewahrungsfrist abgelaufen ist. Halten Sie Ihre Fristen in einem Aufbewahrungsplan fest; der
unter [Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md) hat eine Zeile für
Bestellungen.

## Die Standardwerte, die von selbst greifen

Mehrere Arten von Daten werden von Cronjobs entfernt, mit Fristen, die Sie ändern können, zum Beispiel Sitzungen nach
3 Tagen (`site.ini [Session] SessionTimeout`), `404.log` nach 30 Tagen und `security.log` nach 90 Tagen
([Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)).

> **Warnung**
> Wenn die Cronjobs nicht laufen, wird nichts entfernt. Bitten Sie Ihren Hoster, zu bestätigen, dass sie eingeplant
> sind ([Cronjobs](../../../anleitungen/cronjobs.md)).

## Eine jährliche Aufbewahrungsroutine

- [ ] Aufbewahrungsplan mit der Beratung durchgesehen: Fristen für Bestellungen, Konten, Formulareinsendungen, Logs, Backups.
- [ ] Die Einstellungen entsprechen noch dem Plan; Ihr Administrator ändert sie in `settings/override`.
- [ ] Bestellungen nach Ablauf der buchhalterischen Frist entfernt, die anderen archiviert.
- [ ] Formulareinsendungen, die älter sind als nötig, unter **Setup > Gesammelte Informationen** entfernt.
- [ ] Lange ungenutzte Kundenkonten mit **Benutzer > Benutzerkonten prüfen** durchgesehen.
- [ ] Newsletter-Abonnenten, die nicht mehr da sind, in der Newsletter-Administration bearbeitet.
- [ ] Exportdateien auf den Rechnern der Mitarbeitenden und in `var/tmp/` gelöscht.
- [ ] Backup-Aufbewahrung mit dem Hoster bestätigt.

## Was Sie mit Ihrem Hoster klären sollten

- Wie lange Backups aufbewahrt werden, und wo.
- Ob gelöschte Daten aus den Backups entfernt werden müssen oder mit ihnen verfallen.
- Wie eine Wiederherstellung verhindert, dass gelöschte Daten wieder in Gebrauch kommen.
- Welche Server-, E-Mail- und Panel-Logs außerhalb von Exponential aufbewahrt werden, und wie lange.

## Verwandte Seiten

- [Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)
- [DSGVO-Anfragen](dsgvo-anfragen.md)
- [Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)
- [Aufbewahrung von Logs und Daten für Hoster](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
