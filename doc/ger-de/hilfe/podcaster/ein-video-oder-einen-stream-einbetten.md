---
title: Ein Video oder einen Stream einbetten
audience: podcaster
category: podcasting-streaming
order: 24
summary: Wie Sie einen Player von YouTube, Vimeo, Dailymotion oder einem anderen Video- oder Streamingdienst auf einer Exponential-Seite zeigen - der Block External video des Layout-Editors mit Laden per Klick, der Block HTML snippet, die Klasse NG Video von Installationen mit Media Site - und was die Content Security Policy der Website braucht.
keywords: Video einbetten, Stream einbetten, Block External video, YouTube, Vimeo, Dailymotion, iframe, Player, Laden per Klick, youtube-nocookie, Layout-Editor, HTML snippet, NG Video, Content Security Policy, frame-src, embed video, embed stream, player, click to load, layouts editor
type: help
---
# Ein Video oder einen Stream einbetten

Diese Seite ist für Produzenten und Redakteure, die auf einer Seite der Website ein Video oder einen Liveplayer zeigen,
der anderswo gehostet wird. Das wichtigste Werkzeug ist der Block **External video** des Layout-Editors, auf
Installationen mit Exponential Layouts (`extension/explayouts`). Seiten ohne Layouts haben andere Möglichkeiten, die am
Ende aufgeführt sind.

## Inhalt

1. [Der Block External video](#der-block-external-video)
2. [Den Block hinzufügen](#den-block-hinzufügen)
3. [Was Besucher sehen](#was-besucher-sehen)
4. [Andere Wege](#andere-wege)
5. [Wenn der Player nicht erscheint](#wenn-der-player-nicht-erscheint)

## Der Block External video

Der Block ist in `extension/explayouts/settings/explayouts.ini.append.php` definiert (`[BlockDefinition_video]`,
*External video*) und wird von `expLayoutsVideoBlockHandler` verarbeitet. Seine Einstellungen:

| Einstellung | Was Sie eintragen |
|---|---|
| **Service** | YouTube, Vimeo, Dailymotion oder *Video address below* (die Adresse darunter) |
| **Video ID** | die ID des Videos bei diesem Dienst |
| **Caption** | eine Bildunterschrift, die unter dem Player erscheint und als sein Titel dient |
| **Video URL or embed src** | für andere Dienste: die Adresse des Players (das `src` seines Einbettungscodes) |
| **Width (px or %)** | Standard `100%` |
| **Height (px)** | Standard 360 |
| **Autoplay** | standardmäßig aus; lassen Sie es aus |

Für die drei Dienste baut der Block die Adresse des Players selbst und verwendet dabei deren datenschutzfreundlichere
Varianten: YouTube von `www.youtube-nocookie.com/embed/`, Vimeo mit `dnt=1`, Dailymotion von
`www.dailymotion.com/embed/video/`. Die ID wird geprüft: nur Ziffern bei Vimeo, Buchstaben, Ziffern, `_` und `-` bei
den anderen.

Für jeden anderen Dienst muss die Adresse `http` oder `https` sein und auf einen anderen Host als Ihre eigene Website
zeigen (`expLayoutsSafeValue::externalEmbedUrl()`); alles andere wird verworfen, und der Block zeigt "No video
configured." (kein Video konfiguriert).

## Den Block hinzufügen

1. Öffnen Sie das Layout der Seite im Layout-Editor ([Der Layout-Editor](../layout-editor.md)).
2. Fügen Sie einer Zone einen Block vom Typ **External video** hinzu ([Einen Block hinzufügen](../layout-editor.md#einen-block-hinzufügen)).
3. Wählen Sie den Dienst und geben Sie die ID ein, oder fügen Sie die Adresse des Players ein.
4. Geben Sie eine Bildunterschrift ein, die sagt, was das Video ist.
5. Veröffentlichen Sie das Layout ([Veröffentlichen, behalten oder verwerfen](../layout-editor.md#veröffentlichen-behalten-oder-verwerfen)).

## Was Besucher sehen

Der Player lädt **erst nach einem Klick**. Bis dahin zeigt die Seite eine Schaltfläche **Load video** und einen
Hinweis, dass das Laden Daten an den anderen Host sendet, und von diesem Host wird nichts abgerufen
(`extension/explayouts/design/standard/templates/explayouts/block/parts/embed.tpl`). Beim Klick setzt ein Skript das
`<iframe>` ein, in einer Sandbox und nur mit dem Ursprung Ihrer Website als Referrer. So bleiben Drittanbieter von der
Seite fern, bis der Besucher sich entscheidet ([Datenschutz und Player von Drittanbietern](datenschutz-und-player-von-drittanbietern.md)).

## Andere Wege

| Weg | Wann | Hinweise |
|---|---|---|
| Block **HTML snippet** | ein Einbettungscode eines Dienstes, den der Videoblock nicht annehmen kann | rohes HTML, so eingefügt, wie es geschrieben ist: nur für vertrauenswürdige Redakteure, und es lädt sofort, nicht nach einem Klick |
| Objekt *NG Video* (Installationen mit Media Site) | eine eigene Videoseite | **Video type** YouTube, Vimeo, Dailymotion oder hochgeladene Datei; YouTube lädt nach einem Klick (`full_ng_video.tpl`) |
| Eine hochgeladene Datei | Sie besitzen die Datei und möchten keinen Drittanbieter | [Video hochladen](video-hochladen.md) |
| Formatierter Text | nicht für Player | formatierter Text kann kein `<iframe>` enthalten; siehe [Medien im formatierten Text](medien-im-formatierten-text.md) |

## Wenn der Player nicht erscheint

| Anzeichen | Wahrscheinliche Ursache | Lösung |
|---|---|---|
| "No video configured." | leere oder abgelehnte Adresse, falsche ID | die ID prüfen; die Adresse des Players verwenden, nicht die der Seite |
| Die Schaltfläche lädt, der Rahmen bleibt leer oder zeigt eine Ablehnung | die Content Security Policy der Website wird durchgesetzt und erlaubt den Host nicht | den Host mit `AdditionalSources[]=frame-src <host>` ergänzen ([Mit Ihrem Administrator arbeiten](mit-ihrem-administrator-arbeiten.md)) |
| Der Dienst sagt, Einbetten sei nicht erlaubt | der Eigentümer des Videos hat das Einbetten beim Dienst abgeschaltet | es beim Dienst ändern |
| Der Rahmen funktioniert, ist aber winzig | Breite oder Höhe | **Width** auf `100%` und eine passende **Height** setzen |

Im Auslieferungszustand sendet `site.ini [ContentSecurityPolicy] Mode=auto` auf öffentlichen Siteaccesses keine
Richtlinie, und `Directives[frame-src]='self'` gilt nur dort, wo die Richtlinie eingeschaltet ist.

## Verwandte Seiten

- [Livestreams und Premieren](livestreams-und-premieren.md)
- [Layouttypen, Zonen und Blöcke](../ersteller/website-ersteller/layouttypen-zonen-und-bloecke.md)
- [Leitfaden für Livestreams](../presse/leitfaden-fuer-livestreams.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
