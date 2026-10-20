---
title: Dateien mit Partnern teilen
audience: businesses, admin, partner
category: business-solutions
order: 40
summary: Wie ein Exponential-Extranet Preislisten, Handbücher, Verträge und Marketingmaterial mit Partnern und Kunden teilt, sie Dateien sicher in ihren eigenen Bereich hochladen lässt, Downloads mit Berechtigungsprüfung ausliefert und zeigt, wer was heruntergeladen hat.
keywords: Dateien teilen, Partnerdateien, Downloads, Uploads, content/download, Preislisten, Handbücher, Verträge, Upload durch Partner, content/create, Klasse File, enhancedezbinaryfile, Information Collector, Audit, Prüfung von Uploads, Dokumente im Extranet, file sharing, partner files, downloads, uploads, price lists, manuals, contracts, partner upload, File class, information collector, audit, upload checks, extranet documents
type: help
---
# Dateien mit Partnern teilen

Diese Seite ist für Portalverantwortliche, die Mailanhänge und Links zum Dateitransfer durch ein Portal ersetzen. Sie
erklärt, wie Dateien Partnern angeboten werden, wie Partner Dateien zurückschicken können, was verhindert, dass die
falsche Person eine Datei bekommt, und was die Organisation später herausfinden kann.

## Inhalt

