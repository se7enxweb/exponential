---
title: Startcheckliste für Privatpersonen
audience: individual, website-creator, customer
category: personal-sites
order: 1652
summary: Die Checkliste, die eine Person an dem Tag durchgeht, an dem eine persönliche Website, ein Portfolio, ein Blog, eine Familienseite oder eine Vereinsseite mit Exponential online geht - Konten und Sicherheit, Inhalte, Adresse und HTTPS, Formulare und E-Mail, Berechtigungen, Suchmaschinen, Sicherungen und die erste Woche nach dem Start.
keywords: Start, online gehen, Checkliste, Starttag, Konten, Passwort, Zwei-Faktor, HTTPS, Domain, Formulare, E-Mail, Berechtigungen, privater Bereich, Suchmaschinen, Sitemap, Sicherung, Privatperson, launch, go live, checklist
type: help
---
# Startcheckliste für Privatpersonen

Diese Seite ist für Privatpersonen, die ihre Website gleich öffentlich machen. Gehen Sie sie von oben nach unten
durch; jede Zeile verweist auf die Seite, die sie erklärt. Die Fassung für Website-Ersteller mit mehr technischen
Einzelheiten ist die [Startcheckliste für Website-Ersteller](../../ersteller/website-ersteller/checkliste-fuer-den-start.md).

## Inhalt

