# TgwsMark
テキストからHTMLにするアレ。自分のサイトで使ってる記法と同じやつ。Markdownにちょっと似てる。

## ひつよう🎃
PHP8.2以上。

## いんすとーる🪽
```
composer require mifumi323/tgwsmark
```

## つかいかた🐐
```php
$tgws_mark = <<<TGWSMARK
*み⭐だ⭐し
ほんぶん👣
TGWSMARK;
$html = \Mifumi323\TgwsMark\TgwsMark::toHtml($tgws_mark);
echo $html;
// 出力：<h2>み⭐だ⭐し</h2><p>ほんぶん👣</p>
```

## ぶんぽう🥒

### パラグラフ
```
こんにちわ💞

さよおなら💨
```
```html
<p>こんにちわ💞</p><p>さよおなら💨</p>
```
行頭から書き始めると、パラグラフになります。
空行で、パラグラフが分かれます。

#### パラグラフの間隔
```
まつだみふみが🖐️


おとどけしました🫲
```
```html
<p>まつだみふみが🖐️</p><p style="margin-top:2em">おとどけしました🫲</p>
```
空行を2つ以上挟むと、空行の数に応じてパラグラフの間隔が開きます。

### 改行
```
どかーん💥
ずどーん🎆
```
```html
<p>どかーん💥<br>ずどーん🎆</p>
```
空行を開けずに改行すると、brタグが挿入されます。

### 見出し
```
*えっちつう🪥
**えっちさん🩳
***えっちよん🚍
```
```html
<h2>えっちつう🪥</h2><h3>えっちさん🩳</h3><h4>えっちよん🚍</h4>
```
行頭に`*`をつけると、h2レベルの見出しになります。
つける`*`を増やすと、深い見出しになります。

#### 初期見出しレベルの変更
```php
$tgws_mark = <<<TGWSMARK
*えっちわん🪷
**えっちつう🪥
***えっちさん🩳
TGWSMARK;
$html = \Mifumi323\TgwsMark\TgwsMark::toHtml($tgws_mark, 'h1');
echo $html;
```
```html
<h1>えっちわん🪷</h1><h2>えっちつう🪥</h2><h3>えっちさん🩳</h3>
```
`TgwsMark::toHtml`関数を呼び出す際に、`$head`引数を指定することで、初期見出しレベルを変更することができます。

#### hn以外の見出し
```php
$tgws_mark = <<<TGWSMARK
*びよよーん➿
**びよよーん⚔️
***びよよよよーん😈
TGWSMARK;
$html = \Mifumi323\TgwsMark\TgwsMark::toHtml($tgws_mark, 'div');
echo $html;
```
```html
<div>びよよーん➿</div><div>*びよよーん⚔️</div><div>**びよよよよーん😈</div>
```
`$head`引数にhn以外のタグを指定すると、見出しレベルは無視されます。

#### 見出しの属性
```php
$tgws_mark = <<<TGWSMARK
*城ですな
**そうなの？
***多分ね
TGWSMARK;
$html = \Mifumi323\TgwsMark\TgwsMark::toHtml($tgws_mark, headattr: 'class="pkmn"');
echo $html;
```
```html
<h2 class="pkmn">城ですな</h2><h3 class="pkmn">そうなの？</h3><h4 class="pkmn">多分ね</h4>
```
`TgwsMark::toHtml`関数を呼び出す際に、`$headattr`引数を指定することで、属性を追加できます。
見出しレベルに関わらず、同じ属性が付きます。

#### 空見出し
```
*
あ😦
```
```html
<p>あ😦</p>
```
空見出しそれ自体に意味はありません。
後述する折り畳み記法に影響します。

#### URLフラグメント
```
*ここ🥥#here
```
```html
<h2 id="here">ここ🥥<a href="#here" class="hashlink" title="「ここ🥥」の位置へのリンク">#</a></h2>
```
空でない見出しの後に`#`に続けて識別子を書くと、URLフラグメントとリンクが生成されます。

### 順序付きリスト
```
+わん🧟
+つう🐶
```
```html
<ol><li>わん🧟</li><li>つう🐶</li></ol>
```
`+`を行頭に付けると、順序付きリストになります。

### 順序なしリスト
```
-ありゃりゃ🏛️
-こりゃりゃ🪱
```
```html
<ul><li>ありゃりゃ🏛️</li><li>こりゃりゃ🪱</li></ul>
```
`-`を行頭に付けると、順序なしリストになります。

### 表
```
|おなまえ📛|しょくぎょう🔧|h
|だでぃいな|こすもがーど|
```
```html
<table><thead><tr><th>おなまえ</th><th>しょくぎょう</th></tr></thead><tr><td>だでぃいな</td><td>こすもがーど</td></tr></table>
```
`|`で囲んで区切ると、表になります。
`|h`で終わる行は、表の見出し行になります。

