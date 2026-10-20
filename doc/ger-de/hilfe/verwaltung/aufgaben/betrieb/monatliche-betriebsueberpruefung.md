---
title: Monatliche Betriebsüberprüfung
audience: government, admin, project-manager, hosting
category: public-sector
order: 360
summary: Eine einstündige monatliche Checkliste für eine Exponential-Site im öffentlichen Sektor - Zustand, Logs, Sicherheit, Audit, Cronjobs, Integrität, Konten, Sicherungen und Updates - mit dem Befehl oder der Seite für jeden Punkt und einem Nachweis für die Leitung.
keywords: monatliche Überprüfung, Betriebs-Checkliste, Zustandsprüfung, Routine, Sicherheitsüberprüfung, Sicherungen, Updates, Audit, Konten, Nachweis, Service-Management, monthly review, operations checklist, health check, routine, security review, backups, updates, accounts, record, service management
type: help
---
# Monatliche Betriebsüberprüfung

Diese Aufgabe ist für den Administrator, der eine öffentliche Website betreibt; der Nachweis geht an den
Verantwortlichen für den Dienst. Sie verbindet die wöchentliche Checkliste aus
[Eine Site betreiben](../../../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste) mit den Punkten, nach denen
Behörden üblicherweise gefragt werden. Die Fassung für Hoster ist
[Monatlicher Betrieb (Hosting)](../../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Checkliste](#die-checkliste)
3. [Der Nachweis](#der-nachweis)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Administratorkonto und Shell-Zugang; etwa eine Stunde.

## Die Checkliste

| Bereich | Prüfung | Wie |
|---|---|---|
| Zustand | keine roten Zustandsprüfungen | **Setup > Systeminformationen**; `./console exp:system:info --allow-root-user` |
| Caches | Schichten wie erwartet | `php bin/php/cache.php status --allow-root-user` endet mit `PASS` |
| Fehler | nichts Neues oder Wachsendes | **Fehlerübersicht**, Zeitfenster 720 Stunden ([Die Fehlerübersicht lesen](die-fehleruebersicht-lesen.md)) |
| Logs | im Budget, beschreibbar | **Zustand der Logs** ([Den Zustand der Logs prüfen](den-zustand-der-logs-pruefen.md)) |
| Sicherheit | das `security.log` des Monats | Notizen aus [Das Sicherheits-Log wöchentlich prüfen](das-sicherheits-log-woechentlich-pruefen.md) |
| Audit | Ketten unversehrt, Warnungen bearbeitet | `./console exp:audit verify --allow-root-user`; **Audit > Alarme** |
| Cronjobs | alle Teile laufen, keiner angehalten | [Prüfen, ob die Cronjobs laufen](pruefen-ob-die-cronjobs-laufen.md) |
| Workflows | nichts wartet seit Tagen | **Setup > Workflow Prozesse**, *Ältester wartender* |
| Integrität | keine unerwartet geänderten Dateien | `php bin/php/checkmanifest.php --all --extensions` |
| Konten | ruhende, gesperrte, gemeinsame | **Benutzer > Benutzerkonten prüfen** ([Konten finden, die seit Monaten nicht genutzt wurden](../../../../../eng-us/help/government/tasks/people-and-permissions/find-accounts-not-used-for-months.md)) |
| Administratoren | Inhaber des Vollzugriffs unverändert | [Prüfen, wer Administratorrechte hat](../../../../../eng-us/help/government/tasks/people-and-permissions/audit-who-has-admin-rights.md) (vierteljährlich) |
| Sicherungen | eine Sicherung der letzten Tage existiert; Test der Wiederherstellung wie geplant | [Die Sicherung der letzten Nacht auf einem Testserver wiederherstellen](die-sicherung-der-letzten-nacht-auf-einem-testserver-wiederherstellen.md) |
| Zertifikate | mehr als ein Monat verbleibend | `./console exp:velocity ssl show --allow-root-user` oder `openssl` von außen |
| Updates | neue Releases, Sicherheitskorrekturen | **Setup > Updates and packages**, **Check for updates**; das Changelog ([Sicherheitsupdates und Offenlegung](../../../../../eng-us/help/government/operations/upgrades-and-change/security-updates-and-disclosure.md)) |
| Header | noch vorhanden | [Die Sicherheitsheader prüfen](die-sicherheitsheader-pruefen.md) |

## Der Nachweis

Datum, Prüfer, jeder Bereich mit OK oder einem Befund, Maßnahmen mit Verantwortlichen und Terminen. Drei Monate an
Nachweisen zeigen einem Prüfer eine funktionierende Praxis; Befunde, die sich wiederholen, werden zu Änderungen für das
Änderungsgremium.

## Verwandte Seiten
- [Überblick über das Monitoring](../../../../../eng-us/help/government/operations/monitoring/monitoring-overview.md)
- [Überprüfungen des Administratorzugangs](../../../../../eng-us/help/government/operations/people-and-permissions/admin-access-reviews.md)
- [Nachbereitung eines Vorfalls](../../../../../eng-us/help/government/operations/incidents/post-incident-review.md)
- [Checkliste zur Sicherheitsüberprüfung (Hosting)](../../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
