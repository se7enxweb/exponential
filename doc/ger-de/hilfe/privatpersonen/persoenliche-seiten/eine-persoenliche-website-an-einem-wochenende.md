---
title: Eine persönliche Website an einem Wochenende
audience: individual, website-creator
category: personal-sites
order: 1602
summary: Ein Wochenendplan Schritt für Schritt, mit dem eine Person eine kleine persönliche Website mit Exponential baut - Installation mit SQLite auf dem eigenen Computer, die Seiten anlegen, Bilder und ein Kontaktformular hinzufügen, das Aussehen anpassen und den Umzug zu einem Hoster vorbereiten.
keywords: Wochenende, Schritt für Schritt, persönliche Website, erste Website, Installation, SQLite, exp:install, Seiten, Bilder, Kontaktformular, Theme, veröffentlichen, online gehen, weekend, step by step, first site, go live
type: help
---
# Eine persönliche Website an einem Wochenende

Diese Seite ist für Privatpersonen, die einen Plan ([Eine persönliche Website planen](eine-persoenliche-website-planen.md))
und ein freies Wochenende haben. Sie baut die Website zuerst auf Ihrem eigenen Computer, wo Fehler nichts kosten,
und zieht sie am Ende zu einem Hoster um. Die ausführliche technische Anleitung ist
[Ihre erste Website Schritt für Schritt](../../ersteller/website-ersteller/ihre-erste-website-schritt-fuer-schritt.md); diese Seite
ist dieselbe Arbeit, auf ein Wochenende verteilt.

## Inhalt

