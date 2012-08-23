<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>CSS Scope</title>
		<?php
		include "css-scoper.php";
		$cssScoper = new CSS_Scoper("template.css");
		$file = fopen("style.css", "w");
		fwrite($file, $cssScoper->getCSS());
		fclose($file);
		?>
		
		<link href="http://alexgorbatchev.com/pub/sh/current/styles/shThemeRDark.css" rel="stylesheet" type="text/css" />
		<script src="http://alexgorbatchev.com/pub/sh/current/scripts/shCore.js" type="text/javascript"></script>
		<script src="http://alexgorbatchev.com/pub/sh/current/scripts/shAutoloader.js" type="text/javascript"></script>
		<script src="http://alexgorbatchev.com/pub/sh/current/scripts/shBrushXml.js" type="text/javascript"></script>
		<script src="http://alexgorbatchev.com/pub/sh/current/scripts/shBrushCss.js" type="text/javascript"></script>
		<script src="http://alexgorbatchev.com/pub/sh/current/scripts/shBrushJScript.js" type="text/javascript"></script>
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="style.css" />
	</head>
	<body>
		<div id="container">
			<div id="header">
				<ul>
					<li>
						<a href="javascript:void(0)">Home</a>
					</li>
					<li>
						<a href="javascript:void(0)">Services</a>
					</li>
					<li>
						<a href="javascript:void(0)">About</a>
					</li>
					<li>
						<a href="javascript:void(0)">Contact</a>
					</li>
				</ul>
			</div>
			
			<div id="content">
				<h1>CSS Scoping</h1>
				<span>You know how it goes. You end up with some extremely specific CSS and that selector just
				   keeps getting longer and longer. Worse still is that you have to rewrite the starting
				   of that selector over and over as you style the different children. 
				   <br />
				   <br />
				   Why do we have to do
				   this when all other programming makes use of indentation for "scoping"?
				</span>
				<span>
					That's where this comes in.
					<ul>
						<li>Write a selector once</li>
						<li>View child stylings in a logical layout</li>
						<li>Create shorter (development) CSS files</li>
						<li>Produce your same valid CSS</li>
					</ul>
				</span>
				
				<div class="example">
					<p>Say that you've got a typical navigation bar:</p>
					
					<span>HTML</span>
					<script type="syntaxhighlighter" class="brush: js; html-script: true"><![CDATA[
						<ul>
							<li class="selected">
								<a href="./">Home</a>
							</li>
							<li>
								<a href="important.htm">The Reason You Came Here</a>
							</li>
							<li>
								<a href="contact.htm">Our Address so You Can Get Directions From Google Maps</a>
							</li>
						</ul>
					]]></script>
					
					<span>CSS</span>
					<pre class="brush: css">
						ul { list-style: none; }
						ul > li { color: #FFF; background-color: #333; float: left; width: 33%; }
						ul > li:first-of-type { background-color: #444; }
						ul > li:last-of-type { background-color: #222; }
						ul > li .selected { color: #000; background-color: #DDD; }
					</pre>
					
					<p>Obviously you could write this CSS in a more concise manner but it still seems
					   redundant. Let's try something else.</p>
					   
					<div class="new-format">
						<pre class="brush: css">
							ul {
								list-style: none;
								
								> li {
									color: #FFF;
									background-color: #222;
									float: left;
									width: 33%;
									
									:first-of-type { background-color: #444; }
									:last-of-type { background-color: #222; }
									.selected { color: #000; background-color: #DDD; }
								}
							}
						</pre>
					</div>
				</div>
				
				<div class="example">
					<p>What about those times when you've got to use the same color in multiple places
					   and then you decide you want to do a different pallete?
					</p>
					
					<span>HTML</span>
					<script type="syntaxhighlighter" class="brush: js; html-script: true"><![CDATA[
						<h1>A Red Header</h1>
						<p>A Red Paragraph</p>
						<span>A Red Span</span>
					]]></script>
					
					<span>CSS</span>
					<pre class="brush: css">
						h1 { color: #F00; }
						p { color: #F00; }
						span { color: #F00; }
					</pre>
					
					<p>Rather than have to dig through and find all the cases of red that need changed,
					   while leaving the stuff that should be red alone, we can just change one variable.
					</p>
					
					<div class="new-format">
						<pre class="brush: css">
							$themeColor = #F00;
							
							h1 { color: $themeColor; }
							p { color: $themeColor; }
							span { color: $themeColor; }
						</pre>
					</div>
				</div>
				
				<div class="example">
					<p>Just for kicks, here's the CSS file I wrote for this page.</p>
					
					<span>Before</span>
					<pre class="brush: css"><?=file_get_contents("template.css")?></pre>
					
					<span>After</span>
					<pre class="brush: css"><?=file_get_contents("style.css")?></pre>
				</div>
			</div>
		</div>
		
		<script type="text/javascript">
			SyntaxHighlighter.all();
		</script>
	</body>
</html>