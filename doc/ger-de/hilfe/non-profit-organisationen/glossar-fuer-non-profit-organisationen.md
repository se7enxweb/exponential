---
title: Glossar für Non-Profit-Organisationen
audience: non-profit, businesses, government, editor
category: non-profits
order: 95
summary: Die Begriffe, denen eine Non-Profit-Organisation beim Betrieb ihrer Website mit Exponential begegnet, in einfacher Sprache erklärt - von Administrationsoberfläche, Audit-Protokoll und CAPTCHA bis zu Informationssammler, Objektzustand, Rolle, Bereich, Siteaccess, SQLite, Velocity und Version - jeweils mit der Seite, die den Begriff ausführlich erklärt.
keywords: Glossar, Begriffe, Wörter, Definitionen, Non-Profit-Organisation, Administrationsoberfläche, Audit-Protokoll, CAPTCHA, Klasse, gesammelte Informationen, Cronjob, Double-Opt-in, Erweiterung, Informationssammler, Layout, Objekt, Rolle, Bereich, Siteaccess, SQLite, Velocity, Version, glossary, terms, non-profit, administration interface, audit trail, class, collected information, double opt-in, extension, information collector, object, role, section
type: help
---
# Glossar für Non-Profit-Organisationen

Die Begriffe, die dieser Bereich verwendet, alphabetisch nach dem englischen Begriff geordnet. Jeder Eintrag sagt, was
der Begriff für eine Non-Profit-Organisation bedeutet, und verlinkt auf die Seite, die ihn erklärt. Die allgemeinen
Glossare sind das [Glossar für Unternehmen](../unternehmen/glossar-fuer-unternehmen.md), das
[Glossar für Kunden](../kunden/glossar-fuer-kunden.md) und das [Exponential-Glossar](../../glossar.md).

## Inhalt