1. [Vor dem Samstag](#vor-dem-samstag)
2. [Samstagvormittag: installieren](#samstagvormittag-installieren)
3. [Samstagnachmittag: Seiten](#samstagnachmittag-seiten)
4. [Sonntagvormittag: Bilder, Kontakt und Aussehen](#sonntagvormittag-bilder-kontakt-und-aussehen)
5. [Sonntagnachmittag: prüfen und den Livegang vorbereiten](#sonntagnachmittag-prüfen-und-den-livegang-vorbereiten)
6. [Wenn die Zeit nicht reicht](#wenn-die-zeit-nicht-reicht)

## Vor dem Samstag

- [ ] Ein Computer mit PHP 8.0 oder neuer und Composer, oder Zugang zu einem Hoster mit dem Einrichtungsassistenten
  ([Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
- [ ] Ihre Seitenliste und Texte in einem Dokument, auch als grobe Entwürfe.
- [ ] Fünf bis zehn Bilder, bereits ausgewählt.
- [ ] Eine Adresse, an die Kontaktnachrichten gehen sollen.

## Samstagvormittag: installieren

1. Holen Sie den Code ([Den Code beschaffen](../../../installation/03-den-code-beschaffen.md)).
2. Installieren Sie über die Konsole mit deren Standardwerten: SQLite und das Standard-Seitenpaket. Auf Ihrem eigenen
   Computer ist das der schnellste Weg ([Eine Installationsmethode wählen](../einrichtung/eine-installationsmethode-waehlen.md)):

   ```bash
   php bin/php/console exp:install --dry-run
   php bin/php/console exp:install
   ```

   Für eine echte Website ist später das saubere Paket ohne Demo-Inhalte der bessere Anfang
   ([Ein Seitenpaket und ein Theme wählen](../einrichtung/ein-seitenpaket-und-ein-theme-waehlen.md)).
3. Liefern Sie sie aus, mit dem eingebauten Server von PHP oder mit Velocity ([Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)).
4. Melden Sie sich in der Administration an ([Anmelden in der Administration](../../anmelden-in-der-administration.md)) und
   ändern Sie sofort das Standardpasswort ([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)).
5. Machen Sie den [Rundgang durch die Administration](../../rundgang-durch-die-administration.md).

## Samstagnachmittag: Seiten

1. Löschen oder verbergen Sie die Demo-Inhalte, die Sie nicht möchten (oder beginnen Sie mit dem sauberen Paket).
2. Legen Sie die Ordner Ihrer Seitenliste im Inhaltsbaum an ([Inhalte bearbeiten](../../inhalte-bearbeiten.md)).
3. Schreiben Sie die Über-mich-Seite als *Article*: ein Titel, eine kurze Einleitung, der Text.
4. Legen Sie für jedes Projekt oder jeden Vortrag ein Element an.
5. Veröffentlichen Sie jede Seite und sehen Sie sie sich auf der öffentlichen Website an. Wenn Sie es sich anders
   überlegen: Jedes Veröffentlichen ist eine neue Version, zu der Sie zurückkehren können
   ([Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)).

> **Tipp**
> Schreiben Sie die Einleitung jeder Seite so, als wäre sie der einzige Satz, den jemand liest. Sie erscheint in
> Listen, Suchergebnissen und Feeds.

## Sonntagvormittag: Bilder, Kontakt und Aussehen

1. **Bilder**: Laden Sie sie in die Medienbibliothek oder direkt in die Artikel hoch
   ([Medien und Uploads](../../medien-und-uploads.md)); schneiden Sie sie mit `ezie` im Browser zu oder drehen Sie sie
   ([Bilder mit ezie bearbeiten](../portfolios/bilder-mit-ezie-bearbeiten.md)). Geben Sie jedem Bild einen
   Alternativtext.
2. **Kontakt**: Legen Sie ein Objekt *Feedback form* an und setzen Sie seinen Empfänger
   ([Über-mich-Seite und Kontaktformular](ueber-mich-seite-und-kontaktformular.md)).
3. **Aussehen**: Ändern Sie den Namen der Website, das Logo und die Farben, die das Theme ändern lässt
   ([Ihrer Website ein Markenbild geben](../../ersteller/website-ersteller/ihre-website-markengerecht-gestalten.md)). Tiefere Änderungen
   sind Template-Overrides, die warten können.

## Sonntagnachmittag: prüfen und den Livegang vorbereiten

| Prüfung | Wie |
|---|---|
| Jede Seite öffnet sich, jeder Link funktioniert | Durch das Menü klicken; [Link- und Inhaltsprüfungen](../../ersteller/website-ersteller/link-und-inhaltspruefungen.md) |
| Sie liest sich gut auf einem Smartphone | Das Browserfenster schmal ziehen |
| Das Kontaktformular sendet | Sich selbst eine Nachricht schicken |
| Titel und Beschreibungen | [SEO-Grundlagen](../../ersteller/website-ersteller/seo-grundlagen.md) |
| Bilder haben Alternativtext | Das Bearbeitungsformular jedes Bildes öffnen |

Entscheiden Sie dann, wo sie liegen wird ([Mit kleinem Budget betreiben](../einrichtung/mit-kleinem-budget-betreiben.md)), und
gehen Sie die [Startcheckliste](../checklisten/startcheckliste-fuer-privatpersonen.md) durch. Der Umzug von Ihrem
Computer zu einem Hoster bedeutet, die Datenbankdatei und `var/storage` zusammen mit dem Code zu kopieren und dann
die echte Adresse einzustellen
([Livegang: Hosts, HTTPS und Siteaccesses](../../ersteller/website-ersteller/livegang-hosts-und-https.md)).

## Wenn die Zeit nicht reicht

Veröffentlichen Sie weniger, nicht schlechter. Startseite, Über mich und Kontakt sind eine vollständige persönliche
Website. Arbeiten, Texte und der Rest können Seite für Seite folgen.

## Verwandte Seiten

- [Persönliche Websites](README.md)
- [Ihre erste Website Schritt für Schritt](../../ersteller/website-ersteller/ihre-erste-website-schritt-fuer-schritt.md)
- [Startcheckliste für Privatpersonen](../checklisten/startcheckliste-fuer-privatpersonen.md)
- [Erste Schritte](../../erste-schritte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
