<? header("Content-type: text/css"); 
$pageBackground = '#fff';
$pageNega = '#000';
$textBackground = '#f5f5f6';
$textColor = '#5e5e60';
$brighterTextColor = '#AAA';
$textColorV = '#00a5e5';
$textColorA = '#9b9c9e';
$roundCorner = '4px'; 
$block2u = '24px';
$containerWidth = '1024';
$bgHoverColor = '#EEE';
$fontFamily = 'Arial, Tahoma, Verdana, sans-serif';
$ardBackground = '#00112E';
$headlineColor = '#00112E';

//#00112E ARD Farbe

if(false): //just for syntax highlighting in editor (never executed) ?><style type="text/css"><? endif; ?>

@charset "utf-8";
html,body,div,span,img,ul,li,fieldset,button,table{margin:0;padding:0;border:0;outline:0;font-size:100%;background:transparent}

/** General Style Info **/
body {
	background-color: #FFF;
	background-position:50% 0px;
	background-repeat:no-repeat;
	color: #333;
	font-family:Georgia, "Times New Roman", Times, serif;
	font-size:100%;
	margin: 0;
}
a {
	color:#09F;
	text-decoration: none;
	cursor:pointer;
}
a:hover {
	color: #06F;
	text-decoration:none;
}
a img {
	border:none;
}
h1, h2, h3, h4 {
	font-weight: normal;
	color:<?=$headlineColor?>;
}
h1 {
	color: #333;
	/*color:#A07085;*/
	font-style:italic;
}
h1 span{
	float:left;
	width:32px;
	height:32px;
	background:url(../img/wave_left.png) no-repeat;
	margin-right:5px;
}
h1 i{
	position:absolute;
	width:32px;
	height:32px;
	background:url(../img/wave_right.png) no-repeat;
	margin-left:5px;
}
h2 {

}
h3 {
	font-weight:normal;
}
h4,.fliestext_rot_fett,.h4 {
	color: <?=$ardBackground?>;
	font-weight: normal;
}

hr{color:#666; background:#666; border:none; margin:10px 0; width:100%;}
ul, li {
	margin: 0 12px;
}
p {
	margin: 0 0 1em 0;
	padding:10px;
	color:#000;
	font-family:Georgia, "Times New Roman", Times, serif;
	/*text-shadow:1px 1px 1px #CCC;*/
}
p.pink {
	margin: 0 0 1em 0;
	font-size:18px;
	background:url('../img/white-bg.png') repeat;
	padding:10px;
	color:#b04;
	box-shadow:1px 1px 1px #666;
	/*text-shadow:1px 1px 1px #CCC;*/
}




/** Layout **/

#wrapper{
	color: #333;
	padding: 0px 0px 0 0px;
	background:url('../img/white-bg.png') repeat;
	min-height:450px;
	clear:none;
}
#content{
	color: #333;
	padding: 10px 20px 1em 20px;
	background:#EEE;
	margin:0;
	overflow:auto;
	float:left;
	clear:none;
	width:1240px;
}
#content .view, #content .view-percent{
	padding:10px;
	float:left;
	width:800px;
	background:#FFF;
	border:1px solid #CCC;
	overflow:auto;
}
#content .view-percent{
	width:100%;
}
#content .right{
	padding:0 10px;
	width:370px;
	float:right;
	right:10px;
}
#content .right .box{
	padding:10px;
	float:left;
	width:360px;
	background:#FFF;
	border:1px solid #CCC;
}

.container {

}

