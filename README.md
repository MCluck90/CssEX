CSS-Scoper
==========

Ever get tired of having to write ever expanding CSS selectors? Especially when everything
else in software development uses the idea of scoping? This will allow you to write CSS in a more readable
fashion then translate it to valid CSS.

Example:
(Before)
```css
#container {
		color: #F00;
		.subClass {
			color: #00F;
		}
		> p {
			color: #0F0;
			:hover: {
				color: #0FF;
			}
		}
}
```
(After)
```css
#container { color: #F00; }
#container .subClass { color: #00F; }
#container > p { color: #0F0; }
#container > p:hover { color: #0FF; }
```