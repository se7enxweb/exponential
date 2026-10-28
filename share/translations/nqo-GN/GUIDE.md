# N'Ko (nqo-GN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

The Manding language written in the N'Ko script (ߒߞߏ), in the neutral, plain register of N'Ko newspapers, school books
and the N'Ko Wikipedia, which write the common Manding (Maninka of Guinea, Bambara of Mali, Dioula) shared by all its
readers. No other locale falls back to `nqo-GN`. The vocabulary follows the Bambara catalogue (`bam-ML`) and its
[glossary](../bam-ML/GUIDE.md), so that a term reads the same in both scripts; a word that Maninka of Guinea says
otherwise may take the Maninka form when it is the common one.

- Manding words are used where they exist; computing loans from French are written in N'Ko as the Bambara glossary
  spells them (fisiye -> ߝߌߛߌߦߋ, kasi -> ߞߊߛߌ).
- The plural is **ߟߎ** (-lu) written after the last word of the noun phrase, joined to it: ߝߌߛߌߦߋߟߎ (files).

## 2. Form of address

As in Bambara: the user is addressed with **ߌ** (you, singular) and **ߌ ߟߊ** / **ߌ ߞߊ** (your).

- **Buttons, menu items and links**: the bare verb (imperative): ߡߊߙߊ (save), ߘߊߓߌߟߊ (cancel), ߖߐߛߌ (delete),
  ߦߍߟߍߡߊ (edit), ߓߌߟߊ ߞߍߣߍ ߞߊ߲ (publish).
- **Titles, column headings and tabs**: nouns or noun phrases.
- **Instructions**: "ߌ ߦߋ ..." / "ߌ ߞߊ ...", softened with "ߤߊߞߍߕߏ߸" where a request needs it. A reference
  ("see ...") is "ߊ ߝߌߟߍ ...".
- **Confirmations and questions**: "ߌ ߓߋ ߊ ߝߍ߲ ߞߊ ... ߥߊ؟".
- **What the user can or may do**: "ߌ ߓߋ ߛߋ ߞߊ ...", "ߌ ߕߍ ߛߋ ߞߊ ...". "You do not have permission" is
  "ߦߊߡߊߙߎߦߊ ߕߍ ߌ ߓߟߏ ...".
- Not allowed: slash forms.

## 3. Script and typography

- **N'Ko** (Unicode block NKo, U+07C0-U+07FF), written **right to left**, the script of the shipped locale
  (`share/locale/nqo-GN.ini`, LanguageName ߒߞߏ, TextDirection rtl). Letters for the Latin spelling of the Bambara
  glossary: a ߊ, e ߋ, i ߌ, ɛ ߍ, u ߎ, o ߏ, ɔ ߐ, b ߓ, p ߔ, t ߕ, j ߖ, c ߗ, d ߘ, r ߙ, s ߛ, gb ߜ, g ߜ߳, f ߝ, k ߞ,
  l ߟ, m ߡ, ɲ ߢ, n ߣ, h ߤ, w ߥ, y ߦ, syllabic n / ŋ ߒ; a nasal vowel takes the nasalisation mark ߲ (U+07F2)
  after it (kan -> ߞߊ߲); a long vowel is written once. The tone diacritics are left out, as in plain N'Ko text on
  screens; a reviewer may add them.
- **Latin text inside N'Ko**: product names, codes, acronyms, placeholders, tags, file names, URLs and template code
  stay in Latin letters as they are; the browser's bidirectional algorithm places them. Never reverse or transliterate
  them. A sentence that is mostly Latin code still gets its N'Ko words in N'Ko.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free, with a space before
  and after them, or a punctuation mark right after. Never an N'Ko letter glued to a placeholder.
- **Numbers**: the number comes after the noun: "ߝߍ߲ %count". Digits 0-9 as in the source (the placeholders bring
  them in).
- **Punctuation**: the N'Ko comma ߸ (U+07F8) and exclamation mark ߹ (U+07F9), the Arabic question mark ؟ (U+061F);
  the full stop and the colon as in the source. A label keeps its colon. Where the source's punctuation is code (a
  path, an INI line, `{...}`), keep it exactly.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.

## 4. Glossary

Binding for the kernel and every extension. The terms are those of the Bambara glossary (`bam-ML/GUIDE.md`), written
in N'Ko by the letters above; the main ones:

