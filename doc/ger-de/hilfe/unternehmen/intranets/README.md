---
title: Intranets
audience: businesses, project-manager, admin
category: business-solutions
order: 1
summary: Die Einstiegsseite für Organisationen, die ihr Mitarbeiter-Intranet auf Exponential betreiben - was der Bereich abdeckt, für wen er ist, jede Seite mit einer Zeile, eine empfohlene Lesereihenfolge und die benachbarten Bereiche.
keywords: Intranet, Mitarbeiterportal, Abteilungen, Rollen, Richtlinien, Sektionen, Dokumente, Dateibibliothek, Suche, Neuigkeiten, Ankündigungen, Freigabe, Workflow, Zwei-Faktor, LDAP, Verzeichnis, Benachrichtigungen, Einführung, intranet, staff portal, employee portal, departments, roles, policies, sections, documents, file library, search, news, announcements, approval, workflow, two-factor, directory, notifications, rollout
type: help
---
# Intranets

Dieser Bereich ist für Organisationen, die eine private Website für ihre eigenen Mitarbeiter möchten: einen Ort für
Neuigkeiten, Richtlinien, Formulare, Dokumente und Abteilungsseiten, die nur Mitarbeiter öffnen können. Er ist für die
Führungskräfte geschrieben, die über ein Intranet entscheiden, und für die Person, die es betreiben wird. Jede Seite
sagt in einfacher Sprache, was Exponential für die Aufgabe bietet, und verlinkt dann die technische Seite für die
Person, die es einrichtet.

## Inhalt

