---
title: "Privatpersonen: hier beginnen"
audience: individual, website-creator, customer
category: personal-sites
order: 1
summary: Die Einstiegsseite für Menschen, die eine eigene Website mit Exponential betreiben - eine persönliche Seite, ein Portfolio, ein Blog, eine Familienseite oder eine Vereinsseite - mit kleinem Budget und meist allein: was Exponential Ihnen bietet, was nicht, und wo jede Seite dieses Bereichs zu finden ist.
keywords: Privatperson, persönliche Website, Portfolio, Blog, Familienseite, Vereinsseite, Hobby, kleines Budget, SQLite, Velocity, Shared Hosting, Galerie, Kommentare, RSS, Privatsphäre, Zwei-Faktor, Sicherungen, Updates, hier beginnen, individual, personal site, club site, start here
featured: true
type: help
---
# Privatpersonen: hier beginnen

Dieser Bereich ist für Menschen, die eine Website allein aufbauen und betreiben: eine persönliche Homepage, ein
Portfolio mit Fotografien oder Zeichnungen, ein Blog, eine private Seite für die Familie oder die Website eines
Vereins oder einer kleinen Hobbygruppe. Wahrscheinlich erledigen Sie den größten Teil der Arbeit selbst, abends und
mit kleinem Budget, und Sie möchten eine Website, die ohne ständige Aufmerksamkeit funktioniert.

Jede Seite ist für eine Person geschrieben, die die Arbeit allein macht. Wo eine Aufgabe einen Entwickler, einen
Hosting-Anbieter oder einen Partner braucht, sagt die Seite das deutlich, und wo eine Funktion nicht existiert, sagt
sie auch das.

> **Hinweis**
> Jede in diesem Bereich genannte Funktion wurde am Exponential-Repository geprüft: am Code, an den Einstellungen, an
> der `composer.json` im Wurzelverzeichnis und an der vorhandenen Dokumentation, auf die jede Seite verweist. Der
> Bereich nennt keine Kunden, zitiert keine Statistiken und nennt keine eigenen Preise.

## Inhalt

