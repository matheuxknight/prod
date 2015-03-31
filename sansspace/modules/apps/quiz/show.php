<?php

$this->pageTitle = "$object->name";
include '/sansspace/ui/lib/pageheader.php';

echo CHtml::scriptFile('/sansspace/ui/js/jquery.html5_upload.js');

$backcolor = param('appmainback');
$sessionid = session_id();

$container_top = '34px';
$ismobile = 0;

if(IsMobileEmbeded())
{
	$ismobile = 1;
	$container_top = '0px';
	
	$prot = '';
	if(preg_match('/android/i', $_SERVER['HTTP_USER_AGENT']))
		$prot = 'unknown:/';
}

echo <<<END
<style>

body
{
	margin: 0;
	padding: 0;
/*	background-color: blue;*/
}

#htmlcontainer
{
	position: absolute;
	top: $container_top;
	left: 0px;
	right: 0px;
	height: auto;
	padding: 10px;
	display: block;
	z-index: 100;
	overflow-y: auto;
/*	background-color: yellow;*/
}

</style>

<script>

function setQuizFlashHeight(height)
{
	if($ismobile)
	{
		$('#htmlcontainer').height(height);
	}
	
	else
	{
		getFlashObject('sansmediad').height = height;
		$(window).scrollTop(0);
	}
}

function loadHtmlContainer(url)
{
	$.ajax({
		url: url,
		async: false
	}).success(htmlcontainer_ready);
}

function reportcontainer_height(hh, wh)
{
//	alert("reportcontainer_height "+hh+" "+wh);
	if($ismobile)
	{
		var ret = new Object;
		
		ret['method'] = 'reportQuizHtmlHeight';
		ret['hh'] = hh;
		ret['wh'] = wh;
		
		document.location = '$prot' + JSON.stringify(ret);
	}
	else
		getFlashObject("sansmediad").reportQuizHtmlHeight(hh, wh);
}

// function alertobject(prefix, object)
// {
// 	for (var property in object)
// 	{
// 		if(typeof object[property] == 'function') continue;
// 		alert(prefix + " - " + property + ': ' + object[property] + '; ');
		
// 		if(prefix == "" && typeof object[property] == 'object')
// 			alertobject(property, object[property]);
// 	}
// }

function htmlcontainer_ready(data)
{
	$('#htmlcontainer').empty();
	$('#htmlcontainer').height('auto');
    $('#htmlcontainer').width($(document).width());
	$('#htmlcontainer').html(data);
	
	setTimeout(function()
	{
		var htmlcontainer_height = $('#htmlcontainer').height();
		reportcontainer_height(htmlcontainer_height, $(window).height());
	}, 200);
}

///////////////////////////////////////////////////////////////////////////////////////

function showQuizHtml()
{
	$('#htmlcontainer').show();
}

function hideQuizHtml()
{
	$('#htmlcontainer').hide();
}

// special case for longtext answer

function saveQuizAnswerLongText(params)
{
	var attemptid = params[0];
	var questionid = params[1];
	
	var obj = $('#quiz_answer_longtext');
	var value = $('#quiz_answer_longtext').elrte('val');

	value = encodeURIComponent(value);

	$.ajax({
		url: '/quiz/saveanswer?attemptid='+attemptid+'&questionid='+questionid+'&answerlong='+value,
		async: false
	}).success(saveQuizAnswerLongText_ready);
}

function saveQuizAnswerLongText_ready(data)
{
	if($ismobile)
	{
		var ret = new Object;
		ret['method'] = 'reportQuizAnswerLongText';
		document.location = '$prot' + JSON.stringify(ret);
	}
	else
		getFlashObject("sansmediad").reportQuizAnswerLongText();
}
		
//////////////////////////////////////////////////

function getFlashObject(movieName)
{
	var isIE = navigator.appName.indexOf('Microsoft') != -1;
	return (isIE) ? window[movieName] : document[movieName];
}

/////////////////////////////////////////////////////////////////////////

function initializeHtml5Upload()
{
	$("#upload_field").html5_upload({

		method: 'post',
		sendBoundary: window.FormData || $.browser.mozilla,
		
		url: function(number) {
			return "/upload.php?phpsessid=$sessionid";
		},
		
		onStart: function(event, total) {
			$("#progress_report").show();
			return true;
		},
		
		setName: function(text) {
			$("#progress_report_name").html("<b>"+text+"</b>");
		},
	
		setProgress: function(val) {
			$("#progress_report_bar").css('width', Math.ceil(val*100)+"%");
		},
		
		onFinish: function(event, total) {
		//	window.location = window.location+'&complete';
		},
		
		onError: function(event, name, error) {
			alert('Error while uploading file ' + name);
		}
	});
}

</script>

</head>
<body>

END;


$getflash = mainimg('getflash.jpg');
$flashvars = "quizid=$object->id";

ShowApplication($flashvars, 'quiz', 'sansmediad', '500', false);
//JavascriptReady("RightClick.init('sansmediad');");

echo "<div id='htmlcontainer'></div></body>";








