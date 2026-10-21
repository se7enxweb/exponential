---
title: "Glossar für Verwaltungen: A bis F"
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: Begriffe des öffentlichen Sektors und von Exponential von A bis F (nach den englischen Fachbegriffen) in jeweils ein bis drei Sätzen - Erklärung zur Barrierefreiheit, Audit-Trail, Inhaltsklasse, Verantwortlicher, Auftragsverarbeiter, Ausstiegsplan, Informationsfreiheit und weitere - mit einem Link auf die Seite, die jeden erklärt.
keywords: Glossar, Begriffe, Definitionen, Erklärung zur Barrierefreiheit, Abnahmeprüfung, Audit-Trail, Inhaltsklasse, Einwilligung, Verantwortlicher, Auftragsverarbeiter, Datenresidenz, Ausstiegsplan, Informationsfreiheit, öffentlicher Sektor, glossary, terms, definitions, accessibility statement, acceptance testing, audit trail, content class, consent, data controller, data processor, data residency, exit plan, freedom of information, public sector
type: reference
---
# Glossar für Verwaltungen: A bis F

Diese Seite richtet sich an Leserinnen und Leser in einer öffentlichen Stelle, die in dieser Hilfe oder in einer
Ausschreibung auf einen unbekannten Begriff stoßen. Jeder Eintrag hat ein bis drei Sätze und einen Link auf die Seite,
die ihn erklärt. Die Einträge sind nach dem englischen Fachbegriff geordnet, der in Klammern steht. Einträge über
Exponential wurden mit der Dokumentation abgeglichen; das vollständige Produktglossar finden Sie im
[Exponential-Glossar](../../../../eng-us/glossary.md).

