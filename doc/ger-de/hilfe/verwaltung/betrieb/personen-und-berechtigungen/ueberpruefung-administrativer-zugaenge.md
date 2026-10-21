---
title: Überprüfung der Administrationszugänge
audience: government, admin, project-manager
category: public-sector
order: 150
summary: Eine regelmäßige Überprüfung, wer auf einer öffentlichen Website welche Rechte hat - Administratoren, Personen, die Rollen ändern, Kontenadministratoren, Prüfer und Fachbereiche - mit den Kennzeichen der Rollenliste, den Rollenseiten, Benutzerkonten prüfen, dem CSV-Export und dem Audit-Trail, mit einem Nachweis, dem ein Prüfer folgen kann.
keywords: Überprüfung der Zugänge, Überprüfung der Benutzerzugänge, Rezertifizierung, Berechtigungsprüfung, Administrationsrechte, voller Zugriff, Kann Rollen ändern, Rollenliste, Audit, CSV-Export, vierteljährliche Überprüfung, ISO 27001, NIST, access review, user access review, recertification, entitlement review, admin rights, full access, Can change roles, role list, CSV export, quarterly review
type: help
---
# Überprüfung der Administrationszugänge

Diese Seite richtet sich an Administratoren und Führungskräfte, die Zugriffsrechte nach Plan rezertifizieren müssen,
wie es viele Sicherheitsrahmenwerke und interne Prüfpläne verlangen (zum Beispiel die Themen zur Zugriffskontrolle von
[ISO/IEC 27001](https://www.iso.org/standard/27001) und dem
[NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)). Sie zeigt, wo Exponential Ihnen jede Antwort
liefert und wie Sie einen Nachweis hinterlassen.

## Inhalt
1. [Was die Überprüfung beantwortet](#was-die-überprüfung-beantwortet)
2. [Schritt 1: die Rollen](#schritt-1-die-rollen)
3. [Schritt 2: die Personen](#schritt-2-die-personen)
4. [Schritt 3: was geschehen ist](#schritt-3-was-geschehen-ist)
5. [Schritt 4: handeln und festhalten](#schritt-4-handeln-und-festhalten)
6. [Zeitplan](#zeitplan)
7. [Verwandte Seiten](#verwandte-seiten)

## Was die Überprüfung beantwortet

1. Wer hat vollen Zugriff, und sollte er ihn haben?
2. Wer kann Rollen ändern (und sich damit selbst mehr Rechte geben)?
3. Entspricht jede andere Rolle noch ihrem Zweck?
4. Wird jedes Konto noch gebraucht, ist es persönlich und geschützt?
5. Wurde seit der letzten Überprüfung ein Recht außerhalb des Verfahrens vergeben?

## Schritt 1: die Rollen

**Benutzer > Rollen und Richtlinien** (`/role/list`). Die Liste zeigt Kennzeichen je Rolle:

| Kennzeichen | Bedeutung | Prüffrage |
|---|---|---|
| *Voller Zugriff* (rot) | eine Richtlinie über alle Module | ist jede Zuweisung ein namentlich benannter Administrator? |
| *Kann Rollen ändern* (gelb) | eine Richtlinie über das Modul `role` | ist sie auf Systemadministratoren beschränkt? |
| *Nicht zugewiesen* | gibt niemandem etwas | entfernen Sie sie oder dokumentieren Sie, warum sie bleibt |

Sortieren Sie die Liste nach der Zahl der Zuweisungen, um die umfassendsten Rollen zuerst zu sehen. Öffnen Sie jede Rolle: Die
Seite zeigt ihre Richtlinien als Sätze, die Benutzer und Gruppen, denen sie zugewiesen ist, mit ihren Einschränkungen, und
**Betroffene Benutzer insgesamt**
([Leitfaden zu Rollen, Abschnitt 2](../../../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen)). Vergleichen Sie mit Ihrem Rollennachweis
([Rollen, Richtlinien und Einschränkungen](rollen-richtlinien-und-einschraenkungen.md#zugriff-für-ein-audit-dokumentieren)). Eine Einschränkung,
die mit *(no handler, denies)* markiert ist, gewährt keinen Zugriff und sollte bereinigt werden.

## Schritt 2: die Personen

**Benutzer > Benutzerkonten prüfen** (`/user/hygiene`):

- Filtern Sie nach **Seit N Tagen nicht gesehen** und **Nie angemeldet**: Konten, die niemand nutzt.
- Jede Zeile nennt die Rollen eines Kontos, die eigenen und die von Gruppen geerbten; suchen Sie nach den in Schritt 1
  gefundenen Administratoren.
- **Doppelte E-Mail-Adresse**: gemeinsam genutzte Konten, die persönlich sein sollten.
- **Liste als CSV exportieren** für die Prüfakte
  ([Die Kontenliste für eine Überprüfung exportieren](../../../../../eng-us/help/government/tasks/people-and-permissions/export-the-account-list-for-a-review.md)).

Prüfen Sie auf der Kontoseite jedes Administrators (`/user/edit/<id>`) den Hinweis zur zweistufigen Anmeldung und das Datum
der letzten Passwortänderung ([Sicherheit und Audit, Schritt 6](../../../../anleitungen/sicherheit-und-audit.md#6-ein-konto-auf-seiner-kontoseite-prüfen-2-minuten)).

## Schritt 3: was geschehen ist

Sehen Sie sich im Tab **Audit** (`/audit/console`) oder auf der Kommandozeile den Kanal `access` seit der letzten Überprüfung an:

```bash
./console exp:audit search --name='access.*' --from='<date of last review>' --limit=50 --allow-root-user
./console exp:audit alerts list --allow-root-user
```

Die Alarmregel `admin_role_granted` löst jedes Mal aus, wenn eine Administratorrolle vergeben wird; jeder ausgelöste Alarm sollte
zu einer Anfrage in Ihrem Ticketsystem passen. Prüfen Sie, ob die Kette intakt ist: `./console exp:audit verify --allow-root-user`
([Überprüfung des Audit-Trails](../ueberwachung/pruefung-des-audit-trails.md)).

## Schritt 4: handeln und festhalten

| Befund | Maßnahme |
|---|---|
| Administrator nicht nötig | entfernen Sie die Zuweisung auf der Rollenseite (**Ausgewählte entfernen**) |
| Rolle umfassender als ihr Zweck | bearbeiten Sie die Richtlinien; speichern Sie; notieren Sie die Änderung |
| Ungenutztes Konto | **Deaktivieren** unter Benutzerkonten prüfen |
| Gemeinsam genutztes Konto | legen Sie persönliche Konten an; deaktivieren Sie das gemeinsame |
| Vergabe ohne Ticket | untersuchen Sie sie; siehe [Verdacht auf ein kompromittiertes Konto](../vorfaelle/verdacht-auf-kompromittiertes-konto.md) |

Nachweis: Datum, prüfende Person, der CSV-Export, die Liste der Rollen mit Kennzeichen, Befunde und Maßnahmen. Rollenänderungen und
Deaktivierungen stehen selbst im Audit-Trail, was den Nachweis mit den Belegen verbindet.

## Zeitplan

Exponential plant keine Überprüfungen. Üblich ist vierteljährlich für vollen Zugriff und Personen, die Rollen ändern, halbjährlich
für alles andere und sofort, wenn ein Vertrag endet oder ein Team umorganisiert wird. Tragen Sie den Termin in Ihren Kalender und
in Ihre Zugriffsrichtlinie ein ([Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md#eine-zugriffsrichtlinie-auf-einer-seite)).

## Verwandte Seiten
- [Prüfen, wer Administrationsrechte hat](../../../../../eng-us/help/government/tasks/people-and-permissions/audit-who-has-admin-rights.md)
- [Pflege der Benutzerkonten](pflege-von-benutzerkonten.md)
- [Funktionstrennung](funktionstrennung.md)
- [Checkliste zur Sicherheitsprüfung (Hosting)](../../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
