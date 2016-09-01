<?php
// The request is a JSON request.
// We must read the input.
// $_POST or $_GET will not work!

$data = file_get_contents("https://people.yojoe.local/api/people");

$objData = json_decode($data);

// perform query or whatever you wish, sample:



foreach($objData->people as $person)
{
    if($person->employee_number == "125542")
    {
        echo $person->content;
    }
}

// Static array for this demo
// $values = array('php', 'web', 'angularjs', 'js');

// Check if the keywords are in our array
if(in_array($objData->data, $person)) {
	echo 'I have found what you\'re looking for!';
}
else {
	echo 'Sorry, no match!';
}
