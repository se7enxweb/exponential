---
title: Was es gibt und was nicht
audience: individual, website-creator, customer
category: personal-sites
order: 1506
summary: Eine einfache Funktionsübersicht für Privatpersonen - was Exponential für persönliche Websites, Portfolios, Blogs, Familienseiten und Vereinsseiten mitliefert, was eine optionale Erweiterung braucht, was einen externen Dienst braucht und was es überhaupt nicht gibt, jeweils mit seiner Quelle.
keywords: Funktionen, Grenzen, was es gibt, nicht eingebaut, Kommentare, ezcomments, Importer, WordPress, Geburtstagserinnerungen, Ticketverkauf, Mitgliedsbeiträge, Domain, E-Mail, autorss, syndication, ezpm, externer Dienst, Erweiterung, features, limits, what exists, outside service, extension
type: help
---
# Was es gibt und was nicht

Diese Seite ist für Privatpersonen, die vor dem Start wissen möchten, auf welche Funktionen sie sich verlassen
können. Jede Zeile wurde am Repository geprüft: an der `composer.json` im Wurzelverzeichnis (was standardmäßig
installiert wird), am Ordner `extension/` und an den Inhaltsklassen der mitgelieferten Seitenpakete. „Mitgeliefert"
heißt, dass es Teil der Standardinstallation ist.

## Inhalt

