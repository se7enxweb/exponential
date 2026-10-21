---
title: Extranet-FAQ
audience: businesses, project-manager, partner
category: business-solutions
order: 90
summary: Kurze Antworten mit Quellen auf die Fragen, die Führungskräfte zu Partner-, Kunden-, Händler- und Lieferantenportalen auf Exponential stellen - Trennung zwischen Partnern, Registrierung und Freigabe, eigene Adressen, Dateien, Bestellen, Anmeldung und Prüfungen.
keywords: Extranet-FAQ, Fragen zum Partnerportal, Kundenportal, Händlerportal, Lieferantenportal, Trennung, Freigabe, Registrierung, eigener Host, Dateien, Ermäßigungsgruppen, Zwei-Faktor, Single Sign-on, Berechtigungsprüfung, extranet FAQ, partner portal questions, customer portal, dealer portal, supplier portal, separation, approval, registration, separate host, files, discount groups, two-factor, single sign-on, permissions review
type: help
---
# Extranet-FAQ

Diese Seite ist für Führungskräfte und Projektleitungen, die schnelle Antworten zu Portalen für Menschen außerhalb des
Unternehmens brauchen. Jede Antwort ist kurz und verlinkt die Seite mit den Einzelheiten.

## Inhalt

1. [Trennung und Zugriff](#trennung-und-zugriff)
2. [Konten](#konten)
3. [Adresse und Design](#adresse-und-design)
4. [Dateien und Bestellen](#dateien-und-bestellen)
5. [Sicherheit und Nachweise](#sicherheit-und-nachweise)
6. [Verwandte Seiten](#verwandte-seiten)

## Trennung und Zugriff

**Kann ein Partner die Dokumente eines anderen Partners sehen?**
Nicht, wenn der Ordner jeder Organisation nur über eine Rolle lesbar ist, die der Gruppe dieser Organisation mit einer
Teilbaum-Einschränkung zugeordnet ist, und das getestet wurde
([Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md)).

**Brauchen wir für jeden Partner eine eigene Rolle?**
Nein. Eine Rolle, die der Gruppe jeder Organisation mit deren eigener Teilbaum-Einschränkung zugeordnet wird, bedient
jede Organisation ([Rollen und Richtlinien, 5](../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).

**Können Gold-Partner mehr sehen als Silber-Partner?**
Ja, mit einer Sektion pro Stufe und einer Leserolle pro Stufe
([Kunden- und Partnerbereiche, Stufen mit Sektionen](kunden-und-partnerbereiche.md#stufen-mit-sektionen)).

**Kann ein Eintrag von intern zu Partnern zu öffentlich wechseln, ohne verschoben zu werden?**
Ja, mit einer Gruppe von Objektzuständen, die in den Leserichtlinien verwendet wird
([Kunden- und Partnerbereiche, Wechselnde Zielgruppen](kunden-und-partnerbereiche.md#wechselnde-zielgruppen-mit-objektzuständen)).

**Zeigt die Suche Partnern Dinge, die sie nicht lesen dürfen?**
Nein. Suchergebnisse folgen den Berechtigungen ([Suche im Intranet](../intranets/suche-im-intranet.md)).

## Konten

**Können sich Partner selbst registrieren?**
Ja, wenn die Rolle Anonymous `user/register` hat. Neue Installationen geben das standardmäßig nicht
([Rollen und Richtlinien, 8](../../../anleitungen/rollen-und-richtlinien.md#8-festlegen-wer-sich-registrieren-und-wer-knotendaten-lesen-darf)).

**Können wir jede Registrierung freigeben, bevor sie wirkt?**
Ja. Die Mail an den Administrator enthält signierte Links **Freigeben** und **Ablehnen**; mit einem Freigabe-Workflow
wartet das Konto, bis jemand entscheidet ([Registrierung und Kontofreigabe](registrierung-und-kontofreigabe.md)).

**Sind die Freigabelinks in der Mail sicher, wenn ein Mailscanner sie öffnet?**
Ja. Das Öffnen eines Links ändert nichts; die freigebende Person muss mit der richtigen Richtlinie angemeldet sein und
durch einen Klick auf eine Schaltfläche bestätigen. Die Links sind signiert, laufen standardmäßig nach 7 Tagen ab und
geben nur einmal frei oder lehnen nur einmal ab
([Signierte Links, Security](../../../../eng-us/features/6.0/registration-admin-links.md#security)).

**Wie schließen wir Konten, wenn ein Vertrag endet?**
Entfernen Sie die Zuordnungen der Organisation, deaktivieren Sie dann ihre Konten oder deaktivieren und verschieben Sie
sie ([Berechtigungen im Extranet prüfen, Wenn eine Partnerschaft endet](berechtigungen-im-extranet-pruefen.md#wenn-eine-partnerschaft-endet)).

**Können sich Partner mit ihren eigenen Firmenkonten anmelden?**
Single Sign-on mit dem Identitätsanbieter eines Partners ist nicht eingebaut. Der Kernel hat Login-Handler für LDAP und
Textdateien und einen Erweiterungspunkt für Single-Sign-on-Handler
([Verzeichnisanmeldung und LDAP](../intranets/verzeichnisanmeldung-und-ldap.md)). Die Erweiterung
`sevenx_authentication_2fa` bietet Social Login mit Anbietern wie Google, standardmäßig ausgeschaltet
([Zwei-Faktor und Anmeldung, Social Login](../intranets/zwei-faktor-und-anmeldung.md#social-login)).

## Adresse und Design

**Kann das Portal eine eigene Adresse haben?**
Ja. Ein über den Host zugeordneter Siteaccess, etwa `partners.example.com`, in derselben Installation wie die
öffentliche Website ([Getrennte Hosts und Siteaccesses](getrennte-hosts-und-siteaccesses.md)).

**Können Partner das Portal nutzen, sich aber nicht auf der öffentlichen Website anmelden, oder umgekehrt?**
Ja. `user/login` lässt sich in jeder Rolle nach Siteaccess einschränken (`kernel/user/module.php`).

**Können Partner nach der Anmeldung auf ihrer eigenen Seite landen?**
Ja, mit `LoginRedirectionUriAttribute[group]`, einem Textattribut der Benutzergruppe, das die Adresse enthält
([Kunden- und Partnerbereiche](kunden-und-partnerbereiche.md#partner-nach-der-anmeldung-in-ihren-bereich-schicken)).

**Kann das Portal anders aussehen als die öffentliche Website?**
Ja. Jeder Siteaccess hat sein eigenes Design, und Templates können sich nach Sektion unterscheiden
([Getrennte Hosts und Siteaccesses, Ein eigenes Aussehen](getrennte-hosts-und-siteaccesses.md#ein-eigenes-aussehen)).

## Dateien und Bestellen

**Ist ein weitergeleiteter Download-Link ein Risiko?**
Nein. Jeder Download prüft, ob die Person die Datei lesen darf
([Dateien mit Partnern teilen](dateien-mit-partnern-teilen.md)).

**Können Partner Dateien hochladen?**
Ja, in ihren eigenen Ordner, mit einer Rolle, die auf die Klasse File und ihren Teilbaum beschränkt ist
([Dateien mit Partnern teilen, Partner hochladen lassen](dateien-mit-partnern-teilen.md#partner-hochladen-lassen)).

**Können Händler zu ihren eigenen Preisen bestellen?**
Sie können über den eingebauten Shop bestellen, und Ermäßigungsgruppen geben Benutzergruppen einen prozentualen
Nachlass auf Produkte oder Klassen. Feste Preislisten pro Händler sind keine dokumentierte Funktion
([Händler- und Lieferantenportale](haendler-und-lieferantenportale.md)).

**Verwaltet der Shop Lagerbestände oder Rechnungen?**
Nein. Lagerbestand, Buchhaltungsrechnungen, Versandetiketten und Marktplätze sind Erweiterungen oder Integrationen
([Einen Shop betreiben, What the shop does not do](../../../../eng-us/help/creators/website-creators/shop-overview.md#what-the-shop-does-not-do)).

## Sicherheit und Nachweise

**Können wir die zweistufige Anmeldung für Partner vorschreiben?**
Ja, mit `sevenx_authentication_2fa` und `Enforce2FA=enabled`
([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).

**Können wir belegen, wer Zugriff hatte und was sich geändert hat?**
Der Audit-Trail zeichnet Anmeldungen, Ablehnungen, Rollenzuordnungen und Inhaltsänderungen in einem
manipulationssicheren Log auf ([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

**Wie oft sollten wir Zugriffe prüfen?**
In einem festen Rhythmus, den Sie mit Ihrem Sicherheitsbeauftragten vereinbaren; die Routine dafür steht unter
[Berechtigungen im Extranet prüfen](berechtigungen-im-extranet-pruefen.md).

## Verwandte Seiten

- [Extranets und Partnerportale](README.md)
- [Intranet-FAQ](../intranets/intranet-faq.md)
- [FAQ zu Mitgliederwebsites](../mitglieder/faq-zu-mitgliederwebsites.md)
- [FAQ für Unternehmen](../faq-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
