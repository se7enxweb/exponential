---
title: FAQ für Privatpersonen
audience: individual, website-creator, customer
category: personal-sites
order: 1502
summary: Kurze, ehrliche Antworten auf die Fragen, die Menschen vor und während des Betriebs einer persönlichen Website, eines Portfolios, eines Blogs, einer Familienseite oder einer Vereinsseite mit Exponential in Eigenregie stellen - Kosten, Hosting, Kommentare, Import, Privatsphäre, E-Mail, Sicherungen, Updates und wann man sich Hilfe holt.
keywords: FAQ, Fragen, Privatperson, persönliche Website, Blog, Portfolio, Familienseite, Verein, Kosten, kostenlos, Hosting, SQLite, Kommentare, WordPress, Import, Privatsphäre, E-Mail, Sicherungen, Updates, Zwei-Faktor, KI, questions, individual, backups, AI
type: help
---
# FAQ für Privatpersonen

Diese Seite ist für Privatpersonen, die eine schnelle Antwort möchten, bevor sie eine ganze Seite lesen. Jede
Antwort verweist auf die Seite mit den Einzelheiten.

## Inhalt

1. [Kosten und Hosting](#kosten-und-hosting)
2. [Die Website bauen](#die-website-bauen)
3. [Blogs und Portfolios](#blogs-und-portfolios)
4. [Familien- und Vereinsseiten](#familien--und-vereinsseiten)
5. [Der Betrieb](#der-betrieb)

## Kosten und Hosting

**Kostet Exponential etwas?**
Keine Lizenzgebühr. Es ist freie Software unter der GNU General Public License, Version 2 oder später
([Lizenzierung für Käufer](../geschaeft/lizenzen-fuer-kaeufer.md)). Sie bezahlen für das Hosting, einen Domainnamen
und, wenn Sie sie möchten, externe Dienste wie einen E-Mail-Anbieter oder einen KI-Anbieter.

**Was ist die kleinste Einrichtung, die funktioniert?**
PHP 8.0 oder neuer, SQLite 3 (kein Datenbankserver) und entweder ein gängiger Webserver oder mit Velocity gar kein
Webserver ([Anforderungen auf einen Blick](../hosting/grundlagen/anforderungen-auf-einen-blick.md),
[Mit kleinem Budget betreiben](setup/mit-kleinem-budget-betreiben.md)).

**Kann ich günstiges Shared Hosting nutzen?**
Oft ja, mit Apache oder LiteSpeed und `.htaccess`, installiert über den Einrichtungsassistenten. Velocity passt
meist nicht zu Shared Hosting, weil es einen dauerhaft laufenden Prozess braucht
([Shared Hosting oder ein kleiner Server](setup/shared-hosting-oder-ein-kleiner-server.md)).

**Brauche ich einen Datenbankserver?**
Nein. SQLite 3 ist der Standard und speichert alles in einer Datei. PostgreSQL, MongoDB, MariaDB, Oracle und MySQL
werden ebenfalls unterstützt, wenn Ihr Hoster eine davon anbietet ([Datenbankunterstützung](../datenbankunterstuetzung.md)).

**Kann ich es ausprobieren, bevor ich für irgendetwas bezahle?**
Ja. Installieren Sie es mit `exp:install` und dessen Standardwerten (SQLite, das Standard-Seitenpaket) auf Ihrem
eigenen Computer ([Eine Installationsmethode wählen](setup/eine-installationsmethode-waehlen.md)).

## Die Website bauen

**Muss ich programmieren können?**
Zum Schreiben und Veröffentlichen nicht. Um das Aussehen der Website über die Optionen des Themes hinaus zu ändern,
bearbeiten Sie Templates und Stylesheets, was eher HTML als Programmieren ist
([Ein Seitenpaket und ein Theme wählen](setup/ein-seitenpaket-und-ein-theme-waehlen.md)).

**Mit welchem Theme sollte ich beginnen?**
Die Standardinstallation verwendet `sevenx_themes_simple`. Das ältere Design `ezwebin` bleibt für Websites erhalten,
die darauf aufgebaut sind ([Seitenpakete und die mitgelieferten Themes](../ersteller/website-ersteller/seitenpakete-und-mitgelieferte-themes.md)).

**Kann ich Seiten visuell gestalten?**
Ja, mit Exponential Layouts, dem Seitenbaukasten ([Layout-Editor](../layout-editor.md)). Viele persönliche Websites
brauchen ihn nie; Ordner und Artikel genügen.

**Kann ich meinen Domainnamen über Exponential registrieren?**
Nein. Domains werden bei einem Registrar gekauft und auf Ihren Server gerichtet. Exponential muss nur seine Adresse
kennen ([Domainnamen und HTTPS](setup/domainnamen-und-https.md)).

## Blogs und Portfolios

**Hat ein Blog Kommentare?**
Die mitgelieferte Klasse *Blog post* hat ein Kontrollkästchen „Kommentare aktivieren", und Kommentare sind
*Comment*-Objekte, die unter dem Beitrag gespeichert werden. Wer sie schreiben darf, ist eine Berechtigung, die Sie
festlegen. In dieser Distribution gibt es keine separate Erweiterung `ezcomments`
([Kommentare in Ihrem Blog](blogs/kommentare-in-ihrem-blog.md)).

**Kann ich mein WordPress-Blog importieren?**
Nicht mit einem Klick. Es gibt keinen fertigen Importer; der dokumentierte Weg besteht aus einem Export, einem
kleinen Umwandlungsskript und einem Importwerkzeug ([Umzug von einer anderen Blogplattform](blogs/umzug-von-einer-anderen-blogplattform.md)).

**Erzeugt es RSS-Feeds?**
Ja, RSS 2.0, Atom und Podcast-Feeds sind unter **Setup > RSS** eingebaut
([RSS-Feeds für Ihr Blog](blogs/rss-feeds-fuer-ihr-blog.md)).

**Kann ich Fotos im Browser zuschneiden und drehen?**
Ja, mit dem Bildeditor `ezie` im Bearbeitungsformular ([Bilder mit ezie bearbeiten](portfolios/bilder-mit-ezie-bearbeiten.md)).

**Kann ich viele Fotos auf einmal hochladen?**
Ja, mit der Mehrfach-Upload-Seite von `ezmultiupload` ([Viele Bilder hochladen](portfolios/viele-bilder-hochladen.md)).

## Familien- und Vereinsseiten

**Kann ich eine Website erstellen, die nur meine Familie sehen kann?**
Ja. Entweder einen privaten Bereich (eine Sektion, die anonyme Besucher nicht lesen dürfen) oder die ganze Website
hinter einer Anmeldung ([Private Bereiche mit Rollen und Sektionen](familienseiten/private-bereiche-mit-rollen-und-sektionen.md)).

**Verschickt die Erweiterung birthday Geburtstagserinnerungen?**
Nein. Sie fügt ein Feld hinzu, das ein Geburtsdatum speichert. Erinnerungen bräuchten eigene Entwicklungsarbeit
([Geburtstage und Familientermine](familienseiten/geburtstage-und-familientermine.md)).

**Können sich Vereinsmitglieder selbst registrieren?**
Ja, mit der Registrierungsseite und Aktivierung per E-Mail ([Mitglieder und Registrierung](vereinsseiten/mitglieder-und-registrierung.md)).

**Kann ich einen Vereinsnewsletter verschicken?**
Ja, mit `cjw_newsletter`. Er braucht einen Cronjob und funktionierenden E-Mail-Versand ([Vereinsnewsletter](vereinsseiten/vereinsnewsletter.md)).

**Können Mitglieder Beiträge bezahlen oder Eintrittskarten kaufen?**
Es gibt kein Modul für Ticketverkauf oder Mitgliedsbeiträge. Der eingebaute Shop kann Artikel verkaufen; alles
Weitere braucht eigene Entwicklung oder einen externen Dienst ([Was es gibt und was nicht](was-es-gibt-und-was-nicht.md)).

## Der Betrieb

**Wie sichere ich?**
Die Datenbank und die hochgeladenen Dateien, zusammen, an einem anderen Ort aufbewahrt ([Sicherungen für eine Person](im-betrieb/sicherungen-fuer-eine-person.md)).

**Wie oft muss ich aktualisieren?**
Achten Sie auf neue Versionen und spielen Sie Sicherheitskorrekturen zügig ein; ein monatlicher Blick ist eine gute
Gewohnheit ([Updates für eine Person](im-betrieb/updates-fuer-eine-person.md)).

**Gibt es eine zweistufige Anmeldung?**
Ja, mit Authenticator-Apps oder einem Code per E-Mail ([Zwei-Faktor-Anmeldung](im-betrieb/zwei-faktor-anmeldung.md)).

**Schreibt der KI-Assistent mein Blog für mich?**
Er schreibt Entwürfe, veröffentlicht nie und braucht einen Anbieterschlüssel oder ein lokales Modell, das Sie
einrichten ([Der Schreibassistent](im-betrieb/schreibassistent-expai.md)).

**Wann sollte ich jemanden bezahlen?**
Wenn eine Aufgabe den Server, den E-Mail-Versand, eine Migration oder eigenen Code betrifft, den Sie nicht testen
können ([Wann Sie sich Hilfe holen sollten](im-betrieb/wann-sie-sich-hilfe-holen-sollten.md)).

## Verwandte Seiten

- [Privatpersonen: hier beginnen](privatpersonen-hier-beginnen.md)
- [Glossar für Privatpersonen](glossar-fuer-privatpersonen.md)
- [Was es gibt und was nicht](was-es-gibt-und-was-nicht.md)
- [FAQ für Website-Ersteller](../ersteller/website-ersteller/faq-fuer-website-ersteller.md)
- [FAQ für Kunden](../kunden/faq-fuer-kunden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