1. [Mit Exponential mitgeliefert](#mit-exponential-mitgeliefert)
2. [Mitgeliefert, aber einzurichten](#mitgeliefert-aber-einzurichten)
3. [Optionale Erweiterungen, die Sie selbst hinzufügen](#optionale-erweiterungen-die-sie-selbst-hinzufügen)
4. [Braucht einen externen Dienst](#braucht-einen-externen-dienst)
5. [Gibt es nicht](#gibt-es-nicht)
6. [Wenn Ihnen etwas fehlt](#wenn-ihnen-etwas-fehlt)

## Mit Exponential mitgeliefert

| Funktion | Quelle | Seite |
|---|---|---|
| Die Klassen Blog, Blog post, Comment, Gallery, Image, Event, Event calendar, Poll, Feedback form, Forum | das Paket `sevenx_classes` des Standard-Seitenpakets | [Inhaltsklassen für Websites](../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md) |
| Entwürfe, Versionen, zeitgesteuerte Veröffentlichung | Kernel; `README.md` „Main Exponential features" | [Veröffentlichen und Versionen](../veroeffentlichen-und-versionen.md) |
| RSS 1.0, RSS 2.0, Atom, OPML und Podcast-Feeds | Kernel-Modul `rss` | [RSS-Feeds für Ihr Blog](blogs/rss-feeds-fuer-ihr-blog.md) |
| Bildvarianten (Vorschaubilder und Größen) | `settings/image.ini` | [Bildvarianten für Portfolios](portfolios/bildvarianten-fuer-portfolios.md) |
| Bildeditor im Bearbeitungsformular | `se7enxweb/ezie` in `composer.json` | [Bilder mit ezie bearbeiten](portfolios/bilder-mit-ezie-bearbeiten.md) |
| Hochladen mehrerer Dateien | `se7enxweb/ezmultiupload` | [Viele Bilder hochladen](portfolios/viele-bilder-hochladen.md) |
| Tags | `se7enxweb/eztags` | [Tags und Kategorien](blogs/tags-und-kategorien.md) |
| Feld für Sternebewertungen | `se7enxweb/ezstarrating`; die Klassen *Gallery* und *Image* haben ein Feld `rating` | [ezstarrating](../../funktionen/6.0/erweiterungen/ezstarrating.md) |
| Sektionen, Rollen, private Bereiche | Kernel | [Private Bereiche](familienseiten/private-bereiche-mit-rollen-und-sektionen.md) |
| Selbstregistrierung mit Aktivierung per E-Mail | Kernel `user/register` | [Mitglieder und Registrierung](vereinsseiten/mitglieder-und-registrierung.md) |
| Formulare und Umfragen (Informationssammler) | Kernel | [Formulare, Umfragen und Anmeldungen](vereinsseiten/formulare-umfragen-und-anmeldungen.md) |
| Newsletter | `se7enxweb/cjw_newsletter` | [Vereinsnewsletter](vereinsseiten/vereinsnewsletter.md) |
| Feld für das Geburtsdatum | `se7enxweb/birthday` (Datentyp `ezbirthday`) | [Geburtstage und Familientermine](familienseiten/geburtstage-und-familientermine.md) |
| Karten für einen Ort | `se7enxweb/ezgmaplocation` | [ezgmaplocation](../../funktionen/6.0/erweiterungen/ezgmaplocation.md) |
| Zweistufige Anmeldung | `se7enxweb/sevenx_authentication_2fa` | [Zwei-Faktor-Anmeldung](im-betrieb/zwei-faktor-anmeldung.md) |
| CAPTCHA-Felder | `se7enxweb/recaptcha`, `se7enxweb/hcaptcha` (jedes braucht Schlüssel seines Dienstes) | [Grundlagen zu Spam und Sicherheit](im-betrieb/grundlagen-zu-spam-und-sicherheit.md) |
| SQLite-3-Datenbank | Kernel, der Standard | [Mit kleinem Budget betreiben](einrichtung/mit-kleinem-budget-betreiben.md) |
| Webserver Velocity | `se7enxweb/exponential-velocity` | [Shared Hosting oder ein kleiner Server](einrichtung/shared-hosting-oder-ein-kleiner-server.md) |
| Sicherungen aus der Administration | `se7enxweb/git_manager` (**Setup > Backup**) | [Sicherungen für eine Person](im-betrieb/sicherungen-fuer-eine-person.md) |
| Schreibassistent | `se7enxweb/expai` (nur Entwürfe) | [Der Schreibassistent](im-betrieb/schreibassistent-expai.md) |

## Mitgeliefert, aber einzurichten

| Funktion | Was Sie regeln müssen |
|---|---|
| Kommentare zu Blogbeiträgen | Pro Beitrag „Kommentare aktivieren" ankreuzen und der passenden Rolle die Berechtigung geben, *Comment*-Objekte zu erstellen |
| Newsletter, zeitgesteuertes Veröffentlichen, Feed-Import | Ein Cronjob, der `runcronjobs.php` ausführt ([Cronjobs](../../anleitungen/cronjobs.md)) |
| Registrierung, Zurücksetzen des Passworts, Formular-E-Mails, Newsletter | Ausgehende E-Mail, die in den Postfächern ankommt (Sache des Hostings) |
| Zweistufige Anmeldung | Die Erweiterung und ihren Login-Handler in den Einstellungen aktivieren |
| Schreibassistent | Ein Anbieterschlüssel oder ein lokales Modell, eingetragen in die Einstellungen von der Person, die den Server betreibt |
| CAPTCHA | Ein Site-Key und ein Secret von reCAPTCHA oder hCaptcha |
| HTTPS | Ein Zertifikat: von Ihrem Hoster, oder Velocity bezieht eines von Let's Encrypt |

## Optionale Erweiterungen, die Sie selbst hinzufügen

Diese sind auf der Demonstrationsinstallation vorhanden, stehen aber **nicht** in der `require`-Liste der
`composer.json` im Wurzelverzeichnis; eine neue Installation hat sie also erst, wenn Sie sie hinzufügen:

| Erweiterung | Was sie tut | Hinweis |
|---|---|---|
| `autorss` | Ein Workflow-Ereignis, das beim Veröffentlichen automatisch einen RSS-Export anlegt | Composer-Paket `se7enxweb/autorss`; die eingebauten RSS-Exporte funktionieren auch ohne |
| `ezpm` | Private Nachrichten zwischen Mitgliedern | in `composer.json` vorgeschlagen, nicht erforderlich |
| `ezfind` | Suche auf Basis von Solr | vorgeschlagen; braucht einen Solr-Server; für eine kleine Website genügt die eingebaute Suche |

`syndication`, das installiert ist, ist **kein** RSS-Werkzeug: Es synchronisiert Inhalte zwischen getrennten
Exponential-Installationen über SOAP ([syndication](../../funktionen/6.0/erweiterungen/syndication.md)).

## Braucht einen externen Dienst

| Bedarf | Warum extern |
|---|---|
| Ein Domainname und sein DNS | Bei einem Registrar gemietet ([Domainnamen und HTTPS](einrichtung/domainnamen-und-https.md)) |
| E-Mail-Zustellung | Der Mailserver Ihres Hosters oder ein E-Mail-Anbieter verschickt die E-Mails, die Exponential schreibt |
| KI-Texterzeugung | Ein Anbieter (oder ein lokales Modell, das Sie betreiben) schreibt; `expai` spricht nur mit ihm |
| CAPTCHA-Prüfungen | reCAPTCHA oder hCaptcha |
| Speicher für Sicherungen außer Haus | Überall, nur nicht auf demselben Server |
| Zahlungen | Ein Zahlungsanbieter, wenn Sie den Shop nutzen ([Zahlungsanbieter](../ersteller/website-ersteller/payment-gateways.md)) |

## Gibt es nicht

Klar gesagt, damit Sie entsprechend planen können:

- **Keine Erweiterung `ezcomments`.** Kommentare sind die mitgelieferte Klasse *Comment*; es gibt kein
  Kommentarsystem mit Threads, keine Antwort per E-Mail und keine „Likes".
- **Kein Ein-Klick-Importer** aus WordPress oder einer anderen Blogplattform. Es gibt einen dokumentierten manuellen
  Weg ([Umzug von einer anderen Blogplattform](blogs/umzug-von-einer-anderen-blogplattform.md)).
- **Keine Geburtstagserinnerungen oder Glückwunsch-E-Mails.** Die Erweiterung `birthday` speichert nur das Datum.
- **Kein Ticketverkauf, keine Veranstaltungsanmeldung mit Bezahlung und keine Mitgliedsbeiträge.** Anmeldeformulare
  gibt es; eine Bezahlung dafür nicht.
- **Keine Domainregistrierung und keine DNS-Verwaltung.**
- **Kein eingebauter Speicher für Sicherungen außer Haus.** Sicherungen werden auf dem Server geschrieben; sie
  wegzukopieren ist Ihre Aufgabe.
- **Kein vom Projekt betriebener gehosteter Dienst für Privatpersonen.** Sie betreiben es, Ihr Hoster betreibt es,
  oder ein Partner tut es.

## Wenn Ihnen etwas fehlt

1. Prüfen Sie, ob es eine Erweiterung gibt: [Erweiterungen](../../anleitungen/extensions.md) und die Funktionsseiten unter
   [Erweiterungen in 6.0](../../funktionen/6.0/erweiterungen/README.md).
2. Fragen Sie in der Community, ob jemand es gebaut hat ([Community-Kanäle](../fuersprecher/community-kanaele.md)).
3. Bezahlen Sie einen Partner für die Entwicklung und erwägen Sie, das Ergebnis zurückzugeben
   ([Wann Sie sich Hilfe holen sollten](im-betrieb/wann-sie-sich-hilfe-holen-sollten.md),
   [Funktionswünsche und gesponserte Funktionen](../partner/funktionswuensche-und-gesponserte-funktionen.md)).

## Verwandte Seiten

- [Privatpersonen: hier beginnen](privatpersonen-hier-beginnen.md)
- [Ist Exponential das Richtige für Sie?](ist-exponential-das-richtige-fuer-sie.md)
- [Standardverteilung der Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md)
- [Was Sie bauen können](../ersteller/website-ersteller/was-sie-bauen-koennen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
