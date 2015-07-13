<?php
	$questions = getdbolist('quizquestion');
	$i = 1;
	echo "<style>
			td{
				border:1px solid #555;
				padding: 5px;
			}
		   </style>";	
	echo "<table>";
	foreach($questions as $question)
	{
		if(strpos($question->question, "<img")){
			echo "<tr><td>$i</td><td><a href='/question/update?id="; echo $question->id; echo"'>$question->id</a></td>";
			
			echo "</tr>";
			$i++;
			}
	}
	echo "</table>";