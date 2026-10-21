---
title: Vorlage für ein DSGVO-Anfragenprotokoll
audience: customer, admin, businesses
category: customer-success
order: 210
summary: Eine Protokollvorlage für Anfragen von Personen zu ihren personenbezogenen Daten in einem Exponential-Shop - eine Zeile pro Anfrage, mit Daten, der Art der Anfrage, den gefundenen Daten, dem, was getan wurde, und dem Nachweis - und wie das Protokoll selbst sicher aufbewahrt wird. Vorlage, keine Rechtsberatung.
keywords: DSGVO-Anfragenprotokoll, Protokoll für Betroffenenanfragen, Vorlage, Datenschutzprotokoll, Löschprotokoll, Auskunftsanfrage, Rechenschaftspflicht, Dokumentation, keine Rechtsberatung, GDPR request log, data subject request log, template, privacy log, erasure log, access request, accountability
type: help
---
# Vorlage für ein DSGVO-Anfragenprotokoll

Diese Seite ist eine Vorlage für die Person, die Datenschutzanfragen beantwortet. Wenn Sie jede Anfrage und das, was
daraufhin getan wurde, protokollieren, können Sie später nachweisen, dass Anfragen bearbeitet wurden. Die Routine, die
das Protokoll füllt, steht unter [DSGVO-Anfragen](dsgvo-anfragen.md).

> **Warnung**
> Vorlage, keine Rechtsberatung. Was Sie festhalten müssen, und wie lange, entscheidet Ihr Anwalt oder Ihr
> Datenschutzbeauftragter. Passen Sie die Spalten an deren Rat an.

## Wie Sie das Protokoll führen

- Bewahren Sie es dort auf, wo nur die Personen es lesen können, die Anfragen bearbeiten, nicht in einem gemeinsamen
  Ordner für das ganze Team.
- Halten Sie fest, was Sie getan haben, nicht mehr personenbezogene Daten als nötig: Ein Verweis auf die Person genügt
  oft. Fragen Sie Ihre Beratung, welchen Verweis Sie verwenden sollen.
- Kopieren Sie keine exportierten Daten in das Protokoll. Exporte werden gelöscht, wenn die Anfrage abgeschlossen ist
  ([Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)).
- Sehen Sie das Protokoll durch, wenn Sie Ihre Datenschutzerklärung prüfen, mindestens einmal im Jahr.

## Das Protokoll

```text
GDPR REQUEST LOG - [organization]                 Owner of this log: [role]

No. | Received   | Channel  | Kind of request            | Identity confirmed (how, when) | Due (per adviser)
----|------------|----------|----------------------------|--------------------------------|------------------
1   |            |          | access / correction /      |                                |
    |            |          | export / erasure / object  |                                |

No. | Data found (tick)                                              | Decision (by whom)
----|----------------------------------------------------------------|-------------------
1   | [ ] user account   [ ] orders        [ ] form submissions      |
    | [ ] mail prefs     [ ] newsletter    [ ] other: ______         |

No. | Action taken                         | Tool used                       | Done on | By
----|--------------------------------------|---------------------------------|---------|----
1   |                                      |                                 |         |

No. | Answer sent on | Export files deleted on | Kept, and why (e.g. bookkeeping rule) | Closed on
----|----------------|-------------------------|----------------------------------------|----------
1   |                |                         |                                        |
```

## Die Spalte für die Werkzeuge

Nennen Sie das Werkzeug im Protokoll, damit die nächste Person den Schritt wiederholen kann. Die Werkzeuge, die die
Software bietet, aus [DSGVO-Anfragen](dsgvo-anfragen.md) und [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md):

| Daten | Im Protokoll zu nennendes Werkzeug |
|---|---|
| E-Mail-Einstellungen und Einwilligung | `exp:mail:preferences export` oder `erase`; der eigene Download der Person |
| Widerspruch gegen Werbung | `exp:mail:suppression add`, oder der eigene Abmeldelink der Person |
| Prüfprotokoll-Einträge zu einem Benutzer | `exp:audit export --subject-user=<id>` |
| Benutzerkonto | Entfernen unter **Benutzer** (endgültig) |
| Formulareinsendungen | **Setup > Gesammelte Informationen** |
| Bestellungen | **Shop > Kunden**, Bestellansicht des Kunden; `shop/archiveorder` oder `shop/removeorder` |
| Newsletter-Abonnenten | Die Administration von `cjw_newsletter` |
| Backups | Ihr Hoster, gemäß Ihrer Vereinbarung |

## Der Aufbau eines Eintrags

Ein Eintrag als reiner Text, mit den Feldnamen in eckigen Klammern. Halten Sie Einträge frei von mehr
personenbezogenen Daten, als die Anfrage selbst erfordert.

```text
No. [number] | Received [date] | [channel] | [kind of request] | identity confirmed [how, date] | due [date per adviser]
Data found: [which of the places above hold data about the person]
Decision: [what is erased, exported or kept, and why] ([who decided], [date])
Action: [commands and admin pages used] | [date] | [who did it]
Answer sent [date] | [export files, if any] | [what is kept, and the rule for keeping it] | closed [date]
```

## Verwandte Seiten

- [DSGVO-Anfragen](dsgvo-anfragen.md)
- [Datenexport und Löschung](datenexport-und-loeschung.md)
- [DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
