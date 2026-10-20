# LoveStack-Funktionen in Exponential: die ausführlichen Seiten

Exponential 6.0.15 übernimmt 36 Funktionen und eine Reihe von Korrekturen aus **LoveStack**, der Distribution von
Mugo Web des ursprünglich von eZ Systems veröffentlichten Legacy-Kernels (`mugoweb/ezpublish-legacy`), in die
Exponential-Distribution. Jede davon ist in den eigenen APIs von Exponential neu gebaut, dokumentiert und getestet,
mit Dank an Mugo Web.

Beginnen Sie mit dem Überblick, [LoveStack-Funktionen in Exponential 6](../lovestack.md): was jede Funktion tut, für
wen sie gedacht ist, wie Sie sie einschalten und aus welchem LoveStack-Commit sie stammt. Die Seiten hier gehen ins
Detail.

**Wenn Sie LoveStack nie betrieben haben, ist nichts zu tun.** Die Funktionen sind Teil von 6.0.15; jede, die das
Verhalten einer vorhandenen Website ändern könnte, ist ausgeschaltet, bis Sie sie einschalten.

## Die Seiten

| Seite | Für | Was sie enthält |
|---|---|---|
| [erste-schritte.md](erste-schritte.md) | alle | betrifft mich das, was ändert sich auf meiner Website, ein Glossar |
| [funktionen.md](funktionen.md) | Administratoren, Redakteure | jede Funktion in einer Tabelle pro Bereich, mit ihrem Standard bei einem Upgrade und bei einer neuen Installation |
| [api.md](api.md) | Entwickler | die Klassen, Methoden, Einstellungen, Template-Funktionen und Konsolenbefehle |
| [migration.md](migration.md) | Websites, die von LoveStack umziehen | Schritt für Schritt, von den Backups bis zu Ihrem eigenen Code |
| [testen.md](testen.md) | Entwickler, Prüfer | die Tests jeder Funktion und wie Sie sie ausführen |
| [zuordnung-der-tests.md](zuordnung-der-tests.md) | Entwickler, Prüfer | die eigenen Testfälle von LoveStack und wo sie in der Testsuite von Exponential stehen |
| [php8-pruefung.md](php8-pruefung.md) | Entwickler | die Korrekturen von LoveStack für PHP 7.2 bis 8.3, Stelle für Stelle, und was Exponential brauchte |
| [l01-aufrufer.md](l01-aufrufer.md) | Administratoren | wer `ezjscnode` aufruft und was es ändert, dafür eine Richtlinie zu verlangen |
| [faq.md](faq.md) | alle | häufige Fragen |
| [roadmap.md](roadmap.md) | alle | wie die Arbeit gemacht wurde und was als Nächstes kommt |
| [changelog.md](changelog.md) | alle | die Releases, die diese Funktionen enthalten |

Die Aufgabenanleitungen der Dokumentation beschreiben jede Funktion dort, wo Sie sie suchen würden (Rollen,
Sicherheit und Audit, Inhalte und Bearbeitung, Templates, URLs, Betrieb einer Website); der Überblick verlinkt für jede
Funktion auf den passenden Abschnitt.

## In Zahlen

Beide Projekte stammen vom selben Legacy-Kernel; ihre Geschichten trennen sich am 9. März 2016 (Upstream-Commit
`3429aa91bb`). Verglichen bis zum neuesten Commit von LoveStack, `e61e9fdf0c` vom 23. Februar 2026:

| | |
|---|---|
| Verglichene Änderungen von LoveStack | 272 Commits von Mugo Web und Mitwirkenden, 983 Dateien |
| Vor der Portierung bereits in Exponential | viele davon, oft in anderer Form: Kompatibilität mit PHP 7 und 8, SameSite-Cookies, Passwort-Hashing, das Datum im Papierkorb, WebP, SQLite und mehr ([Liste](../lovestack.md#bereits-in-exponential)) |
| Übernommene Funktionen | 36 (L01 bis L36), dazu der Name der Session-Einstellung `SameSite` von LoveStack und die PHP-8-Prüfung |
| Nicht übernommen | 14 Punkte, jeder mit seiner Begründung ([Liste](../lovestack.md#nicht-übernommen-und-warum)) |

## Über LoveStack

LoveStack wird von Mugo Web unter <https://github.com/mugoweb/ezpublish-legacy> entwickelt und auf Packagist als
[`mugoweb/ezpublish-legacy`](https://packagist.org/packages/mugoweb/ezpublish-legacy) veröffentlicht. Als die
Portierung gemacht wurde (12. Oktober 2026), war das neueste Release `v2025.03.1-1` (1. März 2025), der neueste
Commit stammte vom 23. Februar 2026, und die `composer.json` unterstützte PHP 7.3–7.4 und 8.1–8.3. Exponential 6
unterstützt PHP 8.1 bis 8.5.

Beide Projekte stehen unter der GNU GPL Version 2 (Exponential: Version 2 oder später). Code in Exponential, der von
einer Änderung in LoveStack abgeleitet ist, behält jeden vorhandenen Urheberrechtshinweis und nennt den
LoveStack-Commit, aus dem er stammt; siehe [Lizenz und Danksagungen](../lovestack.md#lizenz-und-danksagungen).