#header{
	padding:0px 0px;
	height:94px;
	position:relative;
	background:#00112E;
	margin-top:0px;
}
#header .topText{
	margin:5px;
}
#header .container{
	position:relative;
}
#header .white-left, #header .white-right{
	position:absolute;
	height:172px;
	background:url(../img/white-left-over.png) repeat-y;
	width:8px;
}
#header .white-top{
	position:absolute;
	height:10px;
	background:url(../img/white-top-over.png) repeat-x;
	width:100%;
}
#header .white-right{
	background:url(../img/white-right-over.png) repeat-y;
	right:0;
}
#logo{
	float:left; 
	width:240px; 
	height:49px; 
	font-family:mufont; 
	font-size:30px; 
	line-height:71px;
	margin-left:20px;
}
#logo a{color:#FFF !important}
#beisatz{
	margin-top:42px;
	margin-left:330px;
	width:550px;
}
#header h1 {
	line-height:20px;
	background: #003d4c url('../img/cake.icon.png') no-repeat left;
	color: #fff;
	padding: 0px 30px;
}
#header h1 a {
	color: #fff;
	background: #003d4c;
	font-weight: normal;
	text-decoration: none;
}
#header h1 a:hover {
	color: #fff;
	background: #003d4c;
	text-decoration: underline;
}
#navbar{
	bottom:0;
	line-height:40px;
	height:40px;
	width:100%;
	border-bottom:1px solid #FEF;
	position:absolute;
	/*box-shadow:0px 1px 2px #555;*/
}
#navbar span{
	position:relative; 
	padding:0 20px;
	display:inline;
	float:left;
}
#navbar span ul{
	position:absolute;
	display:none;
	top:40px;
	width:100%;
	left:-10px;
	z-index:100;
	background:<?=$ardBackground?>;
}
#navbar span:hover ul{display:block;}
#navbar span ul li{display:block; width:100%; float:left; margin:0}
#navbar span ul li a{border-top:1px solid #FFF; font-size:16px; width:90%; margin:0 5%; float:left; text-align:center}
#navbar a{
	color:#09F;
	font-size:19px;
}
#navbar a:hover{
	color:#0CF;
}
#navbar img{
	vertical-align:-1px;
}

#footer {
	clear: both;
	padding: 0px 0px;
	text-align: right;
	height:108px;
	position:relative;
}
#footer .white-left, #footer .white-right{
	position:absolute;
	height:108px;
	background:url(../img/white-left-over.png) repeat-y;
	width:8px;
	left:0;
}
#footer .white-right{
	background:url(../img/white-right-over.png) repeat-y;
	right:0;
	left:auto;
}
#footer-bar{
	font-size:16px;
	float:right;
	margin-top:23px;
	height:20px;
}
#footer .container{min-height:0}
#footer-bar a{
	color:#FFF;
	margin:0 20px;
}
#footer-bar img{
	vertical-align:-5px;
}