1. [Vor dem Umzug zum Hoster](#vor-dem-umzug-zum-hoster)
2. [Konten und Sicherheit](#konten-und-sicherheit)
3. [Adresse und HTTPS](#adresse-und-https)
4. [Inhalte](#inhalte)
5. [Formulare und E-Mail](#formulare-und-e-mail)
6. [Berechtigungen und Privatsphäre](#berechtigungen-und-privatsphäre)
7. [Suchmaschinen und Feeds](#suchmaschinen-und-feeds)
8. [Sicherungen](#sicherungen)
9. [Die erste Woche](#die-erste-woche)

## Vor dem Umzug zum Hoster

- [ ] Die echte Website auf dem `_clean`-Paket gebaut oder Demo-Inhalte entfernt ([Ein Seitenpaket und ein Theme wählen](../setup/ein-seitenpaket-und-ein-theme-waehlen.md)).
- [ ] Hoster geprüft: PHP-Version, Erweiterungen, Cron, E-Mail, HTTPS ([Shared Hosting oder ein kleiner Server](../setup/shared-hosting-oder-ein-kleiner-server.md)).
- [ ] Eine vollständige Sicherung der lokalen Website ([Sicherungen für eine Person](../im-betrieb/sicherungen-fuer-eine-person.md)).

## Konten und Sicherheit

- [ ] Standardpasswort des Administrators geändert ([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)).
- [ ] Zweistufige Anmeldung für Ihr Administratorkonto ([Zwei-Faktor-Anmeldung](../im-betrieb/zwei-faktor-anmeldung.md)).
- [ ] Ein Vertretungs- oder Notfall-Administratorkonto existiert.
- [ ] Testkonten entfernt oder deaktiviert.
- [ ] Debug-Ausgabe auf der Live-Website ausgeschaltet ([Grundlagen zu Spam und Sicherheit](../im-betrieb/grundlagen-zu-spam-und-sicherheit.md)).

## Adresse und HTTPS

- [ ] Domain zeigt auf den Server; `www` und der Name ohne `www` verhalten sich wie beschlossen ([Domainnamen und HTTPS](../setup/domainnamen-und-https.md)).
- [ ] Die Website-URL und die Host-Einstellungen von Exponential verwenden die echte Adresse ([Livegang](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).
- [ ] HTTPS auf jeder Seite; `http://` wird weitergeleitet.
- [ ] Die Administration ist unter ihrer Adresse über HTTPS erreichbar.

## Inhalte

- [ ] Startseite, Über mich, Kontakt und Datenschutzhinweis veröffentlicht.
- [ ] Jede Seite hat einen klaren Titel und eine Einleitung ([Für die eigene Website schreiben](../persoenliche-seiten/fuer-die-eigene-website-schreiben.md)).
- [ ] Jedes Bild hat einen Alternativtext.
- [ ] Menü vollständig; keine Links zur Demo oder zu Ihrer lokalen Adresse ([Link- und Inhaltsprüfungen](../../ersteller/website-ersteller/link-und-inhaltspruefungen.md)).
- [ ] Auf einem Smartphone geprüft.

## Formulare und E-Mail

- [ ] Kontaktformular sendet an die richtige Adresse; die E-Mail kommt an ([Über-mich-Seite und Kontaktformular](../persoenliche-seiten/ueber-mich-seite-und-kontaktformular.md)).
- [ ] E-Mails zur Registrierung und zum Zurücksetzen des Passworts kommen an, falls genutzt ([Mitglieder und Registrierung](../vereinsseiten/mitglieder-und-registrierung.md)).
- [ ] Formular-Token aktiv; CAPTCHA bei öffentlichen Formularen und Kommentaren ([Grundlagen zu Spam und Sicherheit](../im-betrieb/grundlagen-zu-spam-und-sicherheit.md)).
- [ ] Cron läuft, wenn Sie Newsletter oder zeitgesteuertes Veröffentlichen nutzen ([Cronjobs](../../../anleitungen/cronjobs.md)).

## Berechtigungen und Privatsphäre

- [ ] Die Rolle *Anonymous* erlaubt nur, was Sie möchten; Kommentarregel entschieden ([Kommentare in Ihrem Blog](../blogs/kommentare-in-ihrem-blog.md)).
- [ ] Privater Bereich als Fremder getestet ([Private Bereiche mit Rollen und Sektionen](../familienseiten/private-bereiche-mit-rollen-und-sektionen.md#als-fremder-testen)).
- [ ] Familienseiten: die [Checkliste zur Privatsphäre der Familie](checkliste-zur-privatsphaere-der-familie.md) erledigt.

## Suchmaschinen und Feeds

- [ ] `robots.txt` erlaubt, was gefunden werden soll ([robots.txt](../../ersteller/website-ersteller/robots-txt.md)).
- [ ] Sitemap, falls Sie eine möchten ([Sitemaps](../../ersteller/website-ersteller/sitemaps.md)).
- [ ] RSS-Export aktiv und verlinkt, bei einem Blog ([RSS-Feeds für Ihr Blog](../blogs/rss-feeds-fuer-ihr-blog.md)).
- [ ] Keine privaten Inhalte in irgendeinem Feed.

## Sicherungen

- [ ] Automatische Sicherungen laufen beim Hoster.
- [ ] Eine erste Kopie heruntergeladen und getrennt vom Server aufbewahrt.
- [ ] Wiederherstellung einmal getestet oder ein Termin dafür festgelegt.

## Die erste Woche

- [ ] Sehen Sie sich die Website jeden Tag an; lesen Sie Formulareinsendungen und Kommentare.
- [ ] Sehen Sie einmal ins Fehlerprotokoll ([Protokolle für Inhaber](../../kunden/gesund-halten/logs-fuer-betreiber.md)).
- [ ] Erzählen Sie davon: Freunden, Familie, Vereinsmitgliedern.
- [ ] Tragen Sie die [monatliche Pflegecheckliste](monatliche-pflegecheckliste.md) in Ihren Kalender ein.

## Verwandte Seiten

- [Checklisten für Privatpersonen](README.md)
- [Startcheckliste (Website-Ersteller)](../../ersteller/website-ersteller/checkliste-fuer-den-start.md)
- [Eine persönliche Website an einem Wochenende](../persoenliche-seiten/eine-persoenliche-website-an-einem-wochenende.md)
- [Die ersten 30 Tage als Inhaber](../../kunden/ihre-ersten-30-tage.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
