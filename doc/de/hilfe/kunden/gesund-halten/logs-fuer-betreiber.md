---
title: Logs für Betreiber
audience: customer, admin, businesses
category: customer-success
order: 12
summary: Was die Logs und das Prüfprotokoll einer Exponential-Website einem Betreiber sagen können, wie eine Fehlerreferenz die Meldung eines Besuchers mit den genauen Logzeilen verbindet, wie personenbezogene Daten in Logs geschützt werden und was Sie Ihren Administrator oder Hoster fragen sollten.
keywords: Logs, Log-Viewer, Fehlerreferenz, ERR, Prüfprotokoll, Fehlerübersicht, Maskierung, personenbezogene Daten, Aufbewahrung, Rotation, logs, log viewer, error reference, audit trail, error digest, masking, personal data, retention, rotation
type: help
---
# Logs für Betreiber

Sie werden selten selbst ein Log lesen, aber Sie sollten wissen, was die Logs beantworten können, denn das ändert, was
Sie anfordern, wenn etwas schiefgeht. Diese Seite ist die Übersicht für Betreiber. Die Werkzeuge des Administrators sind
unter [Der Log-Viewer](../../../../en/us/features/6.0/log-viewer.md) und
[Einen Fehler anhand seiner Referenz finden](../../../../en/us/guides/finding-an-error-by-its-reference.md) beschrieben.

## Zwei Arten von Aufzeichnungen

| Aufzeichnung | Beantwortet | Wo |
|---|---|---|
| Die Logs | Was technisch schiefging, und wann | **Setup > Logdateien** in der Administration ([Der Log-Viewer](../../../../en/us/features/6.0/log-viewer.md)) |
| Das Prüfprotokoll | Wer was getan hat: Anmeldungen, Inhaltsänderungen, Einstellungen, Deployments, Bestellungen | Der Reiter **Audit** ([Das Prüfprotokoll](../../../../en/us/features/6.0/audit-trail.md)) |

Die Logs erklären einen Fehler; das Prüfprotokoll erklärt oft seine Ursache: „Eine Bearbeitung, eine Veröffentlichung
oder eine Einstellungsänderung direkt vor dem Fehler ist oft die Ursache“ ([Einen Fehler anhand seiner Referenz finden](../../../../en/us/guides/finding-an-error-by-its-reference.md)).

## Die Fehlerreferenz

Wenn auf der Website etwas schiefgeht, sieht der Besucher **Something went wrong on our side** mit einer Referenz wie
`ERR-0123456789` am unteren Rand. „Der Fehler wurde bereits protokolliert; mit der Referenz findet ein Administrator
genau diesen Moment in den Logs“ ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)).

Was das für Sie bedeutet:

- **Fragen Sie nach der Referenz.** Schulen Sie Ihr Support-Personal und Ihre Redakteure darin, die Referenz, die
  Uhrzeit und die Seitenadresse zu notieren, wann immer sie oder ein Kunde auf einen Fehler stoßen ([Probleme melden](probleme-melden.md)).
- **Geben Sie sie unverändert weiter.** Ein Administrator fügt sie unter **Setup > Logdateien** in das Feld
  **Fehlerreferenz** ein und sieht den Eintrag und jede Zeile derselben Anfrage.
- **Melden Sie schnell.** Logs rotieren; eine alte Referenz ist möglicherweise schon herausrotiert (die Zahl der
  aufbewahrten Rotationen ist eine Einstellung, `site.ini [FileSettings] LogFileRotateCount`, derselbe Leitfaden).

## Personenbezogene Daten in Logs

Logs können E-Mail- und IP-Adressen enthalten. Der Log-Viewer maskiert sie standardmäßig; nur ein Benutzer mit der
Richtlinie `setup/unmasklogs` kann die Maskierung für sich selbst ausschalten, und ein geteilter Permalink ist immer
maskiert ([Einen Fehler anhand seiner Referenz finden](../../../../en/us/guides/finding-an-error-by-its-reference.md)). Das
Prüfprotokoll kürzt Adressen, hasht Sitzungen und zeichnet nie Passwörter, Tokens oder Geheimnisse auf
([Das Prüfprotokoll](../../../../en/us/features/6.0/audit-trail.md)).

Ihre Regel: **Fügen Sie nie unmaskierte Logzeilen in ein Ticket ein**, und leiten Sie nie Logs mit Kundendaten an
jemanden weiter, der sie nicht braucht. Wie lange Logs und Prüfprotokoll-Einträge aufbewahrt werden, behandelt
[Datenaufbewahrung und Löschung](../../../../en/us/help/businesses/security/data-retention-and-deletion.md) (keine Rechtsberatung).

## Wer was lesen darf

| Recht | Gibt |
|---|---|
| `setup/logs` | Den Log-Viewer, maskiert |
| `setup/unmasklogs` | Die Maskierung für sich selbst ausschalten |
| `audit/read`, `audit/manage` | Das Prüfprotokoll lesen und verwalten; in einer neuen Installation hat nur die Rolle Administrator diese Richtlinien |

Quellen: [Einen Fehler anhand seiner Referenz finden](../../../../en/us/guides/finding-an-error-by-its-reference.md),
[Der Leitfaden zum Prüfprotokoll](../../../../en/us/bc/6.0/audit.md). Geben Sie diese Rechte nur den Personen, die Probleme
beantworten.

## Was Sie Ihren Administrator oder Hoster fragen sollten

- [ ] Wer liest die Fehlerübersicht, und wie oft ([Überwachung](ueberwachung.md))?
- [ ] Wie lange werden Logs aufbewahrt, bevor sie herausrotieren?
- [ ] Wird das Prüfprotokoll regelmäßig verifiziert, und wird es außerhalb des Servers kopiert?
- [ ] Wenn ich eine Fehlerreferenz schicke, wie schnell können Sie mir sagen, was es war?
- [ ] Müssen die Personen, die Logs demaskieren können, das wirklich können?

## Verwandte Seiten

- [Logs und Fehlerreferenzen (für Unternehmen)](../../../../en/us/help/businesses/reliability/logs-and-error-references.md)
- [Prüfprotokoll für den Betrieb](../../../../en/us/help/businesses/reliability/audit-trail-for-operations.md)
- [Log-Viewer und Rotation (für Hoster)](../../hosting/betrieb/log-viewer-und-log-rotation.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
