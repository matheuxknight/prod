<?php		
		
if(!isset($_SERVER['HTTP_REFERER']) && IsMobileDevice()) return '';

$parentid = getparam('parentid');
$masterid = getparam('masterid');
$recordid = getparam('recordid');

echo "<table width='100%'><tr><td valign='top'>";

if($recordid)
{
	$file = getdbo('VFile', $recordid);
	$this->pageTitle = app()->name .' - '. $file->name;
	
	//showRoleBar($file);
	//showNavigationBar($file->parent);
	//showObjectHeader($file);
	//showObjectMenu($file->object);
}

else if($parentid)
{
	$parent = getdbo('Object', $parentid);
	$this->pageTitle = app()->name .' - '. $parent->name;
	
	//showRoleBar($parent);
	//showNavigationBar($parent->parent);
	//showObjectHeader($parent);
	//showObjectMenu($parent);
}

else echo "<br>";

////////////////////////////////////////////////////////////////////////////

//$flashvars = "masterid=$masterid&recordid=$recordid&parentid=$parentid";
?>

<link href="/sansspace/ui/css/solo-recorder.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/bower_components/underscore/underscore.js"></script>
<script type="text/javascript" src="/bower_components/Recorderjs/recorder.js"></script>
<script src="/sansspace/ui/js/solo-recorder.js"></script>
<script>
    $(document).ready(function(){
        AudioRecord.init('#recorderForm', <?= $parent->id ?>, "<?= $parent->name ?>");
    });
</script>

<div id='form-holder'>
	<form id='recorderForm' class='form-horizontal'></form>
</div>

<?php
//ShowApplication($flashvars, 'recorder', 'sansmediad', 320);
//JavascriptReady("RightClick.init('$name');");

if($recordid)
{
	$file = getdbo('VFile', $recordid);
	
	//showObjectFooter($file);
	//showObjectComments($file);
}

else if($parentid)
{
	$parent = getdbo('Object', $parentid);
	
	//showObjectFooter($parent);
	//showObjectComments($parent);
}

else echo "<br>";

////////////////////////////////////////////////////////////////////////////


//showAllDropBoxRecordings();
echo "</td></tr></table>";