1. [A bis D](#a-bis-d)
2. [E bis L](#e-bis-l)
3. [M bis R](#m-bis-r)
4. [S bis Z](#s-bis-z)

## A bis D

**Administrationsoberfläche (Administration interface).** Der Teil der Website, in dem Mitarbeitende und Ehrenamtliche
im Browser Inhalte bearbeiten und Einstellungen ändern. Siehe den [Rundgang durch die Administration](../rundgang-durch-die-administration.md).

**Alternativtext (Alternative text).** Eine kurze Beschreibung eines Bildes für Menschen, die es nicht sehen können.
Bildfelder können so eingestellt werden, dass er verpflichtend ist; siehe
[Alternativtexte für Bilder](../verwaltung/barrierefreiheit/alternativtext-fuer-bilder.md).

**Freigabe-Workflow (Approval workflow).** Eine Einstellung, die neue oder geänderte Inhalte zurückhält, bis eine
benannte Person sie freigibt. Siehe [Freigaben und Prüfung](../kunden/alltag/freigabe-und-pruefung.md).

**Audit-Protokoll (Audit trail).** Eine manipulationssichere Aufzeichnung, wer was in der Administrationsoberfläche
getan hat: Anmeldungen, Veröffentlichungen, Entfernungen, Einstellungen, Exporte. Standardmäßig eingeschaltet. Siehe
[Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md).

**CAPTCHA.** Ein kleiner Test in einem Formular, der automatisierten Spam fernhält. Exponential liefert zwei mit:
`hcaptcha` und `recaptcha`. Siehe [Spamschutz für Ehrenamtsformulare](ehrenamtliche/spamschutz-fuer-ehrenamtsformulare.md).

**Klasse (Class).** Die Definition einer Art von Inhalt: ein Artikel, eine Veranstaltung, eine Datei, ein
Ehrenamtsformular. Jede Klasse führt ihre Felder (Attribute) auf. Siehe
[Inhaltsklassen für Websites](../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md).

**Gesammelte Informationen (Collected information).** Die über Formulare gesendeten Einsendungen, einzusehen unter
**Setup > Gesammelte Informationen**. Siehe [Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md).

**Cronjob.** Eine Aufgabe, die der Server nach Zeitplan ausführt: Newsletter versenden, zu einem Datum
veröffentlichen, aufräumen. Läuft Cron nicht, geschieht nichts davon. Siehe [Cronjobs](../../anleitungen/cronjobs.md).

**Double-Opt-in (Double opt-in).** Ein Abonnent bestätigt das Abonnement mit einem Klick auf einen Link in einer
E-Mail, bevor er Newsletter erhält. `cjw_newsletter` arbeitet so. Siehe
[Unterstützer-Newsletter](kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md).

**Entwurf (Draft).** Eine Version in Bearbeitung, die noch nicht veröffentlicht ist. Siehe
[Entwürfe, Versionen und Rückgängigmachen](../kunden/alltag/entwuerfe-versionen-und-rueckgaengig.md).

## E bis L

**Erweiterung (Extension).** Ein Paket, das Funktionen hinzufügt. Die in diesem Bereich genannten Erweiterungen
(`cjw_newsletter`, `ezpaypal`, `ezauthorize`, `hcaptcha`, `recaptcha`, `enhancedezbinaryfile`, `ezwebin`) werden über
`composer.json` installiert.

**Exponential Layouts.** Der visuelle Seitenbaukasten für Landing- und Kampagnenseiten. Siehe
[Landingpages mit Layouts](../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md).

**Exponential Intelligence.** Ein Schreibassistent in der Administrationsoberfläche, der nur Entwürfe schreibt und nie
veröffentlicht. Siehe [Exponential Intelligence](../kunden/wachstum/exponential-intelligence.md).

**Informationssammler (Information collector).** Der Mechanismus hinter Formularen: Felder einer Klasse, die als
„Informationssammler“ markiert sind, werden zu einem Formular, und jede Einsendung wird gespeichert. Siehe
[Formulare mit dem Informationssammler](../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md).

**Layout-Regel (Layout rule).** Eine Regel, die festlegt, welches Layout eine Seite verwendet; sie kann eine
Zeitbedingung tragen, sodass sich eine Kampagne nach Datum ein- und ausschaltet. Siehe
[Layout-Regeln](../ersteller/website-ersteller/layout-regeln.md).

## M bis R

**Mitgliederbereich (Members area).** Seiten, die nur angemeldete Benutzer einer Gruppe sehen können, gebaut mit
Bereichen und Rollen. Siehe [Mitgliederregistrierung und Mitgliederbereiche](../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md).

**Objekt (Object).** Ein einzelner Inhalt: ein Artikel, eine Veranstaltung, eine Datei mit einem Jahresbericht.

**Objektzustand (Object state).** Eine Kennzeichnung wie „Entwurf zur Prüfung“ oder „archiviert“, die Rollen verwenden
können. Siehe [Bereiche und Objektzustände](../ersteller/website-ersteller/sektionen-und-objektzustaende.md).

**Partner.** Ein Unternehmen im Exponential-Partnerprogramm, das Websites baut, hostet oder betreut. Siehe
[Mit einem Partner arbeiten](betrieb/mit-einem-partner-arbeiten.md).

**Zahlungs-Gateway (Payment gateway).** Die Verbindung zwischen dem Shop und einem Zahlungsanbieter: `ezpaypal`
(PayPal) und `ezauthorize` (Authorize.Net). Siehe [Zahlungs-Gateways](../ersteller/website-ersteller/payment-gateways.md).

**Rolle (Role).** Eine Gruppe von Berechtigungen, die einem Benutzer oder einer Gruppe zugewiesen wird, gegebenenfalls
beschränkt auf einen Bereich oder einen Teil des Baums. Siehe [Rollen für Ihr Team](../kunden/alltag/rollen-fuer-ihr-team.md).

**RSS-Feed (RSS feed).** Eine maschinenlesbare Liste neuer Einträge, der Leser und andere Websites folgen können. Siehe
[RSS-Feeds für Ihre Website](../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md).

## S bis Z

**Bereich (Section).** Eine Kennzeichnung eines Teils des Inhaltsbaums (zum Beispiel „Vorstand“ oder „Nur für
Ehrenamtliche“), nach der Rollen einschränken können. Siehe
[Bereiche und Objektzustände](../ersteller/website-ersteller/sektionen-und-objektzustaende.md).

**Siteaccess.** Ein Zugang zur Installation, etwa die öffentliche Website, eine zweite Sprache oder die
Administrationsoberfläche.

**SQLite.** Die voreingestellte Datenbank: Die ganze Website liegt in einer Datei, ohne Datenbankserver. Siehe
[SQLite-Datenbankunterstützung](../../funktionen/6.0/sqlite-datenbank.md).

**Zwei-Faktor-Anmeldung (Two-factor login).** Anmeldung mit einem Passwort und einem Code aus einer Smartphone-App.
Siehe [Zwei-Faktor-Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md).

**Velocity.** Der eigene, in PHP geschriebene Webserver von Exponential. Siehe
[Der Webserver Velocity](../../funktionen/6.0/velocity-webserver.md).

**Version.** Bei jeder Veröffentlichung eines Inhalts wird der vorherige Stand als Version aufbewahrt, die verglichen
oder wiederhergestellt werden kann. Siehe [Versionen eines Objekts](../../anleitungen/inhaltsverlauf.md).

## Verwandte Seiten

- [Non-Profit-Organisationen: hier beginnen](non-profit-organisationen-hier-beginnen.md)
- [FAQ für Non-Profit-Organisationen](faq-fuer-non-profit-organisationen.md)
- [Glossar für Unternehmen](../unternehmen/glossar-fuer-unternehmen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
