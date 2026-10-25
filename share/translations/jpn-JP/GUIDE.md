# Japanese (jpn-JP): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard modern Japanese (共通語) as used in Japan, written in the polite style (です・ます調). No other locale falls
back to this catalogue. The tone is neutral, concise and professional, as in the Japanese versions of Windows,
Microsoft 365 and common web CMS. The terminology follows the Microsoft Japanese terminology and style guide where
it has a term; no invented words, no colloquial or overly humble language (no させていただきます, no
お～いただけますでしょうか).

The older, partial Japanese catalogue set several conventions that remain valid: the polite style with
してください, the confirmation question ～してもよろしいですか？, the button nouns 保存, 削除, 編集, キャンセル,
適用, 破棄, and the terms 配置先, セクション, ロール, ポリシー, 制限, 下書き, 識別子, 権限, 保留中, 昇順/降順.
Other forms of that catalogue are no longer used: ユーザ (now ユーザー, see section 3), デフォルト (now 既定),
ステート (now 状態), エクステンション (now 拡張機能), 複製 (now コピー), ブラウズ (now 参照), the pronoun あなた,
half-width "?" and straight quotes in running text.

## 2. Form of address

- **Buttons, menu items, links and action labels** are nouns or noun phrases (サ変 nouns without する): 「保存」,
  「削除」, 「編集」, 「追加」, 「キャンセル」, 「新規作成」, 「変更の適用」, 「下書きの保存」, 「下書きの破棄」,
  「公開に送信」, 「すべて選択」, 「選択項目の削除」. A verb phrase is used only where no noun form is natural
  (「ごみ箱を空にする」). Established Microsoft button forms stay as they are, although they are verbs:
  「戻る」, 「次へ」, 「前へ」, 「閉じる」, 「開く」, 「はい」, 「いいえ」, 「OK」.
- **Titles, column headings and tabs** are nouns: 「コンテンツ構造」, 「ユーザーアカウント」, 「レイアウトの種類」,
  「通知設定」, 「オブジェクトの状態」, 「作成日時」.
- **Sentences, hints and messages** use です・ます. Instructions use ～してください:
  - 「ここから作成される「%class」オブジェクトの配置先を選択してください。」 (Please choose a location for …)
  - 「有効なメールアドレスを入力してください。」
  - 「接続を確認して、もう一度お試しください。」
  - 「バージョン履歴の設定は content.ini で変更できます。」
- **Questions and confirmations** use ～してもよろしいですか？ or ～しますか？:
  - 「このクラスを削除してもよろしいですか？」
  - 「承認者にメッセージを送信しますか？」
- **Error messages** state the fact politely, without blaming the user: 「クラスを削除する権限がありません。」,
  「下書きを作成できませんでした。」, 「ファイルが見つかりません。」
- **Status messages** are passive or neutral: 「下書き 1 件が削除されました。」, 「通知設定が保存されました。」,
  「cron ジョブの実行は見つかりませんでした」.
- **Tooltips that describe a click**: 「アイコンをクリックすると、コンテキストメニューが表示されます。」 or
  「～するには、ここをクリックしてください。」
- The user is **not named**. Japanese leaves the subject out; where it cannot be left out, use ユーザー or
  name the role (「承認者」, 「編集者」). "your account" is 「アカウント」.
- Not allowed: あなた, 君, plain style (だ／である) in sentences, the imperative (～しろ, ～せよ), ～して下さい
  with the kanji 下さい (always ください), honorific excess (させていただきます, ～でございます), and a verb with
  する on a button where a noun works (「保存する」 is wrong, 「保存」 is right).

## 3. Script and typography

- **Script**: kanji, hiragana and katakana in the usual mix; Jōyō kanji only; words outside the Jōyō list in
  hiragana (「ごみ箱」, 「折りたたみ」, 「すべて」, 「ください」). Product names, codes, file names, formats and
  abbreviations stay in Latin letters (TRANSLATING.md, rule 5): Exponential, Solr, PHP, URL, ID, JSON.