.dialog
{
  background:<?=$pageBackground?>;
  display:none;
  opacity:1;
  padding:10px 5px 10px 10px !important;
  position:absolute;
  z-index:1001;
  border:1px solid #CCC;
  box-shadow:3px 3px 3px #666;
  overflow:hidden;
}
.dialog-title-bar{
	float:left;
	width:100%;
}
.dialog-title{margin-left:0}
#flashMessage .dialog-title{margin-left:8px}
.close-dialog{
	/*position:absolute;*/
	float:right;
	margin-right:3px;
	margin-top:-5px;
	font-size:20px;
}
.close-dialog:hover{color:#F00}

/*.dialog
{
  background:<?=$textColor?>;
  display:none;
  height:100%;
  width:512px;
}*/
.ui-dialog{
	background:<?=$textColor?> !important;
}
.ui-dialog-titlebar{
	padding: 0px !important;
    right: 0px;
    left:0;
    width: 100% !important;
    z-index: 10;
}

/** containers **/
div.form,
div.index,
div.view {
	padding:10px 2%;
}
div.actions {
	padding:10px 1.5%;
}
div.actions h3 {
	padding-top:0;
	color:#777;
}


/** Tables **/
table {
	border-right:0;
	clear: both;
	color: #333;
	margin-bottom: 10px;
	width: 100%;
}
th {
	border:0;
	border-bottom:2px solid #555;
	text-align: left;
	padding:4px;
}
th a {
	display: block;
	padding: 2px 4px;
	text-decoration: none;
}
th a.asc:after {
	content: ' ⇣';
}
th a.desc:after {
	content: ' ⇡';
}
table tr td {
	padding: 6px;
	text-align: left;
	vertical-align: top;
	border-bottom:1px solid #ddd;
}
table tr{
	background:#EEE;
}
table tr:nth-child(even) {
	background: #f9f9f9;
}
td.actions {
	text-align: center;
	white-space: nowrap;
}
table td.actions a {
	margin: 0px 6px;
	padding:2px 5px;
}

/* SQL log */
.cake-sql-log {
	background: #fff;
}
.cake-sql-log td {
	padding: 4px 8px;
	text-align: left;
	font-family: Monaco, Consolas, "Courier New", monospaced;
}
.cake-sql-log caption {
	color:#fff;
}

/** Paging **/
.paging {
	background:#fff;
	color: #ccc;
	margin-top: 1em;
	clear:both;
}
.paging .current,
.paging .disabled,
.paging a {
	text-decoration: none;
	padding: 5px 8px;
	display: inline-block
}
.paging > span {
	display: inline-block;
	border: 1px solid #ccc;
	border-left: 0;
}
.paging > span:hover {
	background: #efefef;
}
.paging .prev {
	border-left: 1px solid #ccc;
	-moz-border-radius: 4px 0 0 4px;
	-webkit-border-radius: 4px 0 0 4px;
	border-radius: 4px 0 0 4px;
}
.paging .next {
	-moz-border-radius: 0 4px 4px 0;
	-webkit-border-radius: 0 4px 4px 0;
	border-radius: 0 4px 4px 0;
}
.paging .disabled {
	color: #ddd;
}
.paging .disabled:hover {
	background: transparent;
}
.paging .current {
	background: #efefef;
	color: #c73e14;
}

/** Scaffold View **/
dl {
	line-height: 2em;
	margin: 0em 0em;
	width: 60%;
}
dl dd:nth-child(4n+2),
dl dt:nth-child(4n+1) {
	background: #f4f4f4;
}

dt {
	font-weight: bold;
	padding-left: 4px;
	vertical-align: top;
	width: 10em;
}
dd {
	margin-left: 10em;
	margin-top: -2em;
	vertical-align: top;
}

/** Forms **/
form {
	clear: both;
	margin-right: 20px;
	padding: 0;
	width: 100%;
}
fieldset {
	border: none;
	margin-bottom: 1em;
	padding: 16px 0 16px 10px;
}
fieldset legend {
	color: #09F;
	font-size: 160%;
	font-weight: bold;
}
fieldset fieldset {
	margin-top: 0;
	padding: 10px 0 0;
}
fieldset fieldset legend {
	font-size: 120%;
	font-weight: normal;
}
fieldset fieldset div {
	clear: left;
	margin: 0 20px;
}
form div {
	clear: both;
	margin-bottom: 0;
	padding: .5em 0 .5em .5em;
	vertical-align: text-top;
}
form.noclear div{
	clear:none;
	padding:0;
}
form .input {
	color: #444;
}
form .required {
	font-weight: bold;
}
form .required label:after {
	color: #e32;
	content: '*';
	display:inline;
}
form div.submit {
	border: 0;
	clear: both;
	margin-top: 10px;
}
label {
	display: block;
	font-size: 110%;
	margin-bottom:3px;
}
input, textarea {
	clear: both;
	font-size: 100%;
	font-family: "frutiger linotype", "lucida grande", "verdana", sans-serif;
	padding: 1%;
	width:98%;
}
select {
	clear: both;
	font-size: 100%;
}
select[multiple=multiple] {
	width: 100%;
}
option {
	font-size: 120%;
	padding: 0 3px;
}
input[type=checkbox] {
	clear: left;
	float: left;
	margin: 2px 6px 7px 2px;
	width: auto;
}
div.checkbox label {
	display: inline;
}
input[type=radio] {
	float:left;
	width:auto;
	margin: 3px 3px 3px 0;
	padding: 0;
	line-height: 26px;
}
.radio label {
	margin: 0 0 6px 20px;
	line-height: 26px;
}
input[type=submit] {
	display: inline;
	font-size: 110%;
	width: auto;
}
form .submit input[type=submit] {
	background:#444;
	background-image: -webkit-gradient(linear, left top, left bottom, from(#444), to(#333));
	background-image: -webkit-linear-gradient(top, #444, #333);
	background-image: -moz-linear-gradient(top, #444, #333);
	border-color: #333;
	color: #fff;
	text-shadow: rgba(0, 0, 0, 0.5) 0px -1px 0px;
	padding: 8px 10px;
}
form .submit input[type=submit]:hover {
	background: #222;
}
/* Form errors */
form .error {
	background: #FFDACC;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	font-weight: normal;
}
form .error-message {
	-moz-border-radius: none;
	-webkit-border-radius: none;
	border-radius: none;
	border: none;
	background: none;
	margin: 0;
	padding-left: 4px;
	padding-right: 0;
}
form .error,
form .error-message {
	color: #9E2424;
	-webkit-box-shadow: none;
	-moz-box-shadow: none;
	-ms-box-shadow: none;
	-o-box-shadow: none;
	box-shadow: none;
	text-shadow: none;
}

/** Notices and Errors **/
.message {
	clear: both;
	color: #fff;
	font-size: 140%;
	font-weight: bold;
	margin: 0 0 1em 0;
	padding: 5px;
}

.success,
.message,
.cake-error,
.cake-debug,
.notice,
p.error,
.error-message {
	background: #ffcc00;
	background-repeat: repeat-x;
	background-image: -moz-linear-gradient(top, #ffcc00, #E6B800);
	background-image: -ms-linear-gradient(top, #ffcc00, #E6B800);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#ffcc00), to(#E6B800));
	background-image: -webkit-linear-gradient(top, #ffcc00, #E6B800);
	background-image: -o-linear-gradient(top, #ffcc00, #E6B800);
	background-image: linear-gradient(top, #ffcc00, #E6B800);
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
	border: 1px solid rgba(0, 0, 0, 0.2);
	margin-bottom: 18px;
	padding: 7px 14px;
	color: #404040;
	text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
	-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25);
}
.success,
.message,
.cake-error,
p.error,
.error-message {
	clear: both;
	color: #fff;
	background: #c43c35;
	border: 1px solid rgba(0, 0, 0, 0.5);
	background-repeat: repeat-x;
	background-image: -moz-linear-gradient(top, #ee5f5b, #c43c35);
	background-image: -ms-linear-gradient(top, #ee5f5b, #c43c35);
	background-image: -webkit-gradient(linear, left top, left bottom, from(#ee5f5b), to(#c43c35));
	background-image: -webkit-linear-gradient(top, #ee5f5b, #c43c35);
	background-image: -o-linear-gradient(top, #ee5f5b, #c43c35);
	background-image: linear-gradient(top, #ee5f5b, #c43c35);
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
}
.success {
	clear: both;
	color: #fff;
	border: 1px solid rgba(0, 0, 0, 0.5);
	background: #3B8230;
	background-repeat: repeat-x;
	background-image: -webkit-gradient(linear, left top, left bottom, from(#76BF6B), to(#3B8230));
	background-image: -webkit-linear-gradient(top, #76BF6B, #3B8230);
	background-image: -moz-linear-gradient(top, #76BF6B, #3B8230);
	background-image: -ms-linear-gradient(top, #76BF6B, #3B8230);
	background-image: -o-linear-gradient(top, #76BF6B, #3B8230);
	background-image: linear-gradient(top, #76BF6B, #3B8230);
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
}
p.error {
	font-family: Monaco, Consolas, Courier, monospace;
	font-size: 120%;
	padding: 0.8em;
	margin: 1em 0;
}
p.error em {
	font-weight: normal;
	line-height: 140%;
}
.notice {
	color: #000;
	display: block;
	font-size: 120%;
	padding: 0.8em;
	margin: 1em 0;
}
.success {
	color: #fff;
}

/**  Actions  **/
.actions ul {
	margin: 0;
	padding: 0;
}
.actions li {
	margin:0 0 0.5em 0;
	list-style-type: none;
	white-space: nowrap;
	padding: 0;
}
.actions ul li a {
	font-weight: normal;
	display: block;
	clear: both;
}

/* Buttons and button links */
.button{
	background:url('../img/btn.png') repeat;
	border:1px solid #FF5B66;
	outline:1px solid #FF202F;
	color:#FFF;
	padding:3px 7px;
	font-size:16px;
}
.button:hover{
	color:#FFF;
}
input[type=submit],
.actions ul li a,
.actions a {
	font-weight:normal;
	padding: 4px 8px;
	background: #333;
	background-image: -webkit-gradient(linear, left top, left bottom, from(#444), to(#333));
	background-image: -webkit-linear-gradient(top, #444, #333);
	background-image: -moz-linear-gradient(top, #444, #333);
	background-image: -ms-linear-gradient(top, #444, #333);
	background-image: -o-linear-gradient(top, #444, #333);
	background-image: linear-gradient(top, #444, #333);
	color:#999;
	border:1px solid #bbb;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	text-decoration: none;
	min-width: 0;
	-moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0px 1px 1px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0px 1px 1px rgba(0, 0, 0, 0.2);
	box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0px 1px 1px rgba(0, 0, 0, 0.2);
	-webkit-user-select: none;
	user-select: none;
}
.actions ul li a:hover,
.actions a:hover {
	background: #ededed;
	border-color: #acacac;
	text-decoration: none;
}
input[type=submit]:active,
.actions ul li a:active,
.actions a:active {
	background: #eee;
	background-image: -webkit-gradient(linear, left top, left bottom, from(#dfdfdf), to(#eee));
	background-image: -webkit-linear-gradient(top, #dfdfdf, #eee);
	background-image: -moz-linear-gradient(top, #dfdfdf, #eee);
	background-image: -ms-linear-gradient(top, #dfdfdf, #eee);
	background-image: -o-linear-gradient(top, #dfdfdf, #eee);
	background-image: linear-gradient(top, #dfdfdf, #eee);
	text-shadow: #eee 0px 1px 0px;
	-moz-box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.3);
	box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.3);
	border-color: #aaa;
	text-decoration: none;
}

/** Related **/
.related {
	clear: both;
	display: block;
}

/** Debugging **/
pre {
	color: #000;
	background: #f0f0f0;
	padding: 15px;
	-moz-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
	box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
	overflow:auto;
}
.cake-debug-output {
	padding: 0;
	position: relative;
}
.cake-debug-output > span {
	position: absolute;
	top: 5px;
	right: 5px;
	background: rgba(255, 255, 255, 0.3);
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	padding: 5px 6px;
	color: #000;
	display: block;
	float: left;
	-moz-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.25), 0 1px 0 rgba(255, 255, 255, 0.5);
	-webkit-box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.25), 0 1px 0 rgba(255, 255, 255, 0.5);
	box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.25), 0 1px 0 rgba(255, 255, 255, 0.5);
	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.8);
}
.cake-debug,
.cake-error {
	font-size: 16px;
	line-height: 20px;
	clear: both;
}
.cake-error > a {
	text-shadow: none;
}
.cake-error {
	white-space: normal;
}
.cake-stack-trace {
	background: rgba(255, 255, 255, 0.7);
	color: #333;
	margin: 10px 0 5px 0;
	padding: 10px 10px 0 10px;
	font-size: 120%;
	line-height: 140%;
	overflow: auto;
	position: relative;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	border-radius: 4px;
}
.cake-stack-trace a {
	text-shadow: none;
	background: rgba(255, 255, 255, 0.7);
	padding: 5px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	border-radius: 10px;
	margin: 0px 4px 10px 2px;
	font-family: sans-serif;
	font-size: 14px;
	line-height: 14px;
	display: inline-block;
	text-decoration: none;
	-moz-box-shadow: inset 0px 1px 0 rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: inset 0px 1px 0 rgba(0, 0, 0, 0.3);
	box-shadow: inset 0px 1px 0 rgba(0, 0, 0, 0.3);
}
.cake-code-dump pre {
	position: relative;
	overflow: auto;
}
.cake-context {
	margin-bottom: 10px;
}
.cake-stack-trace pre {
	color: #000;
	background-color: #F0F0F0;
	margin: 0px 0 10px 0;
	padding: 1em;
	overflow: auto;
	text-shadow: none;
}
.cake-stack-trace li {
	padding: 10px 5px 0px;
	margin: 0 0 4px 0;
	font-family: monospace;
	border: 1px solid #bbb;
	-moz-border-radius: 4px;
	-wekbkit-border-radius: 4px;
	border-radius: 4px;
	background: #333;
	background-image: -webkit-gradient(linear, left top, left bottom, from(#444), to(#333));
	background-image: -webkit-linear-gradient(top, #444, #333);
	background-image: -moz-linear-gradient(top, #444, #333);
	background-image: -ms-linear-gradient(top, #444, #333);
	background-image: -o-linear-gradient(top, #444, #333);
	background-image: linear-gradient(top, #444, #333);
}
/* excerpt */
.cake-code-dump pre,
.cake-code-dump pre code {
	clear: both;
	font-size: 12px;
	line-height: 15px;
	margin: 4px 2px;
	padding: 4px;
	overflow: auto;
}
.cake-code-dump .code-highlight {
	display: block;
	background-color: rgba(255, 255, 0, 0.5);
}
.code-coverage-results div.code-line {
	padding-left:5px;
	display:block;
	margin-left:10px;
}
.code-coverage-results div.uncovered span.content {
	background:#ecc;
}
.code-coverage-results div.covered span.content {
	background:#cec;
}
.code-coverage-results div.ignored span.content {
	color:#aaa;
}
.code-coverage-results span.line-num {
	color:#666;
	display:block;
	float:left;
	width:20px;
	text-align:right;
	margin-right:5px;
}
.code-coverage-results span.line-num strong {
	color:#666;
}
.code-coverage-results div.start {
	border:1px solid #aaa;
	border-width:1px 1px 0px 1px;
	margin-top:30px;
	padding-top:5px;
}
.code-coverage-results div.end {
	border:1px solid #aaa;
	border-width:0px 1px 1px 1px;
	margin-bottom:30px;
	padding-bottom:5px;
}
.code-coverage-results div.realstart {
	margin-top:0px;
}
.code-coverage-results p.note {
	color:#bbb;
	padding:5px;
	margin:5px 0 10px;
	font-size:10px;
}
.code-coverage-results span.result-bad {
	color: #a00;
}
.code-coverage-results span.result-ok {
	color: #fa0;
}
.code-coverage-results span.result-good {
	color: #0a0;
}

/** Elements **/
#url-rewriting-warning {
	display:none;
}

.sub-links{
	text-align:center;
	width:100%;
}
.sub-links a{
	display:inline-block;
	font-size:20px;
	margin:-20px 20px;
	vertical-align:text-top;
}
.sub-links span{
	background:url('../img/vseperator.fw.png') no-repeat;
	width:1px;
	height:37px;
	margin:-20px 20px;
	display:inline-block;
	margin:0 20px;
	list-style:none;
}

.floatL{float:left;}.floatLi{float:left !important}
.floatR{float:right;}.floatRi{float:right !important}
.floatN{float:none;}.floatNi{float:none !important}
.hidden{display:none;}
.s-font{font-size:10px;}.m-font{font-size:14px}
.cien{width:100%}
.cienL{float:left; width:100%}
div.text, p{font-size:16px}
.cienLNoClear{clear:none; float:left; width:100%}
.clear{clear:both;}
.noclear{clear:none}
.no-clear{clear:none}
.stick-left{margin-left:0; padding-left:0}
.no-bg{background:none !important;}
.white-bg{background:#FFF; padding:15px;} .white{color:#FFF}
.m-top-0{margin-top:0}.m-top-5{margin-top:5px}.m-top-10{margin-top:10px}.m-top-15{margin-top:15px}
.m-bottom-0{margin-bottom:0}.m-bottom-5{margin-bottom:5px}.m-bottom-10{margin-bottom:10px}.m-bottom-15{margin-bottom:15px}
.m-left-0{margin-left:0}.m-left-5{margin-left:5px}.m-left-10{margin-left:10px}.m-left-15{margin-left:15px}
.m-right-0{margin-right:0}.m-right-5{margin-right:5px}.m-right-10{margin-right:10px}.m-right-15{margin-right:15px}
.raute{float:left; margin-right:5px; margin-top:3px; width:10px; height:18px; background:url(../img/raute3.png) no-repeat;}
.pink{color:#F36}
.out-border{border:3px solid #FFF; outline:1px solid #F9C;}
.ul{/*list-style-image:url(../img/raute3.png);*/}
.img200{border:1px solid #B04; float:left;}
img.floatL,img.floatR{margin-top:4px;}
.justify{text-align:justify}
ul.list{margin:0; padding:0; font-family:Verdana, Geneva, sans-serif}
ul.list li{display:block; background:#FFF; margin:0; padding:0 0 5px; border-bottom:1px solid #CCC}
ul.list li h4{background:#FFF; padding:2px 5px; cursor:pointer; margin-top:0; margin-bottom:5px;}
ul.list li h4 span{font-size:16px; margin-top:0px;}
ul.list li h4 em{font-size:16px; font-style:normal;}
ul.list li div{margin-left:5px;}
.nopad{padding:0 !important;}
input.date{width:150px;}
div.list-item{
	padding:5px 7px;
	background:#EEE;
	border:1px solid #CCC;
	border-width:0 1px 1px 1px;
}
.circle {
    border-radius: 30px;
    color: #FFFFFF !important;
    float: left;
    height: 15px;
    padding: 0 8px 6px;
    width: 5px;
	font-size:16px;
}
.red-fill{background:#F00;}.yellow-fill{background:#FC0;}.gray-fill{background:#666;}
.red,.red > *{color:#F00 !important}

.texts{
	max-width:600px;
	font-family:Verdana, Geneva, sans-serif;
}


/***********Scrollbars*************************************/


.mainScrollbar,.mainScrollbarN,.mainScrollbarF
{
  background:<?=$pageBackground?>;
  display:block;
  float:left;
  height:100%;
  margin:0 0 0 16px;
  padding:0;
}

.mainScrollbarN
{
  margin-left:4px;
}

.mainScrollbarF{
	width:100%;
    margin:0;
}

.mainScrollbar .viewport
{
  height:100%;
  overflow:hidden;
  position:relative;
}

.mainScrollbar .overview
{
  height:auto;
  left:0;
  list-style:none;
  margin:0;
  padding:0;
  position:absolute;
  top:0;
}

.mainScrollbar .scrollbar
{
  background:<?=$textBackground?>;
  border-radius:8px;
  float:right;
  padding-bottom:3px;
  padding-top:3px;
  width:15px;
  height:100% !important;
}

.mainScrollbar .thumb
{
  background:<?=$textColorA?>;
  border-radius:8px;
  cursor:pointer;
  left:3px;
  overflow:hidden;
  position:absolute;
  top:0;
  width:9px;
}

.mainScrollbarN .viewport{
  overflow:hidden;
  position:relative;
  min-width:485px;
}

.mainScrollbarN .overview
{
  left:0;
  list-style:none;
  margin:0;
  padding:0;
  position:absolute;
  top:0;
  width:484px;
}

.mainScrollbarN .scrollbar
{
  background:<?=$textColorA?>;
  border-radius:8px;
  float:right;
  padding-bottom:3px;
  padding-top:3px;
  width:15px;
  height:100%;
}

.mainScrollbarN .thumb
{
  background:<?=$textBackground?>;
  border-radius:8px;
  cursor:pointer;
  left:3px;
  overflow:hidden;
  position:relative;
  top:0;
  width:9px;
}


.mainScrollbarF .viewport
{
  height:100%;
  overflow:hidden;
  position:relative;
  width:<?=$containerWidth-16?>px;
}

.mainScrollbarF .overview
{
  height:auto;
  left:0;
  list-style:none;
  margin:0;
  padding:0;
  position:absolute;
  top:0;
  width:<?=$containerWidth-16?>px;
}

.mainScrollbarF .scrollbar
{
  background:<?=$textBackground?>;
  border-radius:8px;
  float:right;
  padding-bottom:3px;
  padding-top:3px;
  width:15px;
  height:100% !important;
}
.mainScrollbarF .track{position:relative;}
.mainScrollbarF .thumb
{
  background:<?=$textColorA?>;
  border-radius:8px;
  cursor:pointer;
  left:3px;
  overflow:hidden;
  position:absolute;
  top:0;
  width:9px;
}


/***********LOGOS***********/
.ir {
    background-color: transparent;
    background-repeat: no-repeat;
    border: 0 none;
    direction: ltr;
    display: block;
    overflow: hidden;
    text-align: left;
    text-indent: -999em;
}
.sl28106 { /* Das Erste */
    background-position:0px 0px;
}

.sl28723 { /* EinsPlus */
    background-position:0px -35px;
}

.sl28722 { /* Einsfestival */
    background-position:0px -70px;
}

.sl28721 { /* EinsExtra */
    background-position:0px -105px;
}

.sl-28107 { /* Bayerisches FS */
    background-position:0px -140px;
}

.sl28108 { /* hr Fernsehen */
    background-position:0px -175px;
}

.sl-28229 { /* MDR Fernsehen */
    background-position:0px -210px;
}

.sl-28226 { /* NDR Fernsehen */
    background-position:0px -245px;
}

.sl28385 { /* Radio Bremen TV */
    background-position:0px -280px;
}

.sl-28205 { /* RBB Fernsehen */
    background-position:0px -315px;
}

.sl28486 { /* SR Fernsehen */
    background-position:0px -350px;
}

.sl28113 { /* SWR Fernsehen BW */
    background-position:0px -385px;
}

.sl28231 { /* SWR Fernsehen RP */
    background-position:0px -420px;
}

.sl-28111 { /* WDR Fernsehen */
    background-position:0px -455px;
}

.sl28487 { /* BR alpha */
    background-position:0px -490px;
}

.sl28007 { /* 3sat */
    background-position:0px -525px;
}

.sl28724 { /* arte */
    background-position:0px -560px;
}

.sl28008 { /* KIKA */
    background-position:0px -595px;
}

.sl28725 { /* Phoenix */
    background-position:0px -630px;
}
.sl-c115, .sl-l115, .sl-l140, .sl-r140 {
    background-image: url("../img/station_logos.png");
    height: 34px;
    width: 115px;
}
<? if(false): ?></style><? endif; ?>