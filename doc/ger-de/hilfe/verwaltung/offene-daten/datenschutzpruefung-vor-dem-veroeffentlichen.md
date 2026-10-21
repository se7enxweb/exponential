---
title: Datenschutzprüfung vor dem Veröffentlichen von Daten
audience: government, editor, project-manager
category: public-sector
order: 160
summary: Ein Prüfablauf für Datenteams, bevor ein Datensatz öffentlich wird - personenbezogene Daten in Spalten und Freitext, kleine Fallzahlen, die Menschen erkennbar machen, Metadaten in Dateien, und was Exponential selbst beim Veröffentlichen hinzufügt oder aufzeichnet.
keywords: Datenschutz, personenbezogene Daten, DSGVO, Anonymisierung, Aggregation, kleine Fallzahlen, Metadaten, Dateieigenschaften, Re-Identifizierung, offene Daten, privacy, personal data, GDPR, anonymization, aggregation, small numbers, metadata, file properties, re-identification, open data, data protection
type: help
---
# Datenschutzprüfung vor dem Veröffentlichen von Daten

Diese Seite richtet sich an die Datenveröffentlichung und ihre Führungskräfte. Das Veröffentlichen eines Datensatzes
ist in der Praxis endgültig: Einmal heruntergeladen, lässt sich eine Datei nicht zurückholen. Dieser Ablauf hilft,
personenbezogene Daten abzufangen, bevor sie hinausgehen. Exponential speichert und liefert die Datei, die Sie
hochladen; es untersucht ihren Inhalt nicht, deshalb sind diese Prüfungen ein menschlicher Schritt.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Regeln, die meist gelten](#die-regeln-die-meist-gelten)
2. [Der Prüfablauf](#der-prüfablauf)
3. [Metadaten in Dateien](#metadaten-in-dateien)
4. [Was Exponential beim Veröffentlichen aufzeichnet](#was-exponential-beim-veröffentlichen-aufzeichnet)
5. [Wenn personenbezogene Daten veröffentlicht wurden](#wenn-personenbezogene-daten-veröffentlicht-wurden)

## Die Regeln, die meist gelten

In der EU regelt die [DSGVO, Verordnung (EU) 2016/679](https://eur-lex.europa.eu/eli/reg/2016/679/oj)
personenbezogene Daten, und die [Richtlinie (EU) 2019/1024 über offene Daten](https://eur-lex.europa.eu/eli/dir/2019/1024/oj)
gilt für die Weiterverwendung von Informationen des öffentlichen Sektors, ohne den Schutz personenbezogener Daten zu
berühren. Andere Rechtsordnungen haben eigene Datenschutzgesetze. Ihre Datenschutzbeauftragten entscheiden; diese Seite
gibt die praktischen Prüfungen vor.

## Der Prüfablauf

Führen Sie ihn für jeden neuen Datensatz und jede neue Datei eines bestehenden durch.

| # | Prüfung | Wie |
|---|---|---|
| 1 | Keine direkten Identifikatoren | Suchen Sie nach Spalten mit Namen, E-Mail-Adressen, Telefonnummern, Wohnadressen von Einzelpersonen, Ausweisnummern, Kontonamen |
| 2 | Keine Identifikatoren im Freitext | Lesen oder durchsuchen Sie Spalten mit Kommentaren und Beschreibungen; Menschen schreiben Namen hinein |
| 3 | Keine kleinen Fallzahlen | In Tabellen mit Anzahlen können Zellen mit sehr kleinen Zahlen Menschen erkennbar machen; fassen Sie sie nach der Regel Ihrer Stelle zusammen oder unterdrücken Sie sie |
| 4 | Keine seltenen Kombinationen | Alter, Stadtteil und Datum zusammen können eine einzelne Person herausgreifen; vergröbern Sie sie (Altersgruppen, Monate) |
| 5 | Keine versteckten Daten | Ausgeblendete Spalten oder Blätter in Tabellen, Kommentare, nachverfolgte Änderungen, frühere Werte |
| 6 | Richtige Datei | Dateiname und Inhalt passen zur Datensatzseite; kein Entwurf und nicht die interne Vollfassung |
| 7 | Vier-Augen-Prinzip | Eine Datenverantwortliche gibt frei (siehe „Wer Datensätze veröffentlichen darf“) |

Halten Sie die Prüfung (wer, wann, welche Datei) in Ihrem internen System fest oder in einem Feld des Datensatzes, das
nicht öffentlich angezeigt wird, etwa einem internen Vermerk, den Ihr Template nicht ausgibt.

## Metadaten in Dateien

Office-Dokumente und PDFs tragen Eigenschaften: Autor, Organisation, Kommentare, manchmal den Pfad auf dem Rechner des
Autors. Entfernen Sie sie mit dem Programm, mit dem die Datei erstellt wurde, oder exportieren Sie in ein Format ohne
solche Eigenschaften (CSV hat keine). Exponential speichert die Datei unverändert; es entfernt keine Eigenschaften.

## Was Exponential beim Veröffentlichen aufzeichnet

| Was | Wo | Für die Öffentlichkeit sichtbar |
|---|---|---|
| Die Version und ihr Ersteller | die Versionsgeschichte des Objekts | nein, Mitarbeitende mit `content/versionread` |
| Ereignisse zum Veröffentlichen und Entfernen | der Audit-Trail, Kanal `content` | nein, Mitarbeitende mit `audit/read` |
| Der ursprüngliche Dateiname | das Dateifeld und die Download-Adresse | ja, der Dateiname erscheint in der Adresse |
| Der Eigentümer des Objekts | Objektinformationen | hängt von Ihren Templates ab; geben Sie ihn auf öffentlichen Seiten nicht aus |

Wählen Sie Dateinamen ohne personenbezogene Daten, weil sie in der Download-Adresse erscheinen
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)).

## Wenn personenbezogene Daten veröffentlicht wurden

1. Entfernen Sie die Datei sofort aus der Distribution und veröffentlichen Sie; entfernen Sie dann auf der Seite
   **Versionen** des Objekts die älteren Versionen, die sie noch enthalten
   ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)).
2. Leeren Sie die Caches vor der Website, damit keine Kopien mehr ausgeliefert werden ([Caches](../../../anleitungen/caches.md)).
3. Informieren Sie Ihre Datenschutzbeauftragten; sie entscheiden über Meldepflichten.
4. Finden Sie heraus, wohin die Daten gegangen sind: Die Anfrageprotokolle und, wenn das Aufzeichnen von Lesezugriffen
   eingeschaltet ist, der Kanal `read` des Audit-Trails können Downloads zeigen
   ([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md), [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).
5. Veröffentlichen Sie die berichtigte Datei und einen Hinweis auf der Datensatzseite.

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Datensätze versionieren und aktualisieren](datensaetze-versionieren-und-aktualisieren.md)
- [Wer Datensätze veröffentlichen darf](wer-datensaetze-veroeffentlichen-darf.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