- **Katakana long vowel**: Microsoft rule. A word ending in -er, -or, -ar, -y in English takes the long vowel ー:
  ユーザー, サーバー, フォルダー, フィルター, コンテナー, エディター, プロバイダー, ハンドラー, コントローラー,
  パラメーター, ブラウザー, マネージャー. Exceptions as in Microsoft, without ー: カテゴリ, ディレクトリ, メモリ,
  リポジトリ, レジストリ, エントリ.
- **Katakana compounds** are written together, without ・ or a space: ユーザーグループ, コンテンツクラス,
  ワークフローイベント.
- **Latin words before katakana**: an upper-case abbreviation or the word Web and the katakana word after it are
  written together: URLエイリアス, リモートID, Webサイト, Webサーバー, RSSフィード, INIキャッシュ, PHPファイル,
  APIキー, HTMLタグ. Kept apart with a half-width space: lower-case code words and product names (cron ジョブ,
  ini ファイル, content.ini で, Exponential の, Solr コア), SQL keywords and HTTP methods quoted as code
  (CREATE ステートメント, POST フォーム), and an abbreviation followed by kana or kanji (「URL の」,
  「SQL のデバッグ出力」).
- **Full-width punctuation** in Japanese text: 。 、 ？ ！ （ ） 「 」. A source full stop "." becomes 「。」, "?"
  becomes 「？」, "!" becomes 「！」. A colon after a label stays the half-width ":" of the source; an ellipsis
  "..." stays as in the source (Microsoft menu style: 「現在の設定に名前を付けて保存...」). Parentheses around
  purely Latin content (codes, numbers) may stay half-width: (5), (JSON).
- **Quotation marks**: 「…」 for quoted text, button and field names and values in running text: 「保存」ボタンを
  クリックしてください。 Nested quotes: 『…』. Straight quotes of the source stay inside HTML attributes, JavaScript
  and where the source quotes code or a value typed exactly.
- **Spacing**: no space between Japanese characters, and no space before or after full-width punctuation. A
  placeholder adjacent to Japanese text gets one half-width space on each side for readability: 「%name を削除
  しますか？」, 「%count 件のオブジェクトが削除されます。」 No space where the placeholder touches punctuation or a
  bracket: 「<%name>」, 「「%name」」, 「%1。」.
- **Particles after placeholders**: Japanese does not inflect nouns; case is marked by particles (を, が, の, に),
  which are kana and therefore cannot lengthen a placeholder. Still separate them with the half-width space:
  「%class_name の属性」, never `%class_nameの属性`.
- **Plural and counts**: Japanese has no plural; "item(s)", "object(s)" are translated once, without a plural
  marker (「アイテム」, 「オブジェクト」). Counted nouns take a counter after the number: 件 for records, results,
  messages and entries (「%count 件」), 個 for things (「%count 個のファイル」), 人 for people
  (「%count 人のユーザー」), 回 for runs. Where the counter is unclear, use the label form 「オブジェクト: %count」.
- **Numbers**: half-width digits 0-9; thousands separators and decimals come from the locale. Time units after a
  number: 秒, 分, 時間, 日: 「%n 分前」. Dates and times come from the locale settings, not from the text.
- **Word order** may follow Japanese grammar; placeholders may move (TRANSLATING.md, rule 1).

## 4. Glossary

Binding for the kernel and every extension. Section 5 adds the terms that were settled after the translation.

