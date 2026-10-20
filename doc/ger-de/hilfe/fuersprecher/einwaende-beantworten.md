---
title: Einwände als Fürsprecher beantworten
audience: advocate
category: advocacy
order: 30
summary: Eine Methode, Zweifel an Exponential als Fürsprecher der Community zu beantworten, in öffentlichen Diskussionen und privaten Gesprächen - zuhören, das eigentliche Anliegen finden, mit einer Quelle antworten, eine Prüfung anbieten, Grenzen zugeben - mit Antwortvorlagen und der Liste der Seiten zu Einwänden.
keywords: Einwände, Zweifel, Kritik, antworten, Methode, Forum, soziale Medien, Antwortvorlagen, Skeptiker, ehrliche Antworten, Quellen, objections, doubts, criticism, answering, method, forum, social media, reply templates, skeptics, honest answers, sources
type: help
---
# Einwände als Fürsprecher beantworten

Diese Seite ist für Fürsprecher, die auf Zweifel stoßen: „Ist das nicht tot?“, „PHP ist alt“, „Niemand nutzt es“,
„Warum nicht X?“. Der Vertrieb antwortet Käufern in Besprechungen ([Mit Einwänden umgehen](../geschaeft/einwandbehandlung.md));
Fürsprecher antworten Gleichgesinnten, oft öffentlich, wo viele andere mitlesen. Das ändert das Ziel: Sie versuchen
nicht, die Person vor Ihnen zu überzeugen, sondern hinterlassen eine genaue, freundliche Antwort für alle, die sie später
lesen.

## Inhalt

1. [Die Methode](#die-methode)
2. [Öffentlich und privat](#öffentlich-und-privat)
3. [Antwortvorlagen](#antwortvorlagen)
4. [Die Seiten zu Einwänden](#die-seiten-zu-einwänden)
5. [Wann Sie aufhören](#wann-sie-aufhören)

## Die Methode

| Schritt | Was Sie tun | Beispiel |
|---|---|---|
| 1. Zuhören | Lesen Sie die ganze Nachricht; zitieren Sie sie fair | „Sie schreiben, das Projekt wirke verlassen.“ |
| 2. Das eigentliche Anliegen finden | Fragen Sie, was dahintersteckt, wenn es unklar ist | „Meinen Sie Sicherheitskorrekturen, Releases oder Menschen, die man fragen kann?“ |
| 3. Anerkennen | Das Anliegen ist meist berechtigt | „Das sollte man prüfen, bevor man irgendein CMS wählt.“ |
| 4. Mit einer Quelle antworten | Eine Tatsache, ein Link | „Fünfzehn Releases 6.0.x seit Januar 2024: (Link).“ |
| 5. Eine Prüfung anbieten | Ein Befehl, eine Seite, ein Bildschirm | „`git tag -l 'v6.0.*' --sort=version:refname` zeigt sie alle.“ |
| 6. Grenzen zugeben | Sagen Sie, was die Quellen nicht sagen | „Die Quellen zählen keine Benutzer, also rate ich nicht.“ |
| 7. Einladen | Ein nächster Schritt, nie Druck | „Wenn Sie es ausprobieren, stehen die Foren und der Telegram-Chat offen.“ |

## Öffentlich und privat

| | Öffentlich (Forum, soziale Medien, Kommentare) | Privat (Chat, E-Mail, persönlich) |
|---|---|---|
| Ihr eigentliches Publikum | Alle, die später lesen | Die Person vor Ihnen |
| Länge | Kurz, mit Links | So lang, wie die Person möchte |
| Ton | Ruhig, sachlich, freundlich; kein Sarkasmus | Persönlich, neugierig |
| Fehler | Offen korrigieren ([Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md#wenn-sie-sich-geirrt-haben)) | Korrigieren und mit der Quelle nachfassen |
| Sicherheitsdetails | Nie | Nie: an `SECURITY.md` verweisen ([Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md)) |

## Antwortvorlagen

Passen Sie diese an; fügen Sie sie nie unverändert ein.

```text
ACKNOWLEDGE AND ANSWER
That's a fair question. <Fact in one sentence> (<link>). You can check it yourself with <command or page>.
If you have a specific case in mind, I'm happy to look at it.

WHEN YOU DO NOT KNOW
Good question - I don't know, and I don't want to guess. I'll look for a source and come back here.
(Then do come back, with the source or with "I could not find one".)

WHEN THE ANSWER IS "NO"
You're right that Exponential doesn't <thing>. <What it does instead, if anything> (<link>).
If <thing> is essential for you, <the family member or alternative approach that fits, if documented>.

WHEN THEY ARE PARTLY RIGHT
Part of that is right: <the true part, with source>. One detail has changed since: <fact> (<link>).
```

## Die Seiten zu Einwänden

| Einwand | Seite |
|---|---|
| „Ist das nicht tot? Ist das nicht das eingestellte Produkt?“ | [Einwand: „Lebt es noch?“](einwand-lebt-es-noch.md) |
| „PHP ist alt“, „die Oberfläche wirkt veraltet“ | [Einwand: PHP und moderne Technik](einwand-php-und-moderne-technik.md) |
| „Niemand kennt es; wir finden kein Personal“ | [Einwand: „Finden wir Leute?“](einwand-fachkraefte.md) |
| „Warum nicht ein anderes CMS?“ | [Einwand: „Warum nicht ein anderes CMS?“](einwand-warum-nicht-ein-anderes-cms.md) |
| „Open Source ist weniger sicher“ | [Einwand: „Ist Open Source sicher?“](einwand-sicherheit-von-open-source.md) |
| „KI ruiniert Inhalte“ / „wir brauchen KI“ | [Einwand: „Was ist mit den KI-Funktionen?“](einwand-ki-funktionen.md) |
| Eine Diskussion, die feindselig wird | [Mit feindseligen Diskussionen umgehen](feindselige-diskussionen.md) |

Für Käufer, mit mehr Zeilen (Support, Upgrades, Hosting): [Mit Einwänden umgehen](../geschaeft/einwandbehandlung.md#kurzübersicht).

## Wann Sie aufhören

- Nach zwei Antworten, wenn die andere Person keine Fragen mehr stellt. Die Leser haben, was sie brauchen.
- Wenn die Diskussion persönlich wird. Siehe [Mit feindseligen Diskussionen umgehen](feindselige-diskussionen.md).
- Wenn die ehrliche Antwort lautet: „Exponential passt dafür nicht.“ Sagen Sie es und wünschen Sie viel Erfolg
  ([Wo Exponential nicht passt](../geschaeft/exponential-sachlich-vergleichen.md#wo-exponential-nicht-die-richtige-wahl-ist)).

## Verwandte Seiten

- [Öffentlich antworten](oeffentlich-antworten.md)
- [Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md)
- [Mit Einwänden umgehen](../geschaeft/einwandbehandlung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