#### セル単位の見出し
```
|*きかん|ごおくねん|h
|*ほうしゅう|ひゃくまんえん|
```
```html
<table><thead><tr><th>きかん</th><th>ごおくねん</th></tr></thead><tr><th>ほうしゅう</th><td>ひゃくまんえん</td></tr></table>
```
`*`で始まるセルは見出しになります。

#### セルの属性
```
|はこ|class="long">ながーい|
|もちこ|class="soft">ふわふわ|
```
```html
<table><tr><td>はこ</td><td class="long">ながーい</td></tr><tr><td>もちこ</td><td class="soft">ふわふわ</td></tr></table>
```
セル内の`>`までは属性として`td`/`th`タグに付与されます。

#### 見出しと属性の同時適用
```
|*class="hot">おゆ|りょうが|
|*class="cold">みず|ぴーちゃん|
```
```html
<table><tr><th class="hot">おゆ</th><td>りょうが</td></tr><tr><th class="cold">みず</th><td>ぴーちゃん</td></tr></table>
```
見出しと属性を同時に使う場合、`*`を先に書きます。

### 折り畳み
```
*>
み💛ひ💛つ💛の💛こ💛い
```
```html
<details><p>み💛ひ💛つ💛の💛こ💛い</p></details>
```
1個以上の`*`の後に`>`で、次の行から折りたたまれて隠れます。

#### 折り畳みと見出しレベル
```
**>
**ここかくれる🌚
***ここも🌑
*ここはみえる🌛
やったね🌝
```
```html
<details><h3>ここかくれる🌚</h3><h4>ここも🌑</h4></details><h2>ここはみえる🌛</h2><p>やったね🌝</p>
```
折り畳みと同じかより深い見出しは折り畳み範囲に入って隠れます。
折り畳みより浅い見出しがあると、そこで折り畳みが終了して、それ以降は隠れません。

#### 折り畳みと空見出し
```
**>
**
このりはくのめをもってしても🙈
*
ふつうにみえる🤗
```
```html
<details><p>このりはくのめをもってしても🙈</p></details><p>ふつうにみえる🤗</p>
```
折り畳み記法に対しては、空見出しは見出しタグを生成せずに、同じレベルの見出しと同じ働きをします。

#### 折り畳みの見出し
```
*>はんにんはやす🫧#yasu
ねたばれだあ🗿
```
```html
<details><summary id="yasu">はんにんはやす🫧<a href="#yasu" class="hashlink" title="「はんにんはやす🫧」の位置へのリンク">#</a></summary><p>ねたばれだあ🗿</p></details>
```
`>`の後に書いた文字列はsummary要素になります。
見出しと同じく、`#`でURLフラグメントも生成します。

### コードブロック
````
```
int main(void){}
```
````
```html
<pre><code>
int main(void){}
</code></pre>
```
3つ以上のバッククォートでコードブロックを表せます。
TgwsMark自体はハイライト機能を持ちませんが、[highlight.js](https://highlightjs.org/)でハイライトできるHTMLを出力します。

#### コードブロックの言語指定
````
```c
int main(void){}
```
````
```html
<pre><code class="language-c">
int main(void){}
</code></pre>
```
バッククォートの後に言語を書くことでハイライトのためのclassが付きます。

#### コードブロックのタイトル指定
````
```:main.c
int main(void){}
```
````
```html
<pre title="main.c"><code>
int main(void){}
</code></pre>
```
バッククォートの後にコロン「:」で区切ってタイトル(典型的にはファイル名)を書くとtitle属性に設定されます。
JavaScript等で取り出して表示するとよいでしょう。

#### コードブロックの言語とタイトルの同時指定
````
```c:main.c
int main(void){}
```
````
```html
<pre title="main.c"><code class="language-c">
int main(void){}
</code></pre>
```
言語とタイトルは同時に指定できます。
言語のほうを先に指定します。

#### コードブロックを含むコードブロック
`````
````markdown
ここにシー言語
```c
int main(void){}
```
ここまでシー言語
````
`````
````html
<pre><code class="language-markdown">
ここにシー言語
```c
int main(void){}
```
ここまでシー言語
</code></pre>
````
コードブロックの終了は、開始時に使ったのと同じ数のバッククォートで反映されます。
4つで始めれば4つのバッククォートまでになり、3つはコード内容とみなされます。

#### コードブロックを含むコードブロック(逆パターン)
`````
```markdown
ここにシー言語
````c
int main(void){}
````
ここまでシー言語
```
`````
`````html
<pre><code class="language-markdown">
ここにシー言語
````c
int main(void){}
````
ここまでシー言語
</code></pre>
`````
数が違えば入れ子にできるので、中身のバッククォートのほうが多くてもかまいません。

### 文法解釈のスキップ
```
`*い🥂
⚓<<*ろ
*は>>⛷️
```
```html
*い🥂⚓<h2>ろ</h2><h2>は</h2>⛷️
```
ややこしいことするんじゃねえ🤬