| English | Japanese | Note |
|---|---|---|
| content | コンテンツ | |
| content object / object | コンテンツオブジェクト / オブジェクト | |
| item (content in the tree) | アイテム | sub items: 子アイテム |
| item (entry in a list or form) | 項目 | selected items: 選択項目 |
| node | ノード | main node: メインノード |
| location / main location | 配置先 / メインの配置先 | |
| content class / class | コンテンツクラス / クラス | a PHP class: PHPクラス |
| class group | クラスグループ | not クラスカテゴリ |
| attribute | 属性 | |
| datatype | データ型 | |
| version | バージョン | version history: バージョン履歴 |
| draft | 下書き | Store draft: 下書きの保存; Discard draft: 下書きの破棄 |
| publish / published / publishing | 公開 / 公開済み / 公開 | Send for publishing: 公開に送信 |
| translation / language | 翻訳 / 言語 | |
| main language / content language | メイン言語 / コンテンツ言語 | interface language: 表示言語 |
| siteaccess | サイトアクセス | |
| site / website | サイト / Webサイト | |
| design (Exponential design) | デザイン | |
| structure | 構造 | |
| template | テンプレート | |
| override / override template | オーバーライド / オーバーライドテンプレート | overridden: オーバーライド済み |
| fallback (language, design) | フォールバック | fallback language: フォールバック言語 |
| alias / URL alias | エイリアス / URLエイリアス | |
| wildcard (URL) | ワイルドカード | |
| cache / clear cache | キャッシュ / キャッシュのクリア | |
| workflow / workflow event | ワークフロー / ワークフローイベント | |
| trigger / event | トリガー / イベント | |
| role / policy | ロール / ポリシー | |
| permission / access | 権限 / アクセス | access denied: アクセスが拒否されました |
| limitation (policy) | 制限 | a numeric limit or maximum: 上限; no リミット |
| user / user group / user account | ユーザー / ユーザーグループ / ユーザーアカウント | |
| anonymous user | 匿名ユーザー | |
| administrator / admin interface | 管理者 / 管理画面 | |
| log in, sign in / log out, sign out | ログイン / ログアウト | signed in: ログイン中; not サインイン |
| login (field) / username | ユーザー名 | |
| password | パスワード | |
| e-mail / e-mail address | メール / メールアドレス | |
| section | セクション | always the Exponential section |
| object state / state | オブジェクトの状態 / 状態 | state group: 状態グループ |
| relation / related objects | 関連 / 関連オブジェクト | reverse related: このオブジェクトを参照しているオブジェクト |
| trash | ごみ箱 | Empty trash: ごみ箱を空にする |
| bookmark | ブックマーク | |
| sub-items / children / parent | 子アイテム / 子 / 親 | parent node: 親ノード |
| subtree | サブツリー | |
| owner / creator / modifier | 所有者 / 作成者 / 更新者 | |
| created / modified | 作成日時 / 更新日時 | column headings; not 修正日時 |
| layout (Exponential Layouts) | レイアウト | layout type: レイアウトの種類; the name Exponential Layouts stays |
| layout (directory or release structure) | 構成 | |
| block / zone | ブロック / ゾーン | |
| setting / settings | 設定 | |
| Setup (admin tab) | システム設定 | |
| configuration / configure | 構成 / 設定する | |
| Content structure | コンテンツ構造 | |
| Media library | メディアライブラリ | |
| Dashboard | ダッシュボード | |
| Look and feel | 外観 | |
| extension | 拡張機能 | not エクステンション |
| package | パッケージ | |
| module | モジュール | |
| view (module view, noun) | ビュー | |
| View (button) | 表示 | |
| function (policy function) / function (PHP) | 機能 / 関数 | |
| search / search engine | 検索 / 検索エンジン | |
| search index / reindex / index (verb) | 検索インデックス / 再インデックス / インデックスを作成 | |
| index (database) | インデックス | |
| cronjob | cron ジョブ | |
| schedule | スケジュール | |
| job / queue / worker | ジョブ / キュー / ワーカー | job queue: ジョブキュー |
| log / log file | ログ / ログファイル | never "log in" |
| audit / audit trail | 監査 / 監査証跡 | |
| notification | 通知 | notification settings: 通知設定 |
| collaboration | コラボレーション | not 協同作業 |
| preview | プレビュー | |
| ID / remote ID / identifier | ID / リモートID / 識別子 | |
| file / folder / directory | ファイル / フォルダー / ディレクトリ | |
| image / media | 画像 / メディア | |
| thumbnail | サムネイル | |
| upload / download | アップロード / ダウンロード | |
| export / import | エクスポート / インポート | |
| shop | ショップ | |
| Store (shop tab of the admin header) | ショップ | Store dashboard: ショップダッシュボード; every other Store: 保存 |
| basket / cart | 買い物かご | never カート |
| order (shop) | 注文 | order status: 注文ステータス |
| order (sorting) | 並べ替え順序 / 順序 | "in order to" is not a term |
| customer / product | 顧客 / 商品 | |
| checkout | 購入手続き | |
| VAT / VAT type | 付加価値税 / 付加価値税の種類 | |
| discount / discount rule | 割引 / 割引ルール | |
| rule | ルール | |
| price / currency | 価格 / 通貨 | |
| wish list | ウィッシュリスト | |
| payment gateway / gateway | 決済ゲートウェイ / ゲートウェイ | |
| newsletter / subscriber | ニュースレター / 購読者 | |
| subscription / subscribe | 購読 / 購読する | |
| tag (keyword, eztags) / keywords | タグ / キーワード | |
| tag (HTML/XML) / custom tag | タグ / カスタムタグ | write HTMLタグ where both meanings are near |
| locale / localization | ロケール / ローカライズ | INI keys such as Locale stay as written |
| move / copy / swap | 移動 / コピー / 入れ替え | not 複製; an entry that exists twice: 重複 |
| embedding (vector) / chunk / vector | 埋め込み / チャンク / ベクトル | |
| embed / embedded (object in text) | 埋め込み / 埋め込まれた | |
| prompt / model / token | プロンプト / モデル / トークン | |
| AI | AI | |
| deploy / deployment / release | デプロイ / デプロイ / リリース | |
| enable / disable | 有効にする / 無効にする | buttons: 有効化 / 無効化 |
| enabled / disabled | 有効 / 無効 | |
| active / inactive | アクティブ / 非アクティブ | |
| error / warning / notice | エラー / 警告 / 注意 | not 注意 for warning |
| required / optional | 必須 / 省略可能 | |
| default | 既定 | default value: 既定値; not デフォルト |
| priority | 優先度 | |
| sort / ascending / descending | 並べ替え / 昇順 / 降順 | |
| visible / hidden | 表示 / 非表示 | |
| hide / reveal (show) | 非表示 / 表示 | buttons; in sentences: 非表示にする / 表示する |
| expand / collapse | 展開 / 折りたたみ | |
| OK / Cancel / Yes / No | OK / キャンセル / はい / いいえ | |
| Back / Next / Previous / Close | 戻る / 次へ / 前へ / 閉じる | Microsoft button forms |
| select / select all | 選択 / すべて選択 | |
| Select none / Deselect all | 選択解除 / すべての選択を解除 | |
| apply | 適用 | Apply changes: 変更の適用 |
| save / store | 保存 | |
| discard | 破棄 | |
| edit | 編集 | |
| remove | 削除 | taking out of a list, group or selection: name the container (グループから削除); an assignment: 割り当て解除; Remove formatting: 書式のクリア |
| delete | 削除 | |
| new / create / add | 新規 / 作成 / 追加 | Create new: 新規作成 |
| refresh / update / reset | 最新の情報に更新 / 更新 / リセット | |
| restore | 復元 | from the trash, a version |
| statistics / system information | 統計 / システム情報 | |
| database | データベース | |
| browse | 参照 | not ブラウズ |
| path | パス | |
| handler / wizard | ハンドラー / ウィザード | |
| backend / provider / endpoint | バックエンド / プロバイダー / エンドポイント | |
| method / listener / controller | メソッド / リスナー / コントローラー | |
| allowlist / read-only / argument | 許可リスト / 読み取り専用 / 引数 | |
| dry run / self-test | テスト実行 / 自己診断 | |
| slot / collection / facets / elevate | スロット / コレクション / ファセット / 優先表示 | collection in MongoDB and Solr |
| filter | フィルター | |
| tick (checkbox) | オンにする | untick: オフにする |
| container (class flag "Is container", Bootstrap/CSS) | コンテナー | |
| approve / approval / approved / approver | 承認 / 承認 / 承認済み / 承認者 | |
| deny / reject / rejected | 拒否 / 却下 / 却下済み | |
| accept / accepted | 受け入れ / 受け入れ済み | |
| pending | 保留中 | |
| redirect / forward (URL) | リダイレクト / 転送 | |
| charset | 文字セット | |
| license | ライセンス | |
| privilege (database) / grant | 権限 / 付与 | |
| format / formatting | 形式 / 書式 | a file or data format: 形式; text formatting: 書式 |
| linked / common | リンクされた / 共通 | |
| flags (class flags) | フラグ | language flags: 国旗 |
| teaser | ティーザー | |
| slug | スラッグ | |
| placeholder | プレースホルダー | |
| preset | プリセット | |
| information collection / information collector | 情報収集 / 情報収集の属性 | one submission of a form: 収集データ (counter 件); Collected information: 収集された情報 |
| overview / summary | 概要 / 要約 | |
| description | 説明 | |
| tab | タブ | |
| rich text | リッチテキスト | |
| feed | フィード | |
| session | セッション | |
| assign / assignment | 割り当て / 割り当て | |
| scope | スコープ | |
| thread / chain | スレッド / チェーン | conversation thread too |
| batch | バッチ | |
| schema / registry / manifest | スキーマ / レジストリ / マニフェスト | |
| stylesheet / theme / icon | スタイルシート / テーマ / アイコン | |
| port / digest / profile | ポート / ダイジェスト / プロファイル | |
| core (Solr) / lane (queue) | コア / レーン | |
| editor (person) / editor (software) | 編集者 / エディター | template editor: テンプレートエディター |
| Editor (siteaccess type) | 編集用 | |
| user interface | ユーザーインターフェイス | |
| page / link | ページ / リンク | |
| title / name | タイトル / 名前 | |
| date / time | 日付 / 時刻 | |
| status | ステータス | object state: 状態 |
| type | 種類 | data type: データ型 |
| group / list | グループ / 一覧 | |
| value / field | 値 / フィールド | |
| number / count | 番号 / 数 | |
| width / height / size | 幅 / 高さ / サイズ | |
| server / client | サーバー / クライアント | |
| request / response | リクエスト / レスポンス | |
| task | タスク | |
| details | 詳細 | |
| debug | デバッグ | |
| archive (archived version, object, order) | アーカイブ済み | |
| Archive / Unarchive (button) | アーカイブ / アーカイブ解除 | |
| archive (place, menu item) | アーカイブ | |
| archive (file .tar/.zip/.phar) | アーカイブファイル | |
| run (noun) | 実行 | |
| rotation / rotate | ローテーション / ローテーションする | |
| pattern / repository / maintainer | パターン / リポジトリ / メンテナー | |
| install / uninstall / installation | インストール / アンインストール / インストール | |
| checksum / quarantine | チェックサム / 隔離 | |
| pool / mapping / resolver / checkpoint | プール / マッピング / リゾルバー / チェックポイント | |
| index file | インデックスファイル | |
| resolve | 解決 | |
| child documents (Solr) | 子ドキュメント | |
| script / plugin / kernel | スクリプト / プラグイン / カーネル | |
| revoke | 取り消し | a key, token, session or access |
| label | ラベル | |
| confirmation questions ("Delete …?", "Cancel this …?") | ～してもよろしいですか？ | |