1. [Was ein Intranet auf Exponential ist](#was-ein-intranet-auf-exponential-ist)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Eine empfohlene Reihenfolge](#eine-empfohlene-reihenfolge)
4. [Was Sie vor dem Start brauchen](#was-sie-vor-dem-start-brauchen)
5. [Benachbarte Bereiche](#benachbarte-bereiche)
6. [Verwandte Seiten](#verwandte-seiten)

## Was ein Intranet auf Exponential ist

Ein mit Exponential gebautes Intranet ist eine gewöhnliche Exponential-Website, deren Besucher sich anmelden müssen.
Die Bausteine gehören alle zum Kernel, den jede Installation hat:

| Baustein | Was er einem Intranet gibt | Quelle |
|---|---|---|
| Ein Siteaccess mit `RequireUserLogin=true` | jede Seite verlangt zuerst eine Anmeldung | [Mitgliederregistrierung und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md#a-members-only-area) |
| Benutzergruppen, Rollen und Richtlinien | Abteilungen, Redakteure, Leser und wer was tun darf | [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md) |
| Sektionen | Inhaltsbereiche, die nur manche Gruppen lesen oder bearbeiten dürfen | [Sektionen](../../../anleitungen/sektionen.md#132-ein-intranet-mit-abteilungen) |
| Objektzustände | Stadien wie Entwurf, In Prüfung, Freigegeben | [Objektzustände](../../../anleitungen/objektzustaende.md) |
| Workflows | Freigabe vor dem Veröffentlichen | [Workflows](../../../anleitungen/workflows.md) |
| Dateien und `content/download` | eine Dokumentenbibliothek, deren Downloads die Berechtigungen beachten | [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |
| Suche | Mitarbeiter finden nur, was sie lesen dürfen | [Suche auf der Website](../../../../eng-us/help/creators/website-creators/site-search.md) |
| Benachrichtigungen | eine Mail, wenn unterhalb eines Elements, dem ein Benutzer folgt, etwas veröffentlicht wird | [Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md) |
| Der Audit-Trail | wer was getan hat, mit einer manipulationssicheren Aufzeichnung | [Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |

Zwei weitere Bausteine gibt es als Erweiterungen oder Einstellungen: die zweistufige Anmeldung (die Erweiterung
`sevenx_authentication_2fa`, in `composer.json` aufgeführt) und die Anmeldung gegen ein Firmenverzeichnis (die
Login-Handler des Kernels für LDAP und Textdateien, eingestellt in `settings/ldap.ini` und `settings/textfile.ini`).

> **Hinweis**
> Dieser Bereich beschreibt, was das Repository enthält. Er nennt keine Kunden und zitiert keine Statistiken. Wo es
> eine Funktion nicht gibt, sagt die Seite das.

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Intranet planen](intranet-planen.md) | Was vor dem Bau der ersten Seite zu entscheiden ist: Zielgruppe, Struktur, Verantwortliche, Budgetfragen |
| [Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md) | Wie Abteilungen zu Benutzergruppen, Sektionen und eingeschränkten Rollen werden |
| [Dokumente und Dateibibliotheken](dokumente-und-dateibibliotheken.md) | Richtlinien, Formulare und Dateien so ablegen, dass nur die richtigen Personen sie herunterladen können |
| [Suche im Intranet](suche-im-intranet.md) | Die eingebaute Suche, Solr über `ezfind` und was Mitarbeiter finden können |
| [Neuigkeiten und Ankündigungen](neuigkeiten-und-ankuendigungen.md) | Firmenneuigkeiten veröffentlichen, Startseiten, Feeds und „Benachrichtigen“ |
| [Freigabe-Workflows](freigabe-workflows.md) | Freigabe vor dem Veröffentlichen und Prüfstadien mit Objektzuständen |
| [Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md) | Passwörter, Sperren, zweistufige Anmeldung und Social Login |
| [Verzeichnisanmeldung und LDAP](verzeichnisanmeldung-und-ldap.md) | Was die Login-Handler für LDAP und Textdateien wirklich tun und was nicht eingebaut ist |
| [Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md) | Abonnements, Digests, Mails zur Zusammenarbeit und der Cronjob dahinter |
| [Checkliste für die Intranet-Einführung](checkliste-fuer-die-intranet-einfuehrung.md) | Eine Checkliste vom ersten Test bis zu dem Tag, an dem die Mitarbeiter eingeladen werden |
| [Intranet-FAQ](intranet-faq.md) | Kurze Antworten auf die Fragen, die Führungskräfte am häufigsten stellen |

## Eine empfohlene Reihenfolge

1. Lesen Sie [Intranet planen](intranet-planen.md) mit den Personen, die das Intranet verantworten werden.
2. Legen Sie die Struktur in [Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md) fest.
3. Entscheiden Sie, wie sich Mitarbeiter anmelden: [Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md) und
   [Verzeichnisanmeldung und LDAP](verzeichnisanmeldung-und-ldap.md).
4. Planen Sie die Inhalte: [Dokumente und Dateibibliotheken](dokumente-und-dateibibliotheken.md),
   [Neuigkeiten und Ankündigungen](neuigkeiten-und-ankuendigungen.md), [Suche im Intranet](suche-im-intranet.md).
5. Entscheiden Sie, wer was freigibt: [Freigabe-Workflows](freigabe-workflows.md) und
   [Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md).
6. Arbeiten Sie die [Checkliste für die Intranet-Einführung](checkliste-fuer-die-intranet-einfuehrung.md) durch.
7. Halten Sie die [Intranet-FAQ](intranet-faq.md) für Fragen von Kollegen bereit.

## Was Sie vor dem Start brauchen

- [ ] Eine Exponential-Installation oder einen Partner oder Hoster, der eine bereitstellt
  ([Hosting auswählen](../auswahl/hosting-auswaehlen.md)).
- [ ] Einen Hostnamen für das Intranet und ein HTTPS-Zertifikat
  ([Live gehen: Hosts und HTTPS](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)).
- [ ] Eine benannte verantwortliche Person für das Intranet und eine für jeden Abteilungsbereich.
- [ ] Eine Liste der Mitarbeitergruppen und was jede Gruppe lesen und bearbeiten darf.
- [ ] Eine Entscheidung zur Anmeldung: lokale Konten, ein Verzeichnis, zweistufige Anmeldung.
- [ ] Eine funktionierende Mail-Einrichtung für Aktivierung, Zurücksetzen von Passwörtern und Benachrichtigungen
  ([Mailzustellung](../../hosting/betrieb/mailzustellung.md)).

## Benachbarte Bereiche

- [Extranets](../extranets/README.md): dieselben Werkzeuge für Partner, Kunden, Händler und Lieferanten.
- [Mitgliederwebsites](../mitglieder/README.md): Registrierung durch die Öffentlichkeit, Inhalte nur für Mitglieder und
  Veranstaltungen.
- [Sicherheit](../sicherheit/README.md): Zugriffsprüfungen, Konten und Passwörter, DSGVO-Grundlagen.
- [Zuverlässigkeit](../zuverlaessigkeit/README.md): das Intranet an jedem Arbeitstag verfügbar halten.
- [Kosten](../kosten/README.md), einschließlich des [durchgerechneten Beispiels für ein Intranet](../kosten/durchgerechnetes-beispiel-intranet.md).
- Öffentliche Stellen: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Verwandte Seiten

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- [Welcher Bereich zu Ihrer Organisation passt](../welcher-bereich-zu-ihrer-organisation-passt.md)
- [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
