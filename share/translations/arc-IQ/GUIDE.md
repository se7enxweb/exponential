# Imperial Aramaic (arc-IQ): how the catalogue is made

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Not a translation of its own

`arc-IQ` is Aramaic written in **Hebrew square script** (ארמית), the script Imperial Aramaic was written in and
that became the Hebrew one. Its catalogues are **not translated separately**: they are the Aramaic of the
[syr-IQ catalogue](../syr-IQ/GUIDE.md) (Aramaic in Syriac script), transliterated letter for letter. Wording,
glossary, form of address and placeholder rules are therefore those of `syr-IQ/GUIDE.md`; a correction is made in
`syr-IQ` and reaches this catalogue when it is transliterated again. Do not edit `translation.ts` here by hand.

The locale `share/locale/arc-IQ.ini` (Iraq) says where each of its values comes from.

## 2. The transliteration

The 22-letter abjads of Syriac and Hebrew map one to one:

| Syriac | ܐ | ܒ | ܓ | ܕ | ܗ | ܘ | ܙ | ܚ | ܛ | ܝ | ܟ | ܠ | ܡ | ܢ | ܣ | ܥ | ܦ | ܨ | ܩ | ܪ | ܫ | ܬ |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Hebrew | א | ב | ג | ד | ה | ו | ז | ח | ט | י | כ | ל | מ | נ | ס | ע | פ | צ | ק | ר | ש | ת |

- **Final forms** ך ם ן ף ץ at the end of a word of more than one letter.
- The extra Syriac letters become their base letter: dotless dalath-rish ܖ → ר, final semkath ܤ → ס, the
  Garshuni, Persian and Sogdian letters → ג ט ב ד ז כ פ.
- **Dropped**: the Syriac vowel points (East and West), qushshaya and rukkakha, the **seyame** (plural dots), the
  superscript alaph, the Syriac abbreviation mark, tatweel and zero-width joiners. The Hebrew text has no niqqud.
- **Punctuation**: Arabic comma ، → `,`, semicolon ؛ → `;`, question mark ؟ → `?`; the Syriac points ܀ ܁ ܂ → `.`,
  ܃ ܄ ܅ ܈ ܉ → `:`; guillemets « » → „ ”.
- **Untouched**: placeholders (`%name`, `%1`), HTML tags, entities, Latin text, digits, the XML of the file, and the
  sources and contexts. Placeholders stay free-standing, as in syr-IQ.

## 3. Making the catalogues

A script applies the rules of section 2 to the text of every `<translation>` of
`share/translations/syr-IQ/translation.ts`, `extension/ezoe/translations/syr-IQ/translation.ts` and
`extension/ezjscore/translations/syr-IQ/translation.ts` and writes the `arc-IQ` file next to each, with
`language="arc-IQ"`; sources, contexts and the order of the messages stay those of syr-IQ. It refuses to write a
file in which a Syriac letter is left in a translation, and comes with tests of the mapping. The messages whose text stays as the
source (codes, product names) are listed for `arc-IQ` in `tests/tests/lib/ezi18n/translation-identical-allowlist.json`
with the same reasons as for `syr-IQ`.

## 4. Review

The Aramaic was machine-assisted and is the modern Aramaic of syr-IQ, not reconstructed Achaemenid Aramaic:
technical words have no ancient counterpart. A review by a specialist is welcome.