| English | N'Ko | Bambara (Latin) |
|---|---|---|
| content | ߞߐߣߐߞߏ | kɔnɔko |
| object | ߝߍ߲ | fɛn |
| node | ߣߐߘߌ | nɔdi |
| location | ߦߐߙߐ | yɔrɔ |
| class | ߞߌߟߊߛߌ | kilasi |
| attribute | ߊߕߌߙߌߓߌ | atiribi |
| version | ߥߍߙߌߛߌߦߐ߲ | wɛrisiyɔn |
| draft | ߓߙߎߦߐ߲ | bruyɔn |
| publish | ߓߌߟߊ ߞߍߣߍ ߞߊ߲ | bila kɛnɛ kan |
| section | ߛߍߞߌߛߌߦߐ߲ | sekisiyɔn |
| role | ߖߐߦߐߙߐ | jɔyɔrɔ |
| permission | ߦߊߡߊߙߎߦߊ | yamaruya |
| module | ߡߏߘߌߟߌ | modili |
| user | ߓߊߙߊߞߍߟߊ | baarakɛla |
| group | ߖߍߞߎߟߎ | jɛkulu |
| administrator | ߡߊߙߊߓߊߜ߳ߊ | marabaga |
| username | ߓߊߙߊߞߍߟߊ ߕߐߜ߳ߐ | baarakɛla tɔgɔ (the column "Login" too) |
| password | ߜ߳ߎ߲ߘߏߘߊߢߍ | gundodaɲɛ |
| log in / log out | ߘߏ߲ / ߓߐ | don / bɔ |
| e-mail | ߌߡߍߟߌ | imɛli |
| cache | ߞߊߛߌ | kasi |
| template | ߡߏߘߍߟߌ | modɛli |
| trash | ߣߐߜ߳ߐߛߌߜ߳ߌߦߐߙߐ | nɔgɔsigiyɔrɔ |
| settings | ߟߊߓߍ߲ߟߎ | labɛnw |
| layout | ߛߌߜ߳ߌߗߏߜ߳ߏ | sigicogo |
| search | ߢߌߣߌ | ɲini |
| file / folder | ߝߌߛߌߦߋ / ߘߏߛߌߦߋ | fisiye / dosiye |
| image | ߖߊ | ja |
| server | ߛߍߙߌߥߍߙߌ | sɛriwɛri |
| database | ߞߎ߲ߣߊߝߏߣߌߛߏ | kunnafoniso |
| remove / delete | ߓߐߛߌ / ߖߐߛߌ | bɔsi / jɔsi |
| edit | ߦߍߟߍߡߊ | yɛlɛma |
| save / store | ߡߊߙߊ | mara |
| Store (shop tab) | ߓߎߕߌߞߌ | Butiki |
| cancel | ߘߊߓߌߟߊ | dabila |
| OK | OK | stays as on the Bambara buttons |
| select | ߛߎߜ߳ߊ߲ߘߌ | sugandi |
| add | ߝߊߙߊ ߊ ߞߊ߲ | fara a kan |
| create | ߘߌߟߊ߲ | dilan |
| new | ߞߎߙߊ | kura |
| view | ߟߊߖߍ | lajɛ |
| language | ߞߊ߲ | kan |
| translation | ߓߊߦߍߟߍߡߊߟߌ | bayɛlɛmali |
| name / title | ߕߐߜ߳ߐ / ߞߎ߲ߕߌߟߍ߲ߣߊ | tɔgɔ / kuntilenna |
| description | ߢߍߝߐߟߌ | ɲɛfɔli |
| type | ߛߎߜ߳ߎߦߊ | suguya |
| list / page | ߟߌߛߌ / ߢߍ | lisi / ɲɛ |
| value | ߤߊߞߍ | hakɛ |
| error / warning | ߝߌߟߌ / ߟߊߛߐߡߌߣߌ | fili / lasɔmini |
| help | ߘߍߡߍ | dɛmɛ |
| date / time | ߘߏ߲ / ߥߊߕߌ | don / waati |
| shop | ߓߎߕߌߞߌ | butiki |
| price | ߛߐ߲ߜ߳ߐ | sɔngɔ |
| yes / no | ߐߥߐ / ߊߦߌ | ɔwɔ / ayi |

Every other term: the Bambara glossary's word, written in N'Ko.

## 5. Decisions after the translation

- The header tab "Store" of the shop is ߓߎߕߌߞߌ, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is ߡߊߙߊ.
- "Login" is the user name (ߓߊߙߊߞߍߟߊ ߕߐߜ߳ߐ) in the user, session and audit lists, and the act of logging in (ߘߏ߲)
  on the error page, as in the other right-to-left translations.
