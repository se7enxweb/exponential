# Burmese (mya-MM): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Burmese (မြန်မာဘာသာ) in the **formal written style** (စာပေ / literary register) used by Myanmar software
interfaces, government notices and newspapers: sentence endings သည်, ပါသည်, မည်, ပြီ; postpositions ၏, ကို, တွင်,
မှ, နှင့်, အတွက်. Not the colloquial spoken style (တယ်, မယ်, ရဲ့, မှာ). No other locale falls back to `mya-MM`.

- Short, plain sentences. English technical terms are translated where Burmese software has a word (ဖိုင်, ဆာဗာ and
  ဒေတာဘေ့စ် are established transliterations); product names and code stay in Latin letters.

## 2. Form of address

- The user is **သင်**, where a pronoun is needed; mostly the sentence needs none.
- **Buttons, menu items and links**: the verb with **-ရန်** (to ...), as current Burmese interfaces do: သိမ်းရန်,
  ပယ်ဖျက်ရန်, ဖျက်ရန်, ဖယ်ရှားရန်, တည်းဖြတ်ရန်, ထည့်ရန်, ဖန်တီးရန်, ရှာဖွေရန်, ရွေးရန်, ပို့ရန်, ကြည့်ရန်, ထုတ်ဝေရန်,
  တင်ရန်.
- **Titles, column headings and tabs**: nouns or noun phrases: ဆက်တင်များ, ဘာသာပြန်ချက်များ, အဓိကတည်နေရာ, အမည်.
- **Instructions**: the polite imperative with **-ပါ**, with "ကျေးဇူးပြု၍" where English has "please": "အရာကို ရွေးပြီး
  \"OK\" ကို နှိပ်ပါ။", "ကျေးဇူးပြု၍ ဦးစားပေးအဆင့် သိမ်းဆည်းပြီးသည်အထိ စောင့်ပါ။". A reference ("see ...") is "...
  ကို ကြည့်ပါ".
- **Confirmations and questions**: "... လိုသည်မှာ သေချာပါသလား။" or "... ရန် သေချာပါသလား။": "ဤအတန်းအစားကို ဖျက်ရန်
  သေချာပါသလား။".
- **What the user can or may do**: "... နိုင်သည်", "... ၍ မရပါ", "You do not have permission" is "... ရန် သင့်တွင်
  ခွင့်ပြုချက် မရှိပါ".
- **Messages about a result**: "... ပြီးပါပြီ" / "... ခဲ့သည်": "မူကြမ်းကို သိမ်းဆည်းပြီးပါပြီ။", "ဖိုင်ကို အောင်မြင်စွာ
  တင်ပြီးပါပြီ။".

## 3. Script and typography

- **Myanmar script in Unicode** (the Unicode encoding of the Myanmar block, as Myanmar adopted in 2019), never the
  Zawgyi font encoding. Characters in the Unicode storage order (consonant, medials ျ ြ ွ ှ, vowel signs, ံ, ့, ်,
  း).
- **Spaces**: Burmese does not separate words. Put a space only between phrases, as Burmese software does, and
  always around a placeholder, a Latin word, a number or code.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged, with a space before and after
  them: "%count ခု", "%name ကို". The same for code, file names, INI settings and product names, which stay in Latin
  letters.
- **Punctuation**: the section mark "။" ends a sentence, "၊" separates clauses. A source sentence that ends in ":"
  "?" "!" or "..." keeps that mark, except that a question ends "။" after သလား / လား as Burmese writes it — keep the
  English "?" only where the source is a bare label ending in "?".
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: Western digits 0-9 (not ၀-၉), so that numbers from placeholders and written numbers look the same.

## 4. Glossary

Binding for the kernel and every extension.