## 5. Decisions after the translation

| English | Japanese | Distinction |
|---|---|---|
| destination (URL alias, wildcard) | 転送先 | the target of a redirect or forward; a place in the tree or an import target: 配置先 |
| visibility (node) / invisible (node column) | 表示状態 / 不可視 | hidden: 非表示; "invisible elements" in the rich text editor: 非表示要素 |
| always available / availability | 常に利用可能 / 可用性 | 可用性 only for systems and services; "available" in sentences: 利用できます / 使用できます |
| poll / vote | アンケート / 投票 | Back to poll: アンケートに戻る |
| two-step sign-in / two-factor authentication | 2 段階認証 / 2 要素認証 | |
| credentials | 資格情報 | not 認証情報 |
| secret, secrets (settings, secret store) | シークレット | secret value: シークレットの値; not 秘密 |
| masked / unmasked; redact / redacted / unredacted | マスク済み / マスクなし; マスク / マスク済み / マスクなし | |
| activate / activation / unactivated users | 有効化 / 有効化 / 未有効化のユーザー | deactivate / reactivate: 無効化 / 再有効化; not アクティブ化; "active" stays アクティブ |
| warm (caches) / preload | ウォームアップ / プリロード | |
| template operator / operator (template language) | テンプレート演算子 / 演算子 | attribute operator: 属性演算子; not オペレーター |
| operator (a person running the system) | オペレーター | Stopped by operator: オペレーターによって停止されました |
| finding / findings | 検出結果 | not 検出事項; results of a search or run: 結果 |
| health / health check | 正常性 / 正常性チェック | not 健全性, not ヘルス |
| purge / purged | 完全削除 / 完全削除済み | Remove for good: 完全に削除; not パージ |
| True / False (setting values) | はい / いいえ | code literals true / false stay in Latin letters |
| Any (policy limitation value) | 指定なし | |
| top / bottom (position in an order) | 先頭 / 末尾 | a position on the page or in a feed: 上 / 下 |
| cronjob part | cron ジョブのパート | standalone: パート; not パーツ |
| menu arrangement (menu.ini) | メニュー構成 | |
| Restart (wizard) / restart (server, process) | 最初からやり直し / 再起動 | |
| Generate once / Generate on the fly (PDF export) | 1 回生成 / その都度生成 | |
| budget (cost) / disk budget (log viewer) | 予算 / 容量枠 | a time budget per step: 時間の上限 |
| live (run, schedule) / live (files, probe of a running system) | リアルタイム / ライブ | |
| probe | プローブ | live probe: ライブプローブ; probe results: プローブ結果 |
| staged / staged review | ステージング / ステージングレビュー | staged changes: ステージングされた変更 |
| dead letter / requeue / lease / retention | デッドレター / キューに戻す / リース / 保持期間 | |
| extension point | 拡張ポイント | survey of extension points: 拡張ポイントの調査 |
| extends / implements (PHP) | 継承 / 実装 | 「eZDataType を継承」 |
| fetch / fetch function / fetch alias | フェッチ / フェッチ関数 / フェッチエイリアス | |
| persistent object / persistent class | 永続オブジェクト / 永続オブジェクトクラス | |
| drift check | 差分チェック | |
| hits / misses / hit rate / evictions | ヒット / ミス / ヒット率 / 追い出し | |
| statement (SQL) | ステートメント | |
| maintenance mode / maintenance window | メンテナンスモード / メンテナンス期間 | maintaining software over its life cycle: 保守 |
| Switch on / Switch off; Tick all / Tick none | オン / オフ; すべてオン / すべてオフ | |
| Pause / Resume / Skipped | 一時停止 / 再開 / スキップ | |
| broken link / link check | リンク切れ / リンクチェック | |
| breadcrumb | 階層リンク | |
| Development / Test / Staging / Production (environment) | 開発 / テスト / ステージング / 本番 | short forms DEV, TEST, STAGE, PROD stay |
| Dismiss | 閉じる | |
| contributors / third-party software | 貢献者 / サードパーティ製ソフトウェア | package roles: Contributor 協力者, Lead リーダー, Developer 開発者 |
| navigation part | ナビゲーションパート | |
| orphan files / asset / reference index / node picker | 孤立ファイル / アセット / 参照インデックス / ノードピッカー | |
| origin (of a setting value) | 設定元 | |
| dead entries (OER import) / stale leftover | 無効なエントリ / 古い残骸 | |
| tutor / pupil / lesson / passage / reranker | チューター / 生徒 / レッスン / パッセージ / リランカー | |
| hosting provider, webhost | ホスティング事業者 | |
| Your order (bookmark sort) | ユーザー指定の順序 | |
