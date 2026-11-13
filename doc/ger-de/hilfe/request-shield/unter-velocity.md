---
title: "Request Shield: unter Velocity"
audience: admin, hosting, developer
category: administration
order: 60
summary: Was für den Request Shield anders ist, wenn Exponential Velocity die Website ausliefert - die Prepend-Datei in index.php, der Neustart nach Änderungen am Hook, gemeinsame Zähler mit set store file, das sichere Aktualisieren der Bibliothek - und warum die Antworten mit denen unter PHP-FPM übereinstimmen.
keywords: Request Shield, Velocity, Exponential Velocity, vc, PHP-FPM, EXP_REQUEST_PREPEND_FILE, prepend.php, index.php, Worker, Neustart, exp:velocity restart, Speicher, set store file, APCu, Exit-Signal, shield error, identische Antworten, Bibliothek aktualisieren, restart, store, identical answers
type: help
---
# Request Shield: unter Velocity

Exponential Velocity hält PHP-Worker zwischen den Anfragen am Laufen. Der Shield schützt die Anfragen über Velocity
genauso wie die über PHP-FPM, mit **identischen Antworten**: derselbe Status, dieselben Header, dieselbe
Protokollzeile. Hinter den Kulissen funktionieren einige Dinge anders.

## Inhalt

1. [Wie der Shield jede Anfrage erreicht](#wie-der-shield-jede-anfrage-erreicht)
2. [Wann Sie Velocity neu starten](#wann-sie-velocity-neu-starten)
3. [Zähler, die alle Prozesse teilen](#zähler-die-alle-prozesse-teilen)
4. [Die Extension aktualisieren](#die-extension-aktualisieren)
5. [Warum die Antworten gleich sind](#warum-die-antworten-gleich-sind)

## Wie der Shield jede Anfrage erreicht

Unter PHP-FPM läuft `config.php` bei jeder Anfrage, also läuft der Shield dort. Unter Velocity läuft `config.php`
einmal pro Worker, wenn der Worker startet. Der Hook nennt seine Datei deshalb in `EXP_REQUEST_PREPEND_FILE`, und
`index.php` führt diese Datei zu Beginn jeder Anfrage aus.

Das setzt das `index.php` von **6.0.15** voraus. Mit einem älteren werden die Anfragen über Velocity nicht gefiltert,
die über PHP-FPM schon. Die Übersicht meldet dann „Der Shield lief für diese Seite nicht“, wenn Sie sie über Velocity
öffnen.

## Wann Sie Velocity neu starten

| Änderung | Neustart? |
|---|---|
| Hook-Zeilen in `config.php` hinzugefügt oder entfernt | ja: `./console exp:velocity restart` |
| eine Regeldatei geändert oder ein Modus auf der Übersicht gewechselt | nein: innerhalb von `recheck` Sekunden neu gelesen |
| **Pausieren** oder **Einschalten** | nein: ab der nächsten Anfrage |
| die Extension aktualisiert | ja, mit einer Pause davor und danach (unten) |

## Zähler, die alle Prozesse teilen

Velocity betreibt mehrere Prozesse. Mit dem Speicher in APCu würde jeder Prozess für sich zählen, und eine über die
Worker verteilte Anfrageflut erreichte ihr Limit später. `set store file` in der Regeldatei lässt alle Worker, und
PHP-FPM, wenn es dieselbe Website ausliefert, die Zähler in `var/request-shield/store/` teilen. Der Kasten
**Einrichtung** auf der Übersicht zeigt den Speicher und seine Stufe.

## Die Extension aktualisieren

Ein Worker, der die alten Klassen schon geladen hat, darf nicht auf die neuen Dateien treffen. Aktualisieren Sie in
dieser Reihenfolge:

1. **Pausieren** (oder `var/request-shield/PAUSED` anlegen).
2. Die Extension aktualisieren.
3. `./console exp:velocity restart`.
4. **Einschalten** (oder `PAUSED` entfernen).

## Warum die Antworten gleich sind

In einem Velocity-Worker löst `exit()` das Exit-Signal von Velocity aus, damit die Anfrage endet und nicht der
Worker. Die Extension lässt dieses Signal durch, wenn der Shield geantwortet hat (eine Abweisung, die Browserprüfung,
eine seiner eigenen Seiten), sodass nichts es für ein Versagen des Shields hält. Sollte eine künftige Bibliothek ihre
inneren Schritte ändern, bemerkt die Extension das und greift auf den einfachen Aufruf der Bibliothek zurück; die
Antworten bleiben richtig, nur der Debug-Header und das Fehlerlog von PHP zeigen dann wieder „shield error“. Die
Einzelheiten stehen in
[doc/velocity.md](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/velocity.md) der Extension (auf
Englisch).

Velocity allgemein: [Velocity ist Teil von Exponential 6.0.15](../velocity-ist-teil-von-exponential-6-0-15.md).
