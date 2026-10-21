---
title: Warnzeichen und Fallstricke bei der Bewertung eines CMS
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 140
summary: Die Behauptungen und Angebotsmuster, bei denen ein Bewertungsgremium innehalten und Nachweise verlangen sollte, wenn ein Angebot auf Exponential beruht, und die Fehler, die Gremien selbst machen - jeweils mit der Dokumentation, die die tatsächliche Lage zeigt.
keywords: Warnzeichen, Fallstricke, nicht belegte Behauptungen, Übertreibung, Anbieterabhängigkeit, Lock-in, Bewertungsfehler, Sorgfaltsprüfung, Behauptungen zur Barrierefreiheit, Behauptungen zur Sicherheit, red flags, warning signs, pitfalls, unsupported claims, overclaiming, vendor lock-in, evaluation mistakes, due diligence, accessibility claims, security claims
type: help
---
# Warnzeichen und Fallstricke bei der Bewertung eines CMS

Diese Seite richtet sich an jedes Mitglied eines Bewertungsgremiums. Sie listet Aussagen in Angeboten über Exponential
auf, die über das hinausgehen, was die Dokumentation des Produkts sagt, Muster, die künftige Kosten oder eine
Anbieterabhängigkeit verbergen, und Fehler, die Bewertungsgremien machen. Jeder Punkt verlinkt die Seite, die die
dokumentierte Lage zeigt, sodass Sie dem Bieter eine genaue Frage stellen können.

