# Häufige Fragen

**Ich betreibe Exponential und habe LoveStack nie betrieben. Muss ich etwas tun?**
Nein. Die Funktionen sind Teil von Exponential 6.0.15. Unter
[Erste Schritte](erste-schritte.md#was-ändert-sich-auf-meiner-exponential-website) steht, was sich nach dem Upgrade
ändert und welche Funktionen Sie vielleicht einschalten möchten.

**Verhält sich meine Website nach dem Upgrade anders?**
Nur dort, wo eine Funktion einen eindeutigen Fehler behebt oder eine Sicherheitslücke für jede Website schließt (das
Abmelden löscht das Sitzungs-Cookie, hochgeladene Bilder werden anhand ihres Inhalts geprüft), dazu einige kleine
Änderungen an der Protokollierung (Deprecations in einem eigenen Log, Log-Dateien von 2 MB) und eine Schaltfläche
Kopieren in der Administration. Funktionen, die ändern könnten, was Besucher oder Redakteure sehen, sind auf
vorhandenen Websites ausgeschaltet. [funktionen.md](funktionen.md) führt den Standard jeder Funktion auf.

**Warum erhalten neue Installationen andere Standardwerte?**
Wo eine Funktion eine Website sicherer macht, aber etwas beeinträchtigen könnte, worauf sich eine vorhandene Website
verlässt (offene Registrierung, anonymes JavaScript, das Knotendaten liest, doppelte Anmeldenamen), beginnt eine neue
Website mit der sichereren Einstellung, und eine aktualisierte Website behält das heutige Verhalten, bis Sie
entscheiden.

**Wie schalte ich eine Funktion ein?**
Jeder Eintrag des [Überblicks](../lovestack.md#funktion-für-funktion) hat eine Zeile „Einschalten“. Meist ist es eine
Einstellung in einer Override-Datei, gefolgt von `php bin/php/ezcache.php --clear-tag=ini`.

**Kopiert Exponential den Code von LoveStack?**
Jede Funktion wurde in den eigenen APIs von Exponential neu gebaut, mit eigenen Tests. Jede Funktion wird als von der
Arbeit von Mugo Web abgeleitet behandelt; daher behält jede Datei mit abgeleitetem Code jeden vorhandenen
Urheberrechtshinweis, nennt den LoveStack-Commit, aus dem sie stammt, und würdigt Mugo Web, wie es die GNU GPL und
gute Praxis verlangen. Siehe [Lizenz und Danksagungen](../lovestack.md#lizenz-und-danksagungen).

**Warum wird LoveStack nicht in Exponential gemergt?**
Die beiden haben sich seit 2016 getrennt entwickelt. Vieles, was LoveStack in dieser Zeit geändert hat, hat auch
Exponential geändert, auf seine eigene Weise (Unterstützung für PHP 8.5, seine Engines, sein Caching, sein
Audit-Protokoll). Der Neubau Funktion für Funktion bewahrt, was jedes Projekt gut macht, und ändert nichts, worauf sich
eine vorhandene Exponential-Website verlässt.

**Wird das von Mugo Web unterstützt?**
Eine Unterstützung ist nicht impliziert. LoveStack ist die Distribution von Mugo Web und bleibt ihre; Exponential
verwendet den Namen nur, um zu sagen, woher eine Funktion stammt.

**Welche PHP-Versionen?**
Exponential 6: PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar). Die `composer.json` von LoveStack (beim für die Portierung verwendeten Commit):
PHP 7.3–7.4 und 8.1–8.3.

**Verliere ich `ezenum`-Inhalte, wenn ich von LoveStack umziehe?**
Nein. Exponential behält den Datentyp `ezenum` im Kernel. Deaktivieren Sie die Erweiterung `legacy_2018` von
LoveStack, die ihn dort enthielt ([Migration, Schritt 4.1](migration.md#41-legacy_2018-entfernen)).

**Meine LoveStack-Datenbank hat einen eindeutigen Index auf den Anmeldenamen. Ist das ein Problem?**
Nein. Exponential funktioniert damit, und neue Exponential-Installationen haben denselben Index. Auf einer
aktualisierten Exponential-Website meldet `./console exp:user-login-check` Duplikate und fügt den Index auf Wunsch
hinzu.

**Meine LoveStack-Datenbank hat `data_text NOT NULL`. Ist das ein Problem?**
Nein. Exponential speichert dort eine leere Zeichenkette, nie NULL; die Spalte kann bleiben, wie sie ist.

**Setzt Exponential `Content-Length` wie `SetContentLengthHeader` von LoveStack?**
Die Engines und der HTTP-Cache von Exponential senden ihn für die Antworten, die sie ausliefern. Die
LoveStack-Einstellung wird nicht gebraucht; entfernen Sie sie.

**Woran erkenne ich, dass die Funktionen funktionieren?**
Jede Funktion hat PHPUnit-Tests, die für sich laufen, und die eigenen Testfälle von LoveStack für Verhalten, das
Exponential hat, sind Teil der Suite. Siehe [testen.md](testen.md).

**Wo melde ich ein Problem?**
Im Issue-Tracker von Exponential: <https://github.com/se7enxweb/exponential/issues>. Bitte melden Sie Probleme mit
Exponential nicht an Mugo Web.
