CssEX
==========

Ever get tired of having to write ever expanding CSS selectors? Especially when everything
else in software development uses the idea of scoping? Ever wanted to use variables? This will allow you to write CSS in a more readable
fashion then translate it to valid CSS.

Example:
(Before)
```css
$red = #F00;
#container {
		color: $red;
		.subClass {
			color: #00F;
		}
		> p {
			color: #0F0;
			:hover {
				color: $red;
			}
		}
}
```
(After)
```css
#container { color: #F00; }
#container .subClass { color: #00F; }
#container > p { color: #0F0; }
#container > p:hover { color: #F00; }
```