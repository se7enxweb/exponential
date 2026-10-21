---
title: DSGVO-Anfragen
audience: customer, admin, businesses
category: customer-success
order: 200
summary: Wie ein Shop-Team Anfragen von Personen zu ihren personenbezogenen Daten bearbeitet - einsehen, berichtigen, exportieren, löschen, Werbung beenden - mit den Werkzeugen, die Exponential jeweils bietet, wer was tut, und einer Routine, die Nachweise sichert. Keine Rechtsberatung.
keywords: DSGVO, Betroffenenanfrage, Auskunftsanfrage, Löschung, Recht auf Vergessenwerden, Datenexport, Berichtigung, Widerspruch, Werbung, Datenschutz, Shop-Kunden, keine Rechtsberatung, GDPR, data subject request, access request, erasure, right to be forgotten, data export, correction, objection, privacy
type: help
---
# DSGVO-Anfragen

Diese Seite ist für den Eigentümer oder die Person, die Datenschutzanfragen beantwortet, in einer Organisation, die mit
Exponential verkauft. Sie gibt eine Routine für Anfragen von Kunden zu ihren personenbezogenen Daten vor und nennt für
jede Art von Anfrage die Werkzeuge, die die Software bietet. Einen Überblick über die Verordnung finden Sie unter
[DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md), und eine Übersicht, wo Daten gespeichert werden, unter
[Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md).

> **Warnung**
> Keine Rechtsberatung. Ob und wie Sie eine Anfrage beantworten müssen, in welcher Frist und was Sie aufbewahren dürfen,
> entscheidet Ihr Anwalt oder Ihr Datenschutzbeauftragter. Diese Seite sagt nur, was die Software kann.

## Wer was tut

| Schritt | Wer |
|---|---|
| Nimmt die Anfrage entgegen und protokolliert sie | Die in Ihrer Datenschutzerklärung genannte Person |
| Bestätigt, dass die Person ist, wer sie zu sein angibt | Diese Person, mit einem Verfahren, das Sie mit Ihrer Beratung vereinbart haben |
| Entscheidet, was beantwortet, aufbewahrt oder gelöscht wird | Eigentümer, mit der Beratung |
| Findet die Daten und exportiert oder löscht sie | Administrator oder Partner, mit den passenden Berechtigungen |
| Antwortet der Person und schließt den Protokolleintrag | Die Person, die die Anfrage entgegengenommen hat |

Verwenden Sie für jede Anfrage die [Vorlage für ein DSGVO-Anfragenprotokoll](vorlage-dsgvo-anfragenprotokoll.md).

## Die Arten von Anfragen und die Werkzeuge

| Anfrage | Was die Software bietet | Quelle |
|---|---|---|
| Ihre Daten einsehen | Für E-Mail-Daten: der eigene Download der Person, oder `./console exp:mail:preferences export`; Prüfprotokoll-Einträge zu einem Benutzer: `exp:audit export --subject-user=<id>`; Bestellungen: **Shop > Kunden** und die Bestellansicht des Kunden; Belege können als Datei heruntergeladen werden | [Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md), `kernel/shop/module.php` |
| Ihre Daten berichtigen | Der Kunde bearbeitet sein eigenes Konto; Mitarbeitende bearbeiten den Benutzer unter **Benutzer** | [Konten und Anmeldung](../../konten-und-anmeldung.md) |
| Ihre Daten mitnehmen | „Meine E-Mail-Daten herunterladen“ in JSON und CSV für E-Mail-Daten | [DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md) |
| Ihre Daten löschen | E-Mail-Daten: `exp:mail:preferences erase`; das Konto: den Benutzer entfernen (endgültig, löscht auch die E-Mail-Einstellungen); Formulardaten: **Setup > Gesammelte Informationen**; Bestellungen: archivieren oder entfernen | [Datenexport und Löschung](datenexport-und-loeschung.md) |
| Werbung beenden | Der Link „alle optionalen E-Mails abbestellen“, oder `exp:mail:suppression add` | [Einwilligung und Formulare](../../unternehmen/sicherheit/einwilligung-und-formulare.md) |

Bestelldaten, Abonnentendatensätze des Newsletters in `cjw_newsletter` und Kopien in Backups werden nicht von einem
einzigen Befehl erfasst ([DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md)). Planen Sie jeden dieser
Punkte mit Ihrer Beratung und Ihrem Hoster.

## Bestellungen sind ein Sonderfall

Bestellungen enthalten den Namen des Kunden, seine E-Mail-Adresse und die an der Kasse eingegebene Adresse
([Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)). Gleichzeitig
können buchhalterische und steuerliche Vorschriften verlangen, dass Sie Bestellunterlagen über Jahre aufbewahren.
Entscheiden Sie mit Ihrer Beratung, bevor die erste Anfrage eintrifft, was Sie mit Bestellungen tun, wenn jemand die
Löschung verlangt:

- sie für die Frist aufbewahren, die Ihre Buchhaltungsvorschriften verlangen, und das in der Antwort sagen;
- oder sie archivieren (`shop/archiveorder`), damit sie aus den täglichen Listen verschwinden;
- oder sie entfernen (`shop/removeorder`), wenn keine Vorschrift die Aufbewahrung verlangt.

## Eine Routine

1. **Protokollieren** Sie die Anfrage am Tag ihres Eingangs, mit Datum und Art der Anfrage.
2. **Bestätigen** Sie die Identität der Person auf die vereinbarte Weise.
3. **Finden** Sie die Daten: Konto, Bestellungen (per E-Mail-Adresse in der Bestellansicht des Kunden),
   Formulareinsendungen, E-Mail-Einstellungen, Newsletter-Datensätze und alles, was Ihre eigenen Inhaltsklassen hinzufügen.
4. **Entscheiden** Sie mit dem Eigentümer und, wenn nötig, der Beratung.
5. **Handeln** Sie: exportieren, berichtigen oder löschen, wie entschieden.
6. **Antworten** Sie der Person.
7. **Räumen** Sie auf: Löschen Sie Exportdateien, sobald die Anfrage erledigt ist; sie enthalten personenbezogene
   Daten ([Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)).
8. **Schließen** Sie den Protokolleintrag mit dem Datum und dem, was getan wurde.

> **Tipp**
> Geben Sie die Berechtigungen für Exporte und das Lesen des Prüfprotokolls, `mailpreferences/export` und
> `audit/read`, nur wenigen Personen, wie es die Compliance-Seite für Hoster empfiehlt ([Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md)).

## Das Prüfprotokoll

Das Prüfprotokoll ist eine Hash-Kette, daher werden einzelne Einträge nicht gelöscht; personenbezogene Felder in
seinem Index werden nach einer festgelegten Zeit pseudonymisiert
([Datenaufbewahrung und Löschung](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)). Ob das in Ihrem Fall
eine Löschanfrage erfüllt, ist eine Rechtsfrage für Ihre Beratung.

## Verwandte Seiten

- [Vorlage für ein DSGVO-Anfragenprotokoll](vorlage-dsgvo-anfragenprotokoll.md)
- [Datenexport und Löschung](datenexport-und-loeschung.md)
- [DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md)
- [Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
