---
title: Glossar für Unternehmen
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 95
summary: Verständliche Erklärungen der Begriffe, die im Bereich Unternehmen verwendet werden, von Siteaccess und Sektion bis zu Cache, Velocity, REST API, USt-Typ, Ländergruppe, RTO und Gesamtbetriebskosten, jeweils mit einem Link auf die Stelle, an der sie vollständig erklärt sind.
keywords: Glossar, Begriffe, Definitionen, Siteaccess, Sektion, Rolle, Richtlinie, Inhaltsklasse, Objekt, Knoten, Entwurf, Version, Erweiterung, Cache, View-Cache, statischer Cache, Velocity, REST API, OAuth, API-Schlüssel, USt, Ländergruppe, Workflow, Audit-Trail, SLA, RTO, RPO, TCO, ROI, DSGVO, Extranet, Intranet, headless, glossary, terms, definitions, section, role, policy, content class, object, node, draft, version, extension, view cache, static cache, API key, VAT, country group, audit trail, GDPR
type: help
---
# Glossar für Unternehmen

Die Begriffe, die im Bereich Unternehmen verwendet werden, erklärt für Inhaber und Führungskräfte. Jeder Eintrag
verweist auf die Seite, die ihn vollständig erklärt. Das technische Glossar der gesamten Dokumentation steht unter
[Glossar](../../../eng-us/glossary.md).

## Inhalt