1. [Warum ein Portal statt Anhängen](#warum-ein-portal-statt-anhängen)
2. [Partnern Dateien anbieten](#partnern-dateien-anbieten)
3. [Partner hochladen lassen](#partner-hochladen-lassen)
4. [Dateien über Formulare](#dateien-über-formulare)
5. [Was Dateien sicher hält](#was-dateien-sicher-hält)
6. [Wissen, was geschehen ist](#wissen-was-geschehen-ist)
7. [Große Dateien](#große-dateien)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Warum ein Portal statt Anhängen

| Anhänge und Transferlinks | Ein Portal auf Exponential |
|---|---|
| Eine weitergeleitete Mail erreicht jeden | ein Download prüft, ob die Person die Datei lesen darf |
| Alte Versionen bleiben in Postfächern | die aktuelle Datei ersetzt die alte unter derselben Adresse; Versionen bleiben erhalten |
| Keine Aufzeichnung, wer was bekommen hat | Anmeldungen und, wenn eingeschaltet, Downloads werden im Audit-Trail aufgezeichnet |
| Der Zugriff endet erst, wenn der Link abläuft | den Zugriff einer Gruppe zu entfernen, beendet ihn sofort |

## Partnern Dateien anbieten

Dateien sind Inhaltsobjekte der Klasse **File** (`ezbinaryfile`), abgelegt in einem Ordner des Partnerbereichs. Jeder
Download läuft über `content/download`, das prüft, ob die Person das Objekt lesen darf
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#how-a-download-works)).

| Datei | Ablegen in | Wer sie herunterladen kann |
|---|---|---|
| Broschüre für alle Partner | Sektion *Partnermaterial* | jeder Partner |
| Preisliste für eine Stufe | die Sektion der Stufe | Partner dieser Stufe |
| Ein Vertrag für einen Partner | der Ordner dieses Partners (Teilbaum) | nur die Gruppe dieses Partners |

Die Struktur erklärt [Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md). Download-Links sollten Format und
Größe zeigen, und jeder geschützte Download sollte als Benutzer ohne Zugriff getestet werden
([Dateien und Downloads, Checklist](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#checklist)).

## Partner hochladen lassen

Ein Partner, der Dokumente zurückschicken muss (Zertifikate, unterschriebene Formulare, Druckvorlagen), kann in seinen
eigenen Ordner hochladen:

- Eine Rolle mit `content/create`, beschränkt auf die Klasse **File** (Einschränkung Class) und der Gruppe des
  Partners mit einer Teilbaum-Einschränkung auf ihren Ordner zugeordnet.
- `content/edit` mit `Owner = Self`, wenn Partner ersetzen dürfen, was sie hochgeladen haben, und sonst nichts.
- Kein `content/remove`, damit ein Partner nichts löschen kann, worauf sich Ihre Mitarbeiter verlassen.

Uploads werden geprüft: Seit dem 12. Oktober 2026 werden Bilder für jeden Upload-Weg anhand ihres Inhalts gegen
`image.ini [ImageUploadSettings] AllowedMIMETypes[]` geprüft
([Verhaltensänderungen 2026-10](../../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)). Die Upload-Größe begrenzen
`upload_max_filesize` und `post_max_size` von PHP.

> **Tipp**
> Damit Mitarbeiter erfahren, wenn ein Partner hochlädt, folgen sie den Partnerordnern mit **Benachrichtigen**
> ([Benachrichtigungen für Mitarbeiter](../../../../eng-us/help/businesses/intranets/notifications-for-staff.md)). Ein Freigabe-Workflow kann Uploads
> von Partnern auch zurückhalten, bis Mitarbeiter sie annehmen ([Freigabe-Workflows](../intranets/freigabe-workflows.md)).

## Dateien über Formulare

Wenn Partner eine Datei mit einem Formular statt als Inhalt schicken, stellt die Erweiterung `enhancedezbinaryfile`
ein Dateifeld für die Informationssammlung bereit: Sie legt hochgeladene Dateien mit einer Begrenzung in einem
Download-Ordner ab und verschickt gesammelte Informationen mit der Datei als Anhang
([enhancedezbinaryfile](../../../../eng-us/features/6.0/extensions/enhancedezbinaryfile.md),
[Formulare mit dem Information Collector](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).

## Was Dateien sicher hält

- **Berechtigungsprüfung bei jedem Download.** Verlinken Sie Dateien nie direkt aus dem Speicherverzeichnis; das umgeht
  die Prüfung ([Dateien und Downloads, Protected downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#protected-downloads)).
- **Inhaltsprüfungen bei Uploads**, wie oben.
- **Formular-Tokens** in jedem Formular
  ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).
- **HTTPS** für das Portal ([Live gehen: Hosts und HTTPS](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)).
- **Zweistufige Anmeldung** für Partnerkonten, wenn die Dateien sensibel sind
  ([Zwei-Faktor und Anmeldung](../../../../eng-us/help/businesses/intranets/two-factor-and-sign-in.md)).
- **Backups**, die das Speicherverzeichnis einschließen
  ([Backups, die sich wiederherstellen lassen](../../../../eng-us/help/businesses/reliability/backups-that-restore.md)).

## Wissen, was geschehen ist

Der Audit-Trail zeichnet standardmäßig Inhaltsänderungen, Anmeldungen und Ablehnungen auf. Sein Kanal `read` zeichnet
stichprobenartig Knotenaufrufe, Suchen und Downloads auf, nur wenn Lesezugriffe eingeschaltet sind
([Der Audit-Trail, What is recorded](../../../../eng-us/features/6.0/audit-trail.md#what-is-recorded)). Entscheiden Sie mit Ihrer
Ansprechperson für den Datenschutz, ob ein Portal Aufzeichnungen der Downloads braucht und für wie lange, und sagen Sie
das im Datenschutzhinweis des Portals ([DSGVO-Grundlagen](../../../../eng-us/help/businesses/security/gdpr-basics.md)).

Jedes File-Objekt behält seine Versionen, seinen Eigentümer und seine Daten, sodass sich die Frage „Welche Preisliste
hatte Partner A im März?“ über die Versionsseite beantworten lässt
([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).

## Große Dateien

Die Upload-Grenzen von PHP gelten für Redakteure und Partner gleichermaßen. Sehr große Dateien wie Videos liegen besser
bei einem Dienst, der dafür gebaut ist, und werden aus dem Portal verlinkt
([Dateien und Downloads, Large files](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#large-files)).

## Checkliste

- [ ] Jeder Dateibereich gehört zur richtigen Sektion oder zum richtigen Partner-Teilbaum.
- [ ] Downloads als Partner ohne Zugriff getestet.
- [ ] Upload-Rolle der Partner auf die Klasse File und den Ordner des Partners beschränkt, ohne Entfernen.
- [ ] Mitarbeiter folgen den Upload-Ordnern der Partner.
- [ ] Upload-Grenzen vereinbart.
- [ ] Entscheidung zu Aufzeichnungen der Downloads dokumentiert.
- [ ] Backups schließen gespeicherte Dateien ein.

## Verwandte Seiten

- [Dokumente und Dateibibliotheken](../intranets/dokumente-und-dateibibliotheken.md)
- [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Händler- und Lieferantenportale](haendler-und-lieferantenportale.md)
- [Aufbewahrung und Löschung von Daten](../../../../eng-us/help/businesses/security/data-retention-and-deletion.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