> **Hinweis** Die Definitionen von Rechtsbegriffen sind Beschreibungen in einfacher Sprache, keine Legaldefinitionen.
> Prüfen Sie das für Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [A](#a)
2. [B und C](#b-und-c)
3. [D](#d)
4. [E und F](#e-und-f)

## A

**Abnahmeprüfung (Acceptance testing).** Die Tests, die eine öffentliche Stelle durchführt, bevor sie gelieferte
Arbeit abnimmt und bezahlt. Stützen Sie sie auf die Anforderungen der Ausschreibung, so formuliert, dass sie sich
überprüfen lassen.

**Zugriffsprüfung (Access review).** Eine regelmäßige Prüfung, wer welche Rechte hat, bei der nicht mehr benötigte
entfernt werden. In Exponential sind die Rollen unter `/role/list` aufgeführt, und **Benutzerkonten prüfen**
(`/user/hygiene`) findet ungenutzte Konten ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

**Konformitätsbericht zur Barrierefreiheit (Accessibility conformance report, ACR).** Der Bericht eines Anbieters
darüber, wie ein Produkt eine Norm zur Barrierefreiheit erfüllt, oft mit einem VPAT erstellt. Für Exponential gibt es
keinen ACR ([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#section-508)).

**Erklärung zur Barrierefreiheit (Accessibility statement).** Eine öffentliche Erklärung auf einer Website über ihre
Barrierefreiheit, ihre bekannten Probleme und wie man Rückmeldung geben kann; die EU-Richtlinie über den barrierefreien
Zugang zu Websites verlangt sie von öffentlichen Stellen. Exponential liefert keine Vorlage dafür mit
([Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#known-gaps-2); Leitfaden des W3C:
[w3.org/WAI/planning/statements](https://www.w3.org/WAI/planning/statements/)).

**admin4.** Das Design der Administration von Exponential, auf das sich die Arbeit an der Barrierefreiheit
konzentriert, mit hellem und dunklem Modus; sein Stand gegenüber WCAG 2.2 AA ist „Partly complies“ (teilweise erfüllt)
([Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)).

**Freigabe-Workflow (Approval workflow).** Ein Ablauf, in dem eine Version wartet, bis jemand sie freigibt, bevor sie
veröffentlicht wird. In Exponential wird er mit Workflows und Triggern gebaut ([Workflows](../../../anleitungen/workflows.md)).

**Audit-Trail (Audit trail).** Ein manipulationssicheres Protokoll sicherheitsrelevanter Ereignisse wie Anmeldungen,
Inhaltsänderungen und das Schreiben von Einstellungen. In Exponential ist er standardmäßig eingeschaltet und mit einer
Hash-Kette gesichert ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## B und C

**Datensicherung (Backup).** Eine Kopie der Datenbank und der Dateien, im selben Moment erstellt, sodass eine Website
wiederhergestellt werden kann ([Datensicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).

**Angebot (Bid).** Die Antwort eines Bieters auf eine Ausschreibung. Vergleichen Sie Angebote mit einem Bewertungsbogen,
der vor ihrem Eingang erstellt wurde.

**Cache.** Gespeicherte Ergebnisse, die Seiten schneller machen; sie werden nach dem Leeren neu aufgebaut. **Setup >
Cache-Verwaltung** (`/setup/cache`) listet sie auf ([Caches](../../../anleitungen/caches.md)).

**Einwilligungsnachweis (Consent record).** Der Nachweis, wozu eine Person wann und mit welchem Text eingewilligt hat.
Exponential führt einen für E-Mail-Kategorien in der Tabelle `expmail_consent_log`
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#gdpr)).

**Inhaltsklasse (Content class).** Die Definition einer Inhaltsart, etwa eines Artikels oder einer Pressemitteilung,
als Liste von Feldern ([Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)).

**Content Security Policy (CSP).** Ein von der Website gesendeter Regelsatz für den Browser, der einschränkt, woher
Skripte und Stile kommen dürfen. Exponential sendet sie standardmäßig in der Administration nur im Berichtsmodus und auf
öffentlichen Websites gar nicht ([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).

**Cronjob.** Eine Aufgabe, die nach einem Zeitplan auf dem Server läuft, etwa das Versenden von Benachrichtigungen oder
das Veröffentlichen zeitgesteuerter Inhalte ([Cronjobs](../../../anleitungen/cronjobs.md)).

## D

**Verantwortlicher (Data controller).** Nach der DSGVO die Organisation, die entscheidet, warum und wie
personenbezogene Daten verarbeitet werden; bei einer öffentlichen Website meist die öffentliche Stelle. Exponential ist
Software, kein Verantwortlicher ([Datenschutzstandards](../../../../eng-us/standards/privacy.md);
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj)).

**Auftragsverarbeiter (Data processor).** Nach der DSGVO eine Organisation, die personenbezogene Daten im Auftrag des
Verantwortlichen verarbeitet, etwa ein Hosting-Unternehmen oder ein Supportdienstleister. Ein
Auftragsverarbeitungsvertrag legt die Bedingungen fest.

**Datenresidenz (Data residency).** Die Anforderung, dass Daten in einem bestimmten Land oder einer bestimmten Region
gespeichert werden. Sie ist eine Eigenschaft des Hostings, nicht der Software; Exponential lässt sich überall
installieren, wo Sie hosten ([Ein Hostingmodell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).

**Datentyp (Datatype).** Der Code, der eine Art von Feld speichert, prüft und anzeigt, etwa Text, Bild oder Relation
([das Exponential-Glossar](../../../../eng-us/glossary.md#d)).

**Entwurf (Draft).** Eine Version eines Inhalts, die bearbeitet wird und noch nicht veröffentlicht ist
([Meine Entwürfe und meine ausstehenden Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).

## E und F

**Editor-Siteaccess (Editor siteaccess).** Eine vereinfachte Administration für Redaktionen, vom Einrichtungsassistenten
installiert ([Einrichtungsassistent und Editor-Siteaccess](../../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)).

**Löschung (Erasure).** Das Löschen der personenbezogenen Daten einer Person auf deren Verlangen. Das Entfernen eines
Benutzerkontos in Exponential löscht dessen E-Mail-Einstellungen und anonymisiert dessen Einwilligungsprotokoll; andere
Daten erreichen Sie über getrennte Werkzeuge ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#known-gaps)).

**Bewertungsgremium (Evaluation panel).** Die Gruppe, die Angebote nach veröffentlichten Kriterien bewertet.

**Ausstiegsplan (Exit plan).** Ein vor der Unterzeichnung vereinbarter Plan, wie ein Vertrag endet: Datenexport,
Übergabe, Übergang. Die Exporte von Exponential sind in
[Von Exponential weg migrieren](../../../installation/migration/exponential-verlassen.md) aufgeführt.

**Erweiterung (Extension).** Ein Ordner unter `extension/`, der Exponential Funktionen hinzufügt und in den
Einstellungen aktiviert wird ([Erweiterungen](../../../anleitungen/extensions.md)).

**Informationsfreiheit (Freedom of information, FOI).** Gesetze, die der Öffentlichkeit das Recht geben, Unterlagen
öffentlicher Stellen anzufordern; in den Vereinigten Staaten ist das Bundesgesetz der FOIA
([foia.gov](https://www.foia.gov/)).

**Funktionale Anforderung (Functional requirement).** Eine Anforderung daran, was das System tun muss, im Unterschied
dazu, wie gut (Leistung, Sicherheit, Barrierefreiheit).

## Verwandte Seiten

- [Glossar für Verwaltungen](README.md)
- [Glossar für Verwaltungen: G bis L](begriffe-g-bis-l.md)
- [Das Exponential-Glossar](../../../../eng-us/glossary.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