1. [Inhalte und Bearbeiten](#inhalte-und-bearbeiten)
2. [Websites, Benutzer und Berechtigungen](#websites-benutzer-und-berechtigungen)
3. [Verkaufen](#verkaufen)
4. [Die Website betreiben](#die-website-betreiben)
5. [Geschäfts- und Vertragsbegriffe](#geschäfts--und-vertragsbegriffe)

## Inhalte und Bearbeiten

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Inhaltsklasse | Die Art von Dingen, die Sie speichern (Produkt, Artikel, Veranstaltung, Mitglied), mit ihren Feldern. Sie legen Ihre eigenen fest | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Inhaltsobjekt | Ein Element einer Klasse: ein Produkt, ein Artikel | ebenda |
| Knoten (Platzierung) | Wo ein Objekt im Inhaltsbaum steht. Ein Objekt kann mehrere Platzierungen haben | ebenda |
| Entwurf | Eine unveröffentlichte Version, die gerade bearbeitet wird | [Meine Entwürfe und meine ausstehenden Elemente](../../anleitungen/entwuerfe-und-ausstehende-objekte.md) |
| Version | Jeder gespeicherte Stand eines Objekts. Ältere Versionen lassen sich vergleichen und wiederherstellen | [Versionen eines Objekts: vergleichen, wiederherstellen, aufräumen](../../anleitungen/inhaltsverlauf.md) |
| Workflow | Schritte, die ablaufen, wenn etwas geschieht, zum Beispiel eine Freigabe vor dem Veröffentlichen oder die Zahlung an der Kasse | [Workflows](../../anleitungen/workflows.md) |
| Exponential Layouts | Der visuelle Seitenbaukasten: Layouts, Zonen und Blöcke, die Redakteure anordnen | [Der Layout-Editor](../layout-editor.md) |
| Exponential Intelligence | Der Schreibassistent; schreibt nur Entwürfe und veröffentlicht nie selbst | [Exponential Intelligence](../../../eng-us/features/6.0/extensions/expai.md) |
| Informationssammler | Lässt eine Seite Formulareingaben von Besuchern sammeln (Kontaktformulare, Anmeldungen) | [Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md) |

## Websites, Benutzer und Berechtigungen

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Siteaccess | Ein Zugang zur Installation: eine öffentliche Website, eine Sprachversion, die Administration, ein Partnerportal. Gewählt über Hostnamen, Pfad oder Port | [Siteaccess-Zuordnung nach Host und Sprache](../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md) |
| Multisite | Mehrere Websites aus einer Installation | [Marken mit Multisite](multisite/README.md) |
| Rolle | Eine benannte Menge von Berechtigungen, Benutzern oder Gruppen zugewiesen | [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md) |
| Richtlinie | Eine Berechtigung innerhalb einer Rolle, etwa „Inhalte lesen“, auf Wunsch eingeschränkt | ebenda |
| Sektion | Eine Kennzeichnung eines Teils des Inhaltsbaums, mit der Berechtigungen und Designs eingeschränkt werden (zum Beispiel „Nur für Partner“) | [Sektionen](../../anleitungen/sektionen.md) |
| Objektzustand | Ein Status von Inhalten (zum Beispiel „freigegeben“, „archiviert“), von dem Berechtigungen abhängen können | [Objektzustände](../../anleitungen/objektzustaende.md) |
| Intranet | Eine Website nur für Mitarbeiter | [Intranets](intranets/README.md) |
| Extranet | Eine Website für angemeldete Externe: Partner, Händler, Lieferanten, Kunden | [Extranets und Partnerportale](extranets/README.md) |
| Zweistufige Anmeldung | Anmeldung mit einem Passwort und einem zweiten Code | [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md) |

## Verkaufen

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Shop | Der eingebaute E-Commerce: Produkte, Warenkorb, Bestellungen | [Online-Shops](shops/README.md) |
| USt-Typ | Ein Steuersatz, dem Produkte zugeordnet werden | [USt-Regeln und USt-Typen](../../../eng-us/help/businesses/stores/vat-rules-and-vat-types.md) |
| Ländergruppe | Eine Menge von Ländern, die gleich behandelt werden, zum Beispiel bei der USt | [Ländergruppen](../../../eng-us/features/6.0/country-groups.md) |
| Zahlungsanbieter | Die Anbindung an einen Zahlungsdienstleister, ausgeführt als Workflow-Ereignis an der Kasse | [Zahlungsanbieter für Unternehmen](shops/zahlungsanbieter-fuer-unternehmen.md) |
| Bestellstatus | Wo eine Bestellung in ihrem Ablauf steht: ausstehend, in Bearbeitung, geliefert und weitere, die Sie festlegen | [Bestellungen und Bestellstatus](shops/bestellungen-und-bestellstatus.md) |
| Headless | Die Inhalte ohne die Website nutzen, zum Beispiel aus einer App | [Mobil und headless](mobil-und-headless/README.md) |
| REST API | Die Schnittstelle, über die Apps und andere Systeme Inhalte lesen und schreiben | [REST API v1 und v2: eine Extension](../../anleitungen/rest-api-v1-und-v2.md) |
| API-Schlüssel / OAuth | Zwei Wege, auf denen eine App sich gegenüber der REST API ausweist | [Persönliche API-Schlüssel: über REST veröffentlichen, ohne Passwort](../../anleitungen/api-schluessel.md) |
| Feed (RSS, ATOM) | Eine maschinenlesbare Liste neuer Inhalte, die andere Websites und Apps aufgreifen | [RSS-Feeds: Exporte veröffentlichen und Importe einlesen](../../anleitungen/rss-feeds.md) |
| Syndication | Einen Teil des Inhaltsbaums mit einer anderen Installation teilen | [syndication](../../../eng-us/features/6.0/extensions/syndication.md) |

## Die Website betreiben

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Velocity | Der eigene Anwendungsserver von Exponential, der die Anwendung zwischen den Anfragen geladen hält | [Velocity für Unternehmen](../../../eng-us/help/businesses/reliability/velocity-for-businesses.md) |
| Cache | Gespeicherte Ergebnisse, die Seiten schnell machen; werden geleert, wenn sich Inhalte ändern | [Caches: was jeder enthält und wie Sie ihn leeren](../../anleitungen/caches.md) |
| View-Cache | Die zwischengespeicherte Ausgabe einer Inhaltsseite | [Caching für Verfügbarkeit](zuverlaessigkeit/caching-fuer-verfuegbarkeit.md) |
| Statischer Cache | Seiten, die als einfache Dateien auf die Festplatte geschrieben werden | [Statischer Cache und Antwortcache](../../../eng-us/help/businesses/reliability/static-and-response-cache.md) |
| Cronjob | Eine Aufgabe, die nach Zeitplan läuft, zum Beispiel das Versenden von Benachrichtigungen | [Cronjobs: ausführen, planen und verfolgen](../../anleitungen/cronjobs.md) |
| Wartungsmodus | Eine Seite, die Besuchern angezeigt wird, während gearbeitet wird | [Wartungsmodus: die Site vom Netz nehmen und zurückholen](../../anleitungen/wartungsmodus.md) |
| Audit-Trail | Ein manipulationssicheres Protokoll darüber, wer was geändert hat | [Der Audit-Trail](../../../eng-us/features/6.0/audit-trail.md) |
| Upgrade-Prüfung | Eine Prüfung der Datenbank und der Dateien gegen das Release vor einem Upgrade | [Die Upgrade-Prüfung: Dateien und Datenbank vor einem Upgrade](../../anleitungen/upgrade-pruefung.md) |
| Erweiterung | Ein Paket, das Funktionen hinzufügt | [Extensions: finden, installieren, konfigurieren, bauen und veröffentlichen](../../anleitungen/extensions.md) |
| Cluster | Mehrere Server, die sich die Arbeit und die Dateien teilen | [Clustering und Hochverfügbarkeit](zuverlaessigkeit/clustering-und-hochverfuegbarkeit.md) |

## Geschäfts- und Vertragsbegriffe

Das sind allgemeine Begriffe, nicht spezifisch für Exponential.

| Begriff | Bedeutung | Lesen |
|---|---|---|
| TCO (Total Cost of Ownership, Gesamtbetriebskosten) | Alle Kosten eines Systems über seine Lebensdauer, nicht nur Kauf oder Aufbau | [Gesamtbetriebskosten](kosten/gesamtbetriebskosten.md) |
| ROI (Return on Investment, Rendite) | Was ein System im Vergleich zu seinen Kosten einbringt oder einspart | [Rendite der Stabilität](kosten/rendite-der-stabilitaet.md) |
| SLA (Service Level Agreement) | Eine schriftliche Zusage zu Verfügbarkeit, Reaktions- und Wiederherstellungszeiten | [Grundlagen zu Verträgen und SLAs](auswahl/grundlagen-zu-vertraegen-und-slas.md) |
| RTO (Recovery Time Objective) | Wie lange es nach einem Ausfall dauern darf, bis alles wieder läuft | [Backups, die sich wiederherstellen lassen](zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md) |
| RPO (Recovery Point Objective) | Wie viele aktuelle Daten Sie sich zu verlieren leisten können; bestimmt, wie oft Sie sichern | ebenda |
| DSGVO | Die Datenschutz-Grundverordnung der Europäischen Union | [DSGVO-Grundlagen](../../../eng-us/help/businesses/security/gdpr-basics.md) |
| GPL | Die GNU General Public License, die Lizenz von Exponential | [Lizenzen für Käufer erklärt: GPL-Code, GFDL-Dokumentation](../geschaeft/lizenzen-fuer-kaeufer.md) |
| GFDL | Die GNU Free Documentation License, die Lizenz dieser Dokumentation | [Freie Dokumentation: Nutzen Sie sie, um sich selbst und anderen zu helfen](../freie-dokumentation.md) |

## Verwandte Seiten

- [Unternehmen: hier beginnen](unternehmen-hier-beginnen.md)
- [FAQ für Unternehmen](faq-fuer-unternehmen.md)
- [Glossar für Hoster](../hosting/glossar-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