## Inhalt
1. [Behauptungen, die der Dokumentation widersprechen](#behauptungen-die-der-dokumentation-widersprechen)
2. [Muster, die Kosten oder Lock-in verbergen](#muster-die-kosten-oder-lock-in-verbergen)
3. [Warnzeichen zu Daten und Datenschutz](#warnzeichen-zu-daten-und-datenschutz)
4. [Fehler, die Gremien machen](#fehler-die-gremien-machen)
5. [Wie Sie auf ein Warnzeichen reagieren](#wie-sie-auf-ein-warnzeichen-reagieren)

## Behauptungen, die der Dokumentation widersprechen

| Behauptung in einem Angebot | Dokumentierte Lage |
|---|---|
| „Exponential ist konform mit WCAG 2.2 AA“ | admin4/admin4l „Partly complies“; öffentliche Designs nicht geprüft ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)) |
| „Für Exponential ist ein VPAT / Accessibility Conformance Report verfügbar“ | es gibt keinen; Section 508 und EN 301 549 sind „Unverified“ |
| „Zertifiziert nach OWASP ASVS“ | es wurde keine Bewertung nach ASVS durchgeführt: „Unverified“ ([Sicherheitsstandards](../../../../../eng-us/standards/security.md#owasp-asvs-403)) |
| „Schutz gegen Brute-Force ist eingebaut und eingeschaltet“ | die Kontosperre ist standardmäßig aus; Brute-Force wird von Alarmregeln des Audits erkannt |
| „Eine strenge Content Security Policy schützt die Website“ | Report-only in der Administration, standardmäßig keine auf Websites |
| „DSGVO-konform ab Werk“ | Funktionen werden bereitgestellt; die Einhaltung ist Aufgabe der Organisation, und es gibt kein Cookie-Banner ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)) |
| „Garantierte Reaktionszeiten vom Exponential-Projekt“ | das Projekt veröffentlicht kein SLA; nur eine erste Antwort auf vertrauliche Sicherheitsmeldungen innerhalb eines Werktags ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)) |
| „Unsere Version ist das offizielle Release mit Langzeitunterstützung“ | `SECURITY.md` nennt die Linie 6.0.x als gepflegt; Korrekturen kommen im nächsten Release 6.0.x |
| „Eingebautes GIS / Kartenfunktion“ | eine GIS-Funktion ist nicht dokumentiert; die Erweiterung [ezgmaplocation](../../../../../eng-us/features/6.0/extensions/ezgmaplocation.md) fügt einen Datentyp für Kartenorte hinzu, der Google Maps verwendet, einen Dienst eines Dritten |

Ein Bieter kann das, was die Behauptung verspricht, trotzdem durch eigene Arbeit liefern. Bitten Sie ihn, die Behauptung
als Zusage für **Ihre** Website neu zu formulieren, mit der Angabe, wie sie bei der Abnahme nachgewiesen wird.

## Muster, die Kosten oder Lock-in verbergen

- **Ein privater Fork des Kernels**, den nur der Bieter pflegt. Fragen Sie, ob Änderungen an das Projekt zurückgehen
  ([Code zurückgeben](../../../partner/code-zurueckgeben.md)); ein privater Fork macht jedes Upgrade zum Monopol
  des Bieters.
- **Erweiterungen ohne offenen Quellcode**, geliefert ohne Quellcode. Nach der GPL erhält, wer eine Erweiterung erhält,
  auch ihren Quellcode ([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)); verlangen Sie die Lieferung des
  Quellcodes im Vertrag.
- **Gebühren für „Lizenz“ oder „Abonnement“ der Kernsoftware.** Die Software hat keine Lizenzgebühr. Gebühren für
  Support, Hosting oder Leistungen sind normal; achten Sie darauf, dass das Angebot sagt, was was ist.
- **An den Lieferanten gebundenes Hosting ohne Export.** Die Daten lassen sich in offenen Formaten exportieren
  ([Exponential verlassen](../../../../installation/migration/exponential-verlassen.md)); bitten Sie den Bieter, das
  vorzuführen.
- **Upgrades, die wie neue Projekte bepreist werden.** Fragen Sie, wie der Bieter Websites aktuell hält
  ([Kunden aktuell halten](../../../partner/kunden-websites-aktuell-halten.md)) und was ein Update innerhalb von 6.0.x
  kostet.
- **Eine alte Linie.** Ein Angebot auf 5.x oder älter bietet eine nicht unterstützte Version an (`SECURITY.md`).

## Warnzeichen zu Daten und Datenschutz

- Eine Demo auf der Kopie einer echten Website mit echten personenbezogenen Daten.
- Analyse, Captcha oder Social Login von Dritten eingeschaltet ohne einen Plan zur Einwilligung: Die Seite
  [Datenschutzstandards](../../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions) listet, welche
  mitgelieferten Erweiterungen bei Aktivierung Daten an Dritte senden.
- Keine Antwort auf „Wer verarbeitet unsere personenbezogenen Daten, wo und nach welcher Vereinbarung?“
- Während der Ausschreibung per E-Mail verschickte Zugangsdaten.

## Fehler, die Gremien machen

- Die Qualität der Präsentation bewerten statt der Anforderungen.
- Ein „Ja“ in einer Erfüllungsmatrix ohne Quelle akzeptieren.
- Eine Mindestanforderung schreiben, die niemand erfüllen kann (zum Beispiel ein VPAT für das Produkt), und dann
  ungleichmäßig darauf verzichten.
- Nur den Lizenzpreis vergleichen und Kosten für Hosting, Personal, Schulung und Ausstieg übersehen.
- Die Redaktion vergessen: Niemand aus der Redaktion sitzt im Gremium oder probiert das System aus.
- Den Ausstieg nicht testen: Der Export wird erst nach der Vertragsunterzeichnung geprüft.

## Wie Sie auf ein Warnzeichen reagieren

1. Schreiben Sie eine Aufklärungsfrage, die die Behauptung und die Dokumentationsseite zitiert.
2. Verlangen Sie Nachweise: ein Dokument, einen Test, den Sie wiederholen können, oder eine vertragliche Zusage mit einem
   Abnahmetest.
3. Bewerten Sie die Antwort, nicht die ursprüngliche Behauptung.
4. Halten Sie den Austausch in der Bewertungsakte fest.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Fragen für die Demo eines CMS](fragen-fuer-eine-demo.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
