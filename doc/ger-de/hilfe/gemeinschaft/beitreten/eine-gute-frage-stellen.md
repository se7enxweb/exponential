---
title: Eine gute Frage stellen
audience: community, user, admin, developer, editor
category: community
order: 14
summary: Wie Sie in den Exponential-Foren oder -Chats eine Frage so stellen, dass jemand sie mit einer Antwort lösen kann - zuerst suchen, das Ziel beschreiben, die Umgebung aus der Systemkarte angeben, zeigen, was Sie versucht haben, und den Faden abschließen.
keywords: Frage, fragen, Foren, Telegram, Hilfe, Umgebung, Systeminformationen, exp:info, Version, PHP-Version, Vorlage, reproduzierbar, question, ask, forums, help, environment, system information, template, reproducible
type: help
---
# Eine gute Frage stellen

Die Menschen, die in den Foren und Chats antworten, geben ihre eigene Zeit. Eine Frage, die alles Nötige enthält, wird
schneller und besser beantwortet und hilft der nächsten Person, die nach demselben sucht.

## Inhalt

1. [Bevor Sie fragen](#bevor-sie-fragen)
2. [Was hineingehört](#was-hineingehört)
3. [Die Umgebung ermitteln](#die-umgebung-ermitteln)
4. [Eine Vorlage](#eine-vorlage)
5. [Nach der Antwort](#nach-der-antwort)

## Bevor Sie fragen

1. **Durchsuchen Sie die Hilfe** in Ihrer Administration (das „?“) und diese Dokumentation.
2. **Durchsuchen Sie die Foren**, die [Dokumentation](https://exponential.doc.exponential.earth/Exponential) und
   [eZpedia](https://ezpedia.exponential.earth/); die Hilfe nennt diese Quellen unter „Um Hilfe bitten“ in
   [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md).
3. **Prüfen Sie, ob es eine Frage ist und kein Fehler.** Wenn die Software eindeutig etwas falsch macht, melden Sie es
   stattdessen ([Einen Fehler melden](../mitwirken/einen-fehler-melden.md)).
4. **Prüfen Sie, dass es kein Sicherheitsproblem ist.** Solche gehen vertraulich an security@se7enx.com (`SECURITY.md`).

## Was hineingehört

| Teil | Warum |
|---|---|
| **Das Ziel**: was Sie erreichen möchten | Oft gibt es einen besseren Weg als den, auf dem Sie feststecken |
| **Was Sie getan haben**, Schritt für Schritt | Damit jemand es wiederholen kann |
| **Was Sie erwartet haben und was geschehen ist**, mit der genauen Meldung | Nach einer genauen Meldung kann man im Code suchen |
| **Die Umgebung**: Exponential-Version, PHP-Version, Webserver oder Engine, Datenbank, Design | Viele Antworten hängen davon ab |
| **Was Sie schon versucht haben** | Spart allen eine Runde |
| **Relevante Einstellungen oder Template-Code**, gekürzt | Nur der Teil, auf den es ankommt, ohne Passwörter |

## Die Umgebung ermitteln

Sie müssen die Umgebung nicht von Hand zusammentragen:

- In der Administration bietet **Setup** > **Systeminformationen** die Schaltfläche **Bericht als Text kopieren**.
- Auf der Kommandozeile gibt `./console exp:info --plain --no-logo` eine Zusammenfassung aus, die „nie ein Passwort,
  einen Schlüssel, ein Token, einen Datenbankbenutzer oder die vollständige Datenbankadresse ausgibt, sodass Sie sie
  unverändert einfügen können“
  ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md), [Die Systemkarte](../../../funktionen/6.0/systemkarte.md)).

## Eine Vorlage

```text
Goal: <what you want to achieve>
Steps: 1. ... 2. ... 3. ...
Expected: <...>
Happened: <...> (exact message: "...")
Environment: <paste of exp:info --plain, or version / PHP / engine / database>
Tried: <...>
```

Wählen Sie einen Titel, der das Problem benennt: „Mehrsprachige URL-Aliase fehlen nach dem Hinzufügen einer Sprache“
wird schneller beantwortet als „Bitte um Hilfe“.

## Nach der Antwort

- **Sagen Sie, ob es funktioniert hat.** Ein Thema ohne Abschluss lässt die nächsten Lesenden raten.
- **Veröffentlichen Sie Ihre eigene Lösung**, wenn Sie sie anderswo gefunden haben.
- **Danken Sie den Menschen, die geholfen haben.**
- **Schreiben Sie es auf**, auf eZpedia oder in einem Forenbeitrag, wenn die Antwort schwer zu finden war
  ([Tutorials und Beiträge schreiben](../anderen-helfen/tutorials-und-beitraege-schreiben.md)).

## Verwandte Seiten

- [Der Community beitreten](README.md)
- [Schritt für Schritt Hilfe bekommen](schritt-fuer-schritt-hilfe-bekommen.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Fragen beantworten](../anderen-helfen/fragen-beantworten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