1. [Exponential für eine Person](#exponential-für-eine-person)
2. [Finden Sie Ihre Art von Website](#finden-sie-ihre-art-von-website)
3. [Die Einrichtung](#die-einrichtung)
4. [Allein betreiben](#allein-betreiben)
5. [Was Exponential Ihnen nicht abnimmt](#was-exponential-ihnen-nicht-abnimmt)
6. [Ein erstes Wochenende](#ein-erstes-wochenende)
7. [Weitere Bereiche dieser Hilfe](#weitere-bereiche-dieser-hilfe)

## Exponential für eine Person

Exponential ist ein Content-Management-System: Sie beschreiben Ihre Inhalte einmal (ein Blogbeitrag hat einen Titel,
einen Text, ein Datum und Tags; eine Fotografie hat eine Bildunterschrift und ein Bild), und das System speichert,
listet, durchsucht und veröffentlicht sie. Das meiste, was eine einzelne Person braucht, wird mitgeliefert:

| Sie möchten | Was Exponential bietet | Wo es erklärt wird |
|---|---|---|
| Eine Website, die Sie in jedem Browser aktualisieren können | Die Administrationsoberfläche und den Editor | [Erste Schritte](../erste-schritte.md), [Inhalte bearbeiten](../inhalte-bearbeiten.md) |
| Ein Blog | Die Klassen *Blog* und *Blog post*, wahlweise mit Kommentaren | [Blogs](blogs/README.md) |
| Ein Foto- oder Kunstportfolio | Die Klassen *Gallery* und *Image*, Bildvarianten, einen Bildeditor (`ezie`) | [Portfolios](portfolios/README.md) |
| Feeds, denen Leser folgen können | RSS- und Atom-Exporte, eingebaut | [RSS-Feeds für Ihr Blog](blogs/rss-feeds-fuer-ihr-blog.md) |
| Einen privaten Bereich für Familie oder Mitglieder | Sektionen und Rollen | [Private Bereiche](familienseiten/private-bereiche-mit-rollen-und-sektionen.md) |
| Mitglieder, die sich selbst registrieren | Benutzerregistrierung mit Aktivierung per E-Mail | [Mitglieder und Registrierung](vereinsseiten/mitglieder-und-registrierung.md) |
| Einen Newsletter für einen Verein | Die Erweiterung `cjw_newsletter` | [Vereinsnewsletter](vereinsseiten/vereinsnewsletter.md) |
| Keinen Datenbankserver, um den Sie sich kümmern müssen | SQLite 3, die Standarddatenbank | [Mit kleinem Budget betreiben](einrichtung/mit-kleinem-budget-betreiben.md) |
| Eine sicherere Anmeldung | Zweistufige Anmeldung (`sevenx_authentication_2fa`) | [Zwei-Faktor-Anmeldung](im-betrieb/zwei-faktor-anmeldung.md) |
| Hilfe bei einem ersten Entwurf | Exponential Intelligence (`expai`), nur Entwürfe, mit einem Anbieterschlüssel, den Sie mitbringen | [Der Schreibassistent](im-betrieb/schreibassistent-expai.md) |

Die Software ist freie Software unter der GNU General Public License, Version 2 oder später: Es gibt keine
Lizenzgebühr ([Lizenzierung für Käufer](../geschaeft/lizenzen-fuer-kaeufer.md)).

## Finden Sie Ihre Art von Website

| Sie möchten betreiben | Beginnen Sie mit |
|---|---|
| Eine persönliche Homepage: wer Sie sind, was Sie tun, wie man Sie erreicht | [Persönliche Websites](persoenliche-seiten/README.md) |
| Ein Portfolio mit Fotografien, Zeichnungen oder anderen Arbeiten | [Portfolios](portfolios/README.md) |
| Ein Blog, allein oder mit einem Freund | [Blogs](blogs/README.md) |
| Eine Website, die nur Ihre Familie sehen kann | [Familienseiten](familienseiten/README.md) |
| Die Website eines Vereins, eines Teams oder einer Hobbygruppe | [Vereinsseiten](vereinsseiten/README.md) |
| Etwas anderes, oder Sie sind nicht sicher, ob Exponential passt | [Ist Exponential das Richtige für Sie?](ist-exponential-das-richtige-fuer-sie.md) |

Ein eingetragener gemeinnütziger Verein oder eine Organisation mit Angestellten findet seine Bedürfnisse eher unter
[Gemeinnützige Organisationen](../../../eng-us/help/non-profits/non-profits-start-here.md), und ein Laden oder ein Handwerksbetrieb
gehört zu [Lokale Unternehmen](../lokale-unternehmen/lokale-unternehmen-hier-beginnen.md). Wenn Sie mit technischen Begriffen
gar nicht vertraut sind, lesen Sie [Nicht-technische Menschen](../../../eng-us/help/non-technical-people/non-technical-people-start-here.md)
zusätzlich zu diesem Bereich.

## Die Einrichtung

Die Seiten zur [Einrichtung](einrichtung/README.md) gehen die Entscheidungen der Reihe nach durch:

1. [Eine Installationsmethode wählen](einrichtung/eine-installationsmethode-waehlen.md): der Browser-Assistent, die Installation
   auf der Konsole oder der Kickstarter.
2. [Ein Seitenpaket und ein Theme wählen](einrichtung/ein-seitenpaket-und-ein-theme-waehlen.md): was die mitgelieferten Pakete
   und Themes Ihnen bieten.
3. [Mit kleinem Budget betreiben](einrichtung/mit-kleinem-budget-betreiben.md): SQLite, ein kleiner Server, wenige bewegliche
   Teile.
4. [Shared Hosting oder ein kleiner Server](einrichtung/shared-hosting-oder-ein-kleiner-server.md): was wo funktioniert und warum
   Velocity einen eigenen Server braucht.
5. [Domainnamen und HTTPS](einrichtung/domainnamen-und-https.md): was Sie außerhalb von Exponential regeln.

## Allein betreiben

Die Seiten zum [Betrieb](im-betrieb/README.md) behandeln die kleinen Routinen, die eine Ein-Personen-Website gesund
halten: [Sicherungen](im-betrieb/sicherungen-fuer-eine-person.md), [Updates](im-betrieb/updates-fuer-eine-person.md),
[Zwei-Faktor-Anmeldung](im-betrieb/zwei-faktor-anmeldung.md), [Grundlagen zu Spam und Sicherheit](im-betrieb/grundlagen-zu-spam-und-sicherheit.md),
den [Schreibassistenten](im-betrieb/schreibassistent-expai.md), [wann Sie sich Hilfe holen sollten](im-betrieb/wann-sie-sich-hilfe-holen-sollten.md)
und [Lernen mit der Partnerstufe Student](im-betrieb/lernen-mit-der-stufe-student.md).

Die [Checklisten](checklisten/README.md) machen daraus Listen zum Ausdrucken: eine Checkliste für den Start, eine
monatliche Pflegeliste, eine Checkliste zur Privatsphäre der Familie und ein Arbeitsblatt für die Websiteplanung.

## Was Exponential Ihnen nicht abnimmt

Manche Dinge liegen außerhalb der Software. Wer sie früh kennt, spart sich ein Wochenende:

- **Domainnamen und DNS** kaufen Sie bei einem Registrar und richten sie auf Ihren Server; Exponential kann keine
  Domain registrieren ([Domainnamen und HTTPS](einrichtung/domainnamen-und-https.md)).
- **Es gibt keinen Ein-Klick-Import aus einer anderen Blogplattform**; der Umzug von Inhalten ist ein kleines Projekt
  ([Umzug von einer anderen Blogplattform](blogs/umzug-von-einer-anderen-blogplattform.md)).
- **Kommentare stammen aus der mitgelieferten Klasse *Comment***, nicht aus einem separaten Kommentarsystem; in dieser
  Distribution gibt es keine Erweiterung `ezcomments` ([Kommentare in Ihrem Blog](blogs/kommentare-in-ihrem-blog.md)).
- **Die Erweiterung `birthday` speichert Geburtsdaten; sie verschickt keine Erinnerungen**
  ([Geburtstage und Familientermine](familienseiten/geburtstage-und-familientermine.md)).
- **E-Mails müssen Ihren Server verlassen können**, für die Registrierung, das Zurücksetzen von Passwörtern und
  Newsletter; das ist Sache des Hostings.

Die vollständige Liste steht unter [Was es gibt und was nicht](was-es-gibt-und-was-nicht.md).

## Ein erstes Wochenende

| Wann | Was tun | Lesen |
|---|---|---|
| Freitagabend | Notieren, wozu die Website dient, wer sie liest und was privat bleiben muss | [Arbeitsblatt für die Websiteplanung](checklisten/arbeitsblatt-fuer-die-websiteplanung.md) |
| Samstagvormittag | Exponential mit SQLite auf dem eigenen Computer installieren und sich umsehen | [Eine Installationsmethode wählen](einrichtung/eine-installationsmethode-waehlen.md), [Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| Samstagnachmittag | Die ersten Seiten und Bilder anlegen; den Editor ausprobieren | [Inhalte bearbeiten](../inhalte-bearbeiten.md), [Eine persönliche Website an einem Wochenende](persoenliche-seiten/eine-persoenliche-website-an-einem-wochenende.md) |
| Sonntagvormittag | Entscheiden, wo die Website liegen wird und was sie kostet | [Mit kleinem Budget betreiben](einrichtung/mit-kleinem-budget-betreiben.md) |
| Sonntagnachmittag | Die zweistufige Anmeldung einschalten und eine erste Sicherung anlegen | [Zwei-Faktor-Anmeldung](im-betrieb/zwei-faktor-anmeldung.md), [Sicherungen für eine Person](im-betrieb/sicherungen-fuer-eine-person.md) |

Die vollständige Lesereihenfolge ist der [Lesepfad](lesepfad-fuer-privatpersonen.md). Kurze Antworten stehen in den
[FAQ](faq-fuer-privatpersonen.md), und die Begriffe werden im [Glossar](glossar-fuer-privatpersonen.md) erklärt.

![Das Dashboard der Administration von Exponential](../../../eng-us/help/images/admin-dashboard.webp)

## Weitere Bereiche dieser Hilfe

| Bereich | Für |
|---|---|
| [Website-Ersteller](../ersteller/website-ersteller/website-ersteller-hier-beginnen.md) | Die ausführlichen Seiten zum Bauen, auf die dieser Bereich verweist |
| [Kunden](../kunden/kunden-hier-beginnen.md) | Inhaber einer Website, die jemand anderes gebaut hat und betreibt |
| [Blogs (Kunden)](../kunden/typen/blogs-hier-beginnen.md) | Die Sicht des Inhabers auf ein Blog |
| [Gemeinnützige Organisationen](../../../eng-us/help/non-profits/non-profits-start-here.md) | Wohltätige Organisationen und Vereine |
| [Lokale Unternehmen](../lokale-unternehmen/lokale-unternehmen-hier-beginnen.md) | Läden, Handwerk und Dienstleistungen |
| [Nicht-technische Menschen](../../../eng-us/help/non-technical-people/non-technical-people-start-here.md) | Hilfe in einfacher Sprache für alle |

## Verwandte Seiten

- [Lesepfad für Privatpersonen](lesepfad-fuer-privatpersonen.md)
- [Ist Exponential das Richtige für Sie?](ist-exponential-das-richtige-fuer-sie.md)
- [Was es gibt und was nicht](was-es-gibt-und-was-nicht.md)
- [Website-Ersteller: hier beginnen](../ersteller/website-ersteller/website-ersteller-hier-beginnen.md)
- [Kunden: hier beginnen](../kunden/kunden-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