| English | Burmese | Note |
|---|---|---|
| content | အကြောင်းအရာ | |
| content object / object | အကြောင်းအရာ အရာဝတ္ထု / အရာဝတ္ထု | |
| item (list entry) | အရာ | sub items: အခွဲအရာများ |
| node | နုဒ် | main node: အဓိကနုဒ်; parent node: မိခင်နုဒ် |
| location | တည်နေရာ | main location: အဓိကတည်နေရာ |
| class / content class | အတန်းအစား / အကြောင်းအရာ အတန်းအစား | class group: အတန်းအစားအုပ်စု; a PHP class: PHP class |
| attribute | ဂုဏ်ရည် | |
| datatype | ဒေတာအမျိုးအစား | |
| version | ဗားရှင်း | version history: ဗားရှင်းမှတ်တမ်း |
| draft | မူကြမ်း | store draft: မူကြမ်းသိမ်းရန် |
| publish | ထုတ်ဝေရန် | published: ထုတ်ဝေပြီး; publishing: ထုတ်ဝေခြင်း |
| send for publishing | ထုတ်ဝေရန် ပို့ရန် | |
| discard | စွန့်ပစ်ရန် | discard draft: မူကြမ်းစွန့်ပစ်ရန် |
| archived / pending / rejected | မော်ကွန်းတင်ပြီး / ဆိုင်းငံ့ / ငြင်းပယ်ပြီး | |
| siteaccess | ဆိုက်ဝင်ရောက်မှု | siteaccess as a setting name stays in code |
| site, website | ဆိုက် / ဝက်ဘ်ဆိုက် | |
| section | အပိုင်း | the Exponential section; an INI section: INI အပိုင်း |
| role | အခန်းကဏ္ဍ | |
| policy | မူဝါဒ | one rule of a role |
| limitation | ကန့်သတ်ချက် | |
| permission | ခွင့်ပြုချက် | access denied: ဝင်ရောက်ခွင့် ငြင်းပယ်ခံရသည် |
| access | ဝင်ရောက်ခွင့် | |
| module | မော်ဂျူး | |
| function | လုပ်ဆောင်ချက် | |
| user / user group | အသုံးပြုသူ / အသုံးပြုသူအုပ်စု | |
| group | အုပ်စု | |
| anonymous | အမည်မသိ | anonymous user: အမည်မသိ အသုံးပြုသူ |
| administrator | စီမံခန့်ခွဲသူ | administration (interface): စီမံခန့်ခွဲမှု |
| username | အသုံးပြုသူအမည် | login (the name): ဝင်ရောက်အမည် |
| password | စကားဝှက် | |
| log in / log out | ဝင်ရန် / ထွက်ရန် | the act: အကောင့်ဝင်ခြင်း / ထွက်ခြင်း |
| e-mail | အီးမေးလ် | e-mail address: အီးမေးလ်လိပ်စာ |
| cache | ကက်ရှ် | clear cache: ကက်ရှ်ရှင်းရန် |
| template | ပုံစံပြား | override template: အစားထိုး ပုံစံပြား |
| override | အစားထိုး | |
| fallback | အရန် | fallback language: အရန်ဘာသာစကား |
| design | ဒီဇိုင်း | |
| workflow | လုပ်ငန်းစဉ် | |
| trigger | အစပျိုးချက် | |
| event | ဖြစ်ရပ် | |
| trash | အမှိုက်ပုံး | empty trash: အမှိုက်ပုံးရှင်းရန်; move to trash: အမှိုက်ပုံးသို့ ရွှေ့ရန် |
| bookmark | စာညှပ် | |
| settings | ဆက်တင်များ | a setting: ဆက်တင်; configuration: ပြင်ဆင်သတ်မှတ်မှု |
| setup | စနစ်ထည့်သွင်းမှု | the Setup tab |
| layout | အပြင်အဆင် | Exponential Layouts |
| block / zone | ဘလောက် / ဇုန် | |
| extension | တိုးချဲ့မှု | |
| package | ပက်ကေ့ဂျ် | |
| search | ရှာဖွေမှု | to search: ရှာဖွေရန်; search index: ရှာဖွေမှုအညွှန်း |
| log | မှတ်တမ်း | a log file: မှတ်တမ်းဖိုင်; never "log in" |
| queue | တန်းစီ | job queue: အလုပ်တန်းစီ |
| job / task | အလုပ် / တာဝန် | |
| cron | cron | |
| script | စခရစ် | |
| worker | အလုပ်သမား လုပ်ငန်းစဉ် | |
| kernel, core | ကာနယ် | |
| server | ဆာဗာ | |
| database | ဒေတာဘေ့စ် | |
| folder / directory | ဖိုင်တွဲ / လမ်းကြောင်းတွဲ | |
| file | ဖိုင် | |
| image | ပုံ | thumbnail: ပုံငယ် |
| preview | အစမ်းကြည့်ရှုမှု | |
| upload / download | တင်ရန် / ဒေါင်းလုဒ်ဆွဲရန် | nouns: တင်ခြင်း / ဒေါင်းလုဒ် |
| export / import | ထုတ်ယူရန် / တင်သွင်းရန် | |
| remove | ဖယ်ရှားရန် | take out of a list or place |
| delete | ဖျက်ရန် | destroy; deleted: ဖျက်ပြီးပါပြီ |
| edit | တည်းဖြတ်ရန် | |
| editor | တည်းဖြတ်သူ | the software: တည်းဖြတ်ကိရိယာ |
| save / store | သိမ်းရန် | the verb "Store" of the forms |
| Store (shop tab) | ဆိုင် | the shop, as "Shop" |
| cancel | ပယ်ဖျက်ရန် | |
| OK | အိုကေ | |
| apply | အသုံးချရန် | |
| back | နောက်သို့ | |
| next / previous | ရှေ့သို့ / ယခင် | next item: နောက်တစ်ခု |
| enable / disable | ဖွင့်ရန် / ပိတ်ရန် | enabled / disabled: ဖွင့်ထားသည် / ပိတ်ထားသည် |
| active / inactive | လက်ရှိအသုံးပြုနေ / အသုံးမပြု | |
| run | လုပ်ဆောင်ရန် | |
| start / stop | စတင်ရန် / ရပ်ရန် | |
| session | ဆက်ရှင် | |
| hidden / visible | ဝှက်ထားသည် / မြင်ရသည် | hide / reveal: ဝှက်ရန် / ပြရန် |
| archive | မော်ကွန်း | a file archive: ချုံ့ဖိုင် |
| order (sort) | အစီအစဉ် | sort order: စီစဉ်မှုအစီအစဉ် |
| order (shop) | မှာယူမှု | |
| shop | ဆိုင် | |
| basket | ခြင်းတောင်း | |
| product | ထုတ်ကုန် | |
| customer | ဖောက်သည် | |
| discount | လျှော့စျေး | |
| VAT | တန်ဖိုးမြင့်အခွန် | |
| price / currency | စျေးနှုန်း / ငွေကြေး | |
| tag | တဂ် | an HTML tag: HTML တဂ် |
| translation | ဘာသာပြန်ချက် | |
| language | ဘာသာစကား | main language: အဓိကဘာသာစကား |
| locale | ဒေသဆိုင်ရာ သတ်မှတ်ချက် | |
| state | အခြေအနေ | the object state; state group: အခြေအနေအုပ်စု |
| status | အနေအထား | kept apart from အခြေအနေ |
| view (button) | ကြည့်ရန် | a module view (noun): မြင်ကွင်း |
| format | ပုံစံ | |
| sort | စီရန် | |
| filter | စစ်ထုတ်ရန် | the filter: စစ်ထုတ်မှု |
| select | ရွေးရန် | select all: အားလုံးရွေးရန်; selected: ရွေးထားသော |
| browse | ရှာကြည့်ရန် | |
| copy / move | ကူးယူရန် / ရွှေ့ရန် | |
| add / create / new | ထည့်ရန် / ဖန်တီးရန် / အသစ် | |
| restore | ပြန်လည်ရယူရန် | |
| install / uninstall | ထည့်သွင်းရန် / ဖြုတ်ရန် | |
| subtree | အခွဲသစ်ပင် | |
| related objects | ဆက်စပ်အရာဝတ္ထုများ | relation: ဆက်နွယ်မှု |
| URL alias | URL အမည်ကွဲ | |
| redirect | လမ်းကြောင်းလွှဲရန် | |
| dashboard | ဒက်ရှ်ဘုတ် | |
| content structure | အကြောင်းအရာ ဖွဲ့စည်းပုံ | |
| media library | မီဒီယာစာကြည့်တိုက် | |
| identifier / ID | ခွဲခြားသတ်မှတ်စနစ် / ID | |
| priority | ဦးစားပေးအဆင့် | |
| owner | ပိုင်ရှင် | |
| modified / created | ပြင်ဆင်ပြီး / ဖန်တီးပြီး | |
| date / time | ရက်စွဲ / အချိန် | |
| error / warning | အမှား / သတိပေးချက် | |
| help | အကူအညီ | |
| notification | အသိပေးချက် | |
| wizard | လမ်းညွှန်အကူ | |
| feed (RSS) | ဖိဒ် | |
| tab | တဘ် | |
| account | အကောင့် | |
| default | မူလ | |
| toolbar | ကိရိယာဘား | |
| container | ထည့်စရာ | |
| collection | စုစည်းမှု | |
| approve, approval | အတည်ပြုရန်, အတည်ပြုချက် | |
| request / query | တောင်းဆိုမှု / မေးမြန်းချက် | |
| backup | အရန်ကူးယူမှု | |
| maintenance | ပြုပြင်ထိန်းသိမ်းမှု | |
| link | လင့်ခ် | |
| registration | မှတ်ပုံတင်ခြင်း | |
| device / browser | စက်ပစ္စည်း / ဘရောက်ဇာ | |
| none / all | မရှိ / အားလုံး | |
| yes / no | ဟုတ်ကဲ့ / မဟုတ်ပါ | |
| name / title / description | အမည် / ခေါင်းစဉ် / ဖော်ပြချက် | |
| value / type / list | တန်ဖိုး / အမျိုးအစား / စာရင်း | |
| page / path | စာမျက်နှာ / လမ်းကြောင်း | |
| required / optional | မဖြစ်မနေလိုအပ် / ရွေးချယ်နိုင် | |
| options | ရွေးချယ်စရာများ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ဆိုင်, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is သိမ်းရန်.
- "Login" in the user, session and audit lists is the user's login name (ဝင်ရောက်အမည်); the Login button of the
  error page is the act (ဝင်ရန်).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
