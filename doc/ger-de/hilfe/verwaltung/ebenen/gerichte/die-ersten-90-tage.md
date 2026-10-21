---
title: Die ersten 90 Tage für Gerichte
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Ein Plan für 90 Tage für ein Gericht oder einen Spruchkörper, der mit Exponential beginnt - zuerst die Richtlinie zur Veröffentlichung von Entscheidungen, dann Modell, Sektionen, Prüfung und Sicherheit, dann Migration der Hinweise und Start - mit Prüfungen.
keywords: Projektplan, 90 Tage, Gericht, Spruchkörper, Richtlinie zur Veröffentlichung von Entscheidungen, Anonymisierung, Prüfung, Sicherheit, Start, Prüfungen, project plan, 90 days, court, tribunal, decision publication policy, anonymization, review, security, launch, checks
type: help
---
# Die ersten 90 Tage für Gerichte

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Website eines
Gerichts oder Spruchkörpers mit Exponential starten. Für Entscheidungen wird nichts gebaut, bevor die
Veröffentlichungsrichtlinie vereinbart ist. Der Plan setzt voraus, dass eine Installation existiert
([Erste Schritte](../../../../anleitungen/erste-schritte.md)).

## Inhalt

1. [Tage 1 bis 30: Richtlinie und Bestandsaufnahme](#tage-1-bis-30-richtlinie-und-bestandsaufnahme)
2. [Tage 31 bis 60: bauen und absichern](#tage-31-bis-60-bauen-und-absichern)
3. [Tage 61 bis 90: Hinweise, Probelauf, Start](#tage-61-bis-90-hinweise-probelauf-start)
4. [Risiken](#risiken)

## Tage 1 bis 30: Richtlinie und Bestandsaufnahme

| Schritt | Ergebnis |
|---|---|
| Die Richtlinie zur Veröffentlichung von Entscheidungen mit Justiz, Recht und Datenschutz vereinbaren: was, wann, wie anonymisiert, wie lange, wer prüft | eine schriftliche Richtlinie |
| Das Verfahren für das Zurückziehen vereinbaren: wer entscheidet, wer handelt, innerhalb welcher Zeit | ein schriftliches Verfahren ([Entscheidungen mit Sorgfalt veröffentlichen](entscheidungen-mit-sorgfalt-fuer-personenbezogene-daten-veroeffentlichen.md)) |
| Bestandsaufnahme von Hinweisen, Formularen und Vorschriften; Veraltetes kennzeichnen | eine Bestandsliste |
| Vereinbaren, wie Informationen zu Verhandlungen und Schließungen veröffentlicht werden und von wem | eine Regel und ein Dienstplan |

Prüfung: Die Richtlinie nennt die Personen mit jeder Rolle im Veröffentlichungsablauf.

## Tage 31 bis 60: bauen und absichern

| Schritt | Prüfung |
|---|---|
| Die Klassen Entscheidung, Gerichtsstandort und Ratgeber anlegen ([Inhaltsmodell](inhaltsmodell.md)) | Testobjekte lassen sich veröffentlichen |
| Die Zustandsgruppe `decision_status` anlegen | eine Testentscheidung zeigt *In Prüfung* |
| Die Sektionen `decisions_review`, `decisions`, `notices` und die Rollen anlegen ([Struktur](struktur-sektionen-und-rollen.md)) | abgemeldet ist `decisions_review` geschlossen |
| Den Prüf-Workflow für Entscheidungen einrichten und bestätigen, dass der Workflow-Cronjob läuft | eine Testentscheidung wartet unter **Setup > Workflow-Prozesse** |
| Die zweistufige Anmeldung für das Team für Entscheidungen und die Administration verpflichtend machen | `Enforce2FA` getestet |
| Die Kontosperre einschalten und die Härtungs-Checkliste durchgehen | [Sicherheitshärtung](../../../sicherheitshaertung.md#checkliste) |
| Über eine CSP für die Website entscheiden und sie zuerst im Report-only-Modus testen | [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md) |

## Tage 61 bis 90: Hinweise, Probelauf, Start

| Schritt | Lesen |
|---|---|
| Die wichtigsten Hinweise in einfacher Sprache neu schreiben | [Schwerpunkte der Anforderungen](schwerpunkte-der-anforderungen.md) |
| Einen vollständigen Zyklus einer Entscheidung proben: vorbereiten, prüfen, veröffentlichen, berichtigen, zurückziehen | [Entscheidungen mit Sorgfalt veröffentlichen](entscheidungen-mit-sorgfalt-fuer-personenbezogene-daten-veroeffentlichen.md) |
| Einen Hinweis auf eine Schließung durch das diensthabende Team proben | der Ordner für Hinweise |
| Die Barrierefreiheit der Templates testen | [Die Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md) |
| Start | [Start-Checkliste](../../../../../eng-us/help/creators/website-creators/launch-checklist.md) |

Prüfungen beim Start:

- [ ] Der Probelauf des Zurückziehens dauerte kürzer, als das Verfahren erlaubt, einschließlich des Entfernens alter
  Versionen.
- [ ] **Setup > Audit** zeigt die Handlungen des Probelaufs mit den richtigen Benutzern.
- [ ] Keine Testentscheidung ist mehr sichtbar.

## Risiken

| Risiko | Antwort |
|---|---|
| Personenbezogene Daten in einer veröffentlichten Entscheidung | das Verfahren für das Zurückziehen; die oder den Datenschutzbeauftragten informieren |
| Die Prüfung wird zum Engpass, und Entscheidungen stauen sich | Prüfende ergänzen; den ältesten wartenden Prozess beobachten |
| Alte Versionen behalten nach einer Berichtigung personenbezogene Daten | bei jeder Berichtigung wegen personenbezogener Daten das Entfernen von Versionen einschließen |
| Ein Konto des Teams für Entscheidungen wird kompromittiert | zweistufige Anmeldung, Kontosperre, Audit-Alarme |

## Verwandte Seiten

- [Gerichte](README.md)
- [Entscheidungen mit Sorgfalt für personenbezogene Daten veröffentlichen](entscheidungen-mit-sorgfalt-fuer-personenbezogene-daten-veroeffentlichen.md)
- [Start-Checkliste](../../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
