---
title: Eine Website hinzufügen
audience: customer, businesses, hosting
category: customer-success
order: 7
summary: Was ein Website-Betreiber organisiert, wenn einer bereits laufenden Exponential-Installation eine zweite Marke, eine Region oder eine Microsite hinzugefügt wird - das Siteaccess-Modell in Kürze, was das Unternehmen bereitstellen muss, wer welchen Schritt erledigt, gemeinsame Inhalte und Teams und der erste Monat.
keywords: Website hinzufügen, Multisite, zweite Marke, Microsite, Siteaccess, Domain, Zertifikat, gemeinsame Inhalte, Design pro Marke, Website-Betreiber, adding a site, second brand, microsite, domain, certificate, shared content, per-brand design, site owner
type: help
---
# Eine Website hinzufügen

Diese Seite ist für den Betreiber, der eine zweite Marke, eine regionale Website oder eine Kampagnen-Microsite auf der
Installation möchte, die die Organisation bereits betreibt. Eine Exponential-Installation kann viele Websites
ausliefern: Jede ist ein **Siteaccess**, ein Zugang zur Installation mit eigenem Namen, eigener Adresse, Sprachliste,
eigenem Design und eigener Startseite, wobei Code und Datenbank meist gemeinsam genutzt werden
([Eine Installation, viele Websites](../../../../en/us/help/businesses/multisite/one-installation-many-sites.md)). Ihr Partner und Ihr
Hoster erledigen die technischen Schritte; Sie liefern die Entscheidungen, die Domain und die Inhalte.

## Inhalt

1. [Ist eine neue Website die richtige Antwort?](#ist-eine-neue-website-die-richtige-antwort)
2. [Was das Unternehmen organisieren muss](#was-das-unternehmen-organisieren-muss)
3. [Die Schritte, und wer sie erledigt](#die-schritte-und-wer-sie-erledigt)
4. [Teams und gemeinsame Inhalte](#teams-und-gemeinsame-inhalte)
5. [Der erste Monat](#der-erste-monat)

## Ist eine neue Website die richtige Antwort?

| Sie möchten | Oft genügt | Eine neue Website, wenn |
|---|---|---|
| Eine Kampagne | eine Landingpage mit einer Zeitregel ([Landingpages mit Layouts](landingpages-mit-layouts.md)) | sie über Monate eine eigene Domain und ein eigenes Design braucht |
| Eine Sprache | ein Siteaccess für die Sprache ([Eine Sprache hinzufügen](eine-sprache-hinzufuegen.md)) | der Markt andere Inhalte braucht, keine Übersetzung |
| Seiten einer Abteilung | ein Ordner und eine Sektion | die Abteilung nach außen eine eigene Marke ist |
| Einen Partner- oder Mitgliederbereich | eine Sektion nur für Mitglieder ([Mitgliederbereiche und Registrierung](mitgliederbereiche-und-registrierung.md)) | er eine eigene Adresse und Anmeldung braucht |

## Was das Unternehmen organisieren muss

Aus [Eine Installation, viele Websites](../../../../en/us/help/businesses/multisite/one-installation-many-sites.md):

- [ ] **Domainnamen**: registriert und auf den Server zeigend.
- [ ] **Zertifikate**: Jeder Hostname, den die Marke verwendet, ist durch HTTPS abgedeckt.
- [ ] **Konten für Analyse und Search Console** pro Marke, wenn Sie Marken getrennt messen.
- [ ] **Rechtliche Seiten** pro Marke (Impressum, Datenschutzerklärung, Bedingungen), vom Markenverantwortlichen verfasst.
- [ ] **Ein Markenverantwortlicher**, der Inhalte und den Start freigibt.

## Die Schritte, und wer sie erledigt

Zusammengefasst aus derselben Seite:

| Schritt | Wer |
|---|---|
| 1. Ein Ordner für die Marke unterhalb der Wurzel | Redakteur oder Website-Ersteller |
| 2. Ein Einstellungsverzeichnis für die Marke: Name, Adresse, Startseite, Design | Entwickler |
| 3. Die Marke in die Liste der Siteaccesses aufgenommen, ihr Host oder Pfad ihr zugeordnet | Entwickler oder Administrator |
| 4. Der Hostname im DNS, im Webserver oder in Velocity, und das Zertifikat | Hosting |
| 5. Caches geleert und die neue Adresse getestet | Administrator |
| 6. Die Marke in die Website-Liste der Administration aufgenommen, damit Vorschauen und Links sie verwenden können | Administrator |

Die genauen Einstellungen stehen unter [Mehrere Websites in einer Installation einrichten](../../../../en/us/help/creators/website-creators/multisite-setup.md).
Ein Testhost oder eine Staging-Adresse lässt Sie die Marke sehen, bevor sie öffentlich ist.

## Teams und gemeinsame Inhalte

- Jede Marke hat ihre eigene Startseite und ihr eigenes Menü; Redakteure arbeiten im Ordner der Marke, und Rollen
  können sie dort halten ([Eine Installation, viele Websites](../../../../en/us/help/businesses/multisite/one-installation-many-sites.md)).
- Inhalte lassen sich bewusst zwischen Marken teilen, über einen zweiten Ort oder über Beziehungen
  ([Gemeinsame Inhalte über Marken hinweg](../../../../en/us/help/businesses/multisite/shared-content-across-brands.md)).
- Jede Marke kann ihr eigenes Design haben, aufgebaut auf der Fallback-Kette der Designs
  ([Designs pro Marke](../../../../en/us/help/businesses/multisite/per-brand-designs.md)).
- Einstellungen, Logs und Caches lassen sich pro Website trennen
  ([Einstellungen und Logs pro Website](../../../../en/us/help/businesses/multisite/per-site-settings-and-logs.md)).
- Links zwischen den Marken: [Website-übergreifende Links](../../../../en/us/help/businesses/multisite/cross-site-links.md).

Aktualisieren Sie das [Arbeitsblatt für Teamrollen](../alltag/arbeitsblatt-teamrollen.md): wer welche Marke
bearbeitet, wer freigibt.

## Der erste Monat

- [ ] Die wöchentliche Routine umfasst die neue Marke ([Wöchentliche Routine des Betreibers](../alltag/woechentliche-routine-des-betreibers.md)).
- [ ] Suchstatistiken werden im neuen öffentlichen Siteaccess aufgezeichnet, wenn Sie sie verwenden.
- [ ] Die Sitemap der neuen Marke wird erzeugt und eingereicht ([XML-Sitemaps](../../../../en/us/help/creators/website-creators/sitemaps.md)).
- [ ] Backups und Überwachung umfassen den neuen Hostnamen; fragen Sie Ihren Hoster.
- [ ] Die vollständige [Checkliste für mehrere Marken](../../../../en/us/help/businesses/multisite/multi-brand-checklist.md) ist erledigt.

## Verwandte Seiten

- [Planung mehrerer Marken](../../../../en/us/help/businesses/multisite/multi-brand-planning.md)
- [Multisite in einer Installation für Hoster](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md)
- [Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
