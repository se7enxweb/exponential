---
title: "Glossar für Verwaltungen: G bis L"
audience: government, project-manager, admin, editor
category: public-sector
order: 30
summary: Begriffe des öffentlichen Sektors und von Exponential von G bis L (nach den englischen Fachbegriffen) in jeweils ein bis drei Sätzen - GPL, Livegang, Härtung, Hostingmodell, Informationssammlung, Oberflächensprache, Rechtsgrundlage, Anbieterabhängigkeit, Ort und weitere - mit einem Link auf die Seite, die jeden erklärt.
keywords: Glossar, Begriffe, Definitionen, GPL, Livegang, Härtung, Hosting, Informationssammlung, Vorfall, Oberflächensprache, Rechtsgrundlage, Anbieterabhängigkeit, Ort, Protokoll, öffentlicher Sektor, glossary, terms, definitions, go-live, hardening, hosting, information collection, incident, interface language, legal basis, lock-in, location, log, public sector
type: reference
---
# Glossar für Verwaltungen: G bis L

Diese Seite richtet sich an Leserinnen und Leser in einer öffentlichen Stelle, die auf einen unbekannten Begriff
stoßen. Jeder Eintrag hat ein bis drei Sätze und einen Link auf die Seite, die ihn erklärt. Die Einträge sind nach dem
englischen Fachbegriff geordnet, der in Klammern steht. Das vollständige Produktglossar ist das
[Exponential-Glossar](../../../../eng-us/glossary.md).

> **Hinweis** Die Definitionen von Rechtsbegriffen sind Beschreibungen in einfacher Sprache, keine Legaldefinitionen.
> Prüfen Sie das für Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [G und H](#g-und-h)
2. [I](#i)
3. [K und L](#k-und-l)

## G und H

**GNU Free Documentation License (GFDL).** Die Lizenz der Dokumentation von Exponential, einschließlich dieser Hilfe;
sie erlaubt Ihnen, Seiten zu kopieren, anzupassen und neu zu veröffentlichen, wenn Sie die Hinweise beibehalten
([Freie Dokumentation](../../freie-dokumentation.md); [gnu.org](https://www.gnu.org/licenses/fdl-1.3.html)).

**GNU General Public License (GPL).** Die Lizenz der Software Exponential, Version 2 oder jede spätere Version. Sie
kostet keine Gebühr und erlaubt Ihnen, den Code auszuführen, zu untersuchen, zu ändern und weiterzugeben
([Fragen zur Lizenzierung](../../projekt/lizenz-faq.md); [GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)).

**Livegang (Go-live).** Der Moment, in dem eine Website für die Öffentlichkeit freigeschaltet wird. Eine Checkliste für
den Livegang umfasst Inhalte, Barrierefreiheit, Sicherheit, Datensicherungen und Überwachung
([Checkliste für den Start](../../../../eng-us/help/creators/website-creators/launch-checklist.md)).

**Härtung (Hardening).** Einstellungen und Server so ändern, dass eine Website Angriffen besser widersteht als mit den
Voreinstellungen ([Sicherheitshärtung](../../sicherheitshaertung.md),
[13. Sicherheitshärtung für den Produktivbetrieb](../../../installation/13-sicherheitshaertung.md)).

**Hostingmodell (Hosting model).** Wie und wo eine Website läuft: Shared Hosting, ein virtueller Server, dedizierte
Server, Container oder ein verwalteter Dienst ([Ein Hostingmodell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).

**HSTS.** HTTP Strict Transport Security, ein Header, der Browser anweist, nur HTTPS zu verwenden. In Exponential ist
er standardmäßig leer und wird vom Betreiber gesetzt, sobald die gesamte Website über HTTPS läuft
([Sicherheitsstandards](../../../../eng-us/standards/security.md#known-gaps)).

## I

**Vorfall (Incident).** Ein Ereignis, das den Dienst oder seine Daten schädigt oder bedroht, etwa ein Ausfall oder ein
Einbruch. Hoster haben Runbooks wie [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md) und
[Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md).

**Informationssammlung (Information collection).** Die Funktion von Exponential für Formulare, Umfragen und
Befragungen, deren Antworten gespeichert und unter **Setup > Gesammelte Informationen** aufgeführt werden
([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).

**Installationsquelle (Installer source).** Ein Paket, aus dem der Einrichtungsassistent Websites installiert; solche
Pakete sind gekennzeichnet und brauchen zum Entfernen eine eigene Bestätigung ([Pakete](../../../anleitungen/pakete.md)).

**Oberflächensprache (Interface language).** Die Sprache der Menüs und Schaltflächen der Administration, getrennt von
der Sprache der Inhalte ([Einstellungen zum Umschalten der Oberfläche](../../../anleitungen/ui-umschalter-einstellungen.md),
[Sprachen](../../sprachen.md)).

**Interoperabilität (Interoperability).** Die Fähigkeit von Systemen, Daten auszutauschen. Exponential bietet eine
REST-API, RSS-Feeds und Exporte als CSV, XML und JSON ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md),
[RSS-Feeds](../../../anleitungen/rss-feeds.md)).

## K und L

**Kickstarter.** Das Installationsprogramm von Exponential für die Kommandozeile, das seine Antworten aus einer Datei
liest, `kickstart.ini` ([6. Der Kickstarter](../../../installation/06-kickstarter.md)).

**Rechtsgrundlage (Legal basis).** Nach der DSGVO der Grund, der die Verarbeitung personenbezogener Daten rechtmäßig
macht, etwa eine Einwilligung oder eine öffentliche Aufgabe. Sie zu wählen ist die Entscheidung des Verantwortlichen,
nicht der Software ([Datenschutzstandards](../../../../eng-us/standards/privacy.md)).

**Beschränkung (Limitation).** Eine Einschränkung innerhalb eines Rechts, zum Beispiel „nur in dieser Sektion“ oder
„nur Inhalte dieser Klasse“ ([Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)).

**Anbieterabhängigkeit (Lock-in).** Die Abhängigkeit von einem Anbieter, die einen Wechsel teuer macht. Bei
GPL-Software mit dokumentierten Exporten entsteht Abhängigkeit vor allem durch Verträge, eigens entwickelten Code und
fehlende Dokumentation, nicht durch die Lizenz
([Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md)).

**Ort (Location).** Ein anderes Wort für einen Knoten: eine Stelle eines Inhaltsobjekts im Inhaltsbaum. Ein Objekt kann
mehrere haben ([das Exponential-Glossar](../../../../eng-us/glossary.md#l)).

**Protokoll (Log).** Eine Datei, in der das System Ereignisse und Fehler aufzeichnet. **Setup > Logdateien** listet sie
mit einer Fehlerübersicht auf ([Protokollanzeige](../../../../eng-us/features/6.0/log-viewer.md),
[Protokollrotation](../../../../eng-us/features/6.0/log-rotation.md)).

**Aufbewahrung von Protokollen (Log retention).** Wie lange Protokolle aufbewahrt werden. In Exponential bewahrt der
Audit-Trail pro Kanal 90 Tage aktiv und zwei Jahre archiviert auf; einige andere Protokolle haben Altersgrenzen, die in
`logviewer.ini` festgelegt sind ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#gdpr)).

## Verwandte Seiten

- [Glossar für Verwaltungen](README.md)
- [Glossar für Verwaltungen: A bis F](begriffe-a-bis-f.md)
- [Glossar für Verwaltungen: M bis R](begriffe-m-bis-r.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
