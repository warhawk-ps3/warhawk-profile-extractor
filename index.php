<html>
<head>
<title>Warhawk profile extractor</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="theme.css" rel="stylesheet" />
</head>
<body>
<pre>
<h1>Warhawk profile extractor</h1>
<?php
$inputDir = "input/";
$files = glob($inputDir . "*.jsp*ID=*");
$accountIds = getAccountIds($files);

foreach ($accountIds as $accountId) {
	echo "<h2>Account #" . $accountId . "</h2>";
	showPlayerStats($files, $accountId);
	showBinaryStats($files, $accountId);
}

function getAccountIds($files) {
	$ids = array();

	foreach ($files as $file) {
		preg_match("/.*ID=(\d+).*/", $file, $matches);
		if (!in_array($matches[1], $ids, true)) {
			array_push($ids, $matches[1]);
		}
	}

	sort($ids, SORT_NUMERIC);

	return $ids;
}

function getRelatedFiles($files, $accountId, $prefix) {
	$relatedFiles = array();

	foreach ($files as $file) {
		if (strpos($file, $prefix) && strpos($file, "ID=" . $accountId . "&")) {
			array_push($relatedFiles, $file);
		}
	}

	return $relatedFiles;
}

function showPlayerStats($files, $accountId) {
	$relatedFile = getRelatedFiles($files, $accountId, "Stats_GetPlayerStats.jsp")[0];
	$data = simplexml_load_file(__DIR__ . "/" . $relatedFile);
	$attributeNames = array("Game Mode", "Rank", "User ID", "Username");
	$elementNames = array("Rank", "Name", "Total Points", "Team Points", "Combat Points", "Bonus Points", "Time Played", "Kills", "Deaths", "Kill/Death Ratio", "Accuracy", "Wins", "Losses", "Wins/Losses", "Score/Min", "DM Points", "TDM Points", "CTF Points", "Zones Points", "Miles Walked", "Miles Driven", "Miles Flown", "Hero Points", "Collection Points");

	echo "<h3>" . str_replace('%3f', '?', basename($relatedFile)) . "</h3>";

	foreach ($data->Career_Leaderboard as $careerLeaderboard) {
		$attributes = $careerLeaderboard->attributes();
		$elements = $careerLeaderboard->children();

		for ($i = 0; $i < count($attributes); $i++) {
			$attribute = $attributes[$i];
			echo "<strong>" . $attributeNames[$i] . " (" . $attribute->getName() . "):</strong> " . $attribute . PHP_EOL;
		}

		for ($i = 0; $i < count($elements); $i++) {
			$element = $elements[$i];
			echo "<strong>" . $elementNames[$i] . " (" . $element->getName() . "):</strong> " . $element . PHP_EOL;
		}
	}
}

function showBinaryStats($files, $accountId) {
	$relatedFile = getRelatedFiles($files, $accountId, "Stats_BinaryStatsDownload_Submit.jsp")[0];
	$data = file_get_contents($relatedFile);
	$pieces = str_split($data, 4);

	//Stat names sourced from Warhawk Stats (https://web.archive.org/web/20131209050121/http://www.warhawkstats.com/)
	$ranks = array("Recruit", "Airman", "Airman 1st Class", "Sergeant", "Chief Sergeant", "Wingman", "Wing Leader", "Sergeant Major", "Cmd Sergeant", "2nd Lieutenant", "1st Lieutenant", "Commander", "Captain", "Major", "Air Marshal", "Cmd Marshal", "Lt. Colonel", "Colonel", "Brigadier Gen.", "General");

	$medals = array("Team Death Match", "Meritorious Service", "Team Cross", "Exemplary Team Cross", "Capture The Flag", "Distinguished Defense", "Zone Offense", "Zone Defense", "Marksman", "Combat", "Exemplary Combat", "Distinguished Combat", "Legion of Merit", "Distinguished Soldier", "Death Match", "Supreme Sacrifice", "Warhawk Service", "Warhawk Exemplary Service", "Warhawk Executive Award", "Air Combat", "Exemplary Air Combat", "Distinguished Air Combat", "Aerial Achievement");

	$ribbons = array("Warhawk Wings", "Count", "Master Wings", "Count", "Bandit Wings", "Count", "Warhawk Team", "Count", "Master Team", "Count", "Bandit Team", "Count", "Special Op", "Count", "Infantry Assault", "Count", "Joint Service Commendation", "Count", "Outstanding Transportation", "Count", "Vehicle Assault", "Count", "Combined Arms Achievement", "Count", "Anti-Air Defense", "Count", "Distinguished Marksman", "Count", "Distinguished Assault", "Count", "Distinguished Support", "Count", "Distinguished Air Superiority", "Count", "Distinguished Efficiency", "Count", "Warhawk Recruiting", "Count", "Warhawk Assault", "Count", "Warhawk Aerial Gunnery", "Count", "Good Conduct", "Count", "Outstanding Volunteer Service", "Count", "Presidential Meritorious Conduct", "Count", "CTF Offensive Merit", "Count", "CTF Defensive Merit", "Count", "Front Line Expansion", "Count", "World Victory", "Count", "Base Defense", "Count", "Winning Team", "Count");

	$badges = array("Defense - Bandit", "Defense - Master", "Defense - Warhawk", "Ground Combat - Bandit", "Ground Combat - Master", "Ground Combat - Warhawk", "Munitions - Bandit", "Munitions - Master", "Munitions - Warhawk", "Offense - Bandit", "Offense - Master", "Offense - Warhawk", "Teamwork - Bandit", "Teamwork - Master", "Teamwork - Warhawk", "4x4 - Bandit", "4x4 - Master", "4x4 - Warhawk", "Tank - Bandit", "Tank - Master", "Tank - Warhawk", "Turret - Bandit", "Turret - Master", "Turret - Warhawk", "Air to Air - Bandit", "Air to Air - Master", "Air to Air - Warhawk", "Air to Surface - Bandit", "Air to Surface - Master", "Air to Surface - Warhawk", "Air to Troop - Bandit", "Air to Troop - Master", "Air to Troop - Warhawk", "Air Mine - Bandit", "Air Mine - Master", "Air Mine - Warhawk", "Cluster Bomb - Bandit", "Cluster Bomb - Master", "Cluster Bomb - Warhawk", "Homing Missile - Bandit", "Homing Missile - Master", "Homing Missile - Warhawk", "Lighting Gun - Bandit", "Lighting Gun - Master", "Lighting Gun - Warhawk", "Machineguns - Bandit", "Machineguns - Master", "Machineguns - Warhawk", "Swarm Missile - Bandit", "Swarm Missile - Master", "Swarm Missile - Warhawk", "TOW Missile - Bandit", "TOW Missile - Master", "TOW Missile - Warhawk", "Binoculars - Bandit", "Binoculars - Master", "Binoculars - Warhawk", "Flamethrower - Bandit", "Flamethrower - Master", "Flamethrower - Warhawk", "Grenade - Bandit", "Grenade - Master", "Grenade - Warhawk", "Knife - Bandit", "Knife - Master", "Knife - Warhawk", "Pistol - Bandit", "Pistol - Master", "Pistol - Warhawk", "Land Mine - Bandit", "Land Mine - Master", "Land Mine - Warhawk", "Rifle - Bandit", "Rifle - Master", "Rifle - Warhawk", "Rocket Launcher - Bandit", "Rocket Launcher - Master", "Rocket Launcher - Warhawk", "Sniper - Bandit", "Sniper - Master", "Sniper - Warhawk");

	//TODO: Figure out what this data represents... Some of it exists in other XML files.
	$others = array("Total Points", "Combat Points", "Team Points", "Bonus Points", "Unknown5", "Unknown6", "Time Played", "Wins", "Losses", "Unknown10", "Unknown11", "Unknown12", "Kills", "Unknown14", "Unknown15", "Deaths", "Unknown17", "Unknown18", "Unknown19", "Unknown20", "Unknown21", "Unknown22", "Unknown23", "Unknown24", "Unknown25", "Unknown26", "Unknown27", "Unknown28", "Unknown29", "Unknown30", "Unknown31", "Unknown32", "Unknown33", "Unknown34", "Unknown35", "Unknown36", "Unknown37", "Unknown38", "Unknown39", "Unknown40", "Unknown41", "Unknown42", "Unknown43", "Unknown44", "Unknown45", "Unknown46", "Unknown47", "Unknown48", "Unknown49", "Unknown50", "Unknown51", "Unknown52", "Unknown53", "Unknown54");

	echo "<h3>" . str_replace('%3f', '?', basename($relatedFile)) . "</h3>";

	for ($i = 0; $i < count($pieces); $i++) {
		$piece = $pieces[$i];
		$obtained = hexdec(bin2hex($piece[0]));
		$date = null;

		if ($obtained) {
			$month = hexdec(bin2hex($piece[1]));
			$day =  hexdec(bin2hex($piece[2]));
			$year = 2000 + hexdec(bin2hex($piece[3]));
			$date = date_create($year . "-" . $month . "-" . $day);
		}

		if ($date) {
			$suffix = date_format($date, 'Y-m-d');
		}
		else {
			$suffix = hexdec(bin2hex($piece));
		}

		if ($i === 0) {
			$suffix .= " (CURRENT RANK: " . $ranks[hexdec(bin2hex($piece[3]))] . ")";
		}
		else if ($i < 21) {
			$suffix .= " (RANK: " . $ranks[$i - 1] . ")";
		}
		else if ($i > 20 && $i < 44) {
			$suffix .= " (MEDAL: " . $medals[$i - 21] . ")";
		}
		else if ($i > 48 && $i < 109) {
			$suffix .= " (RIBBON: " . $ribbons[$i - 49] . ")";
		}
		else if ($i > 116 && $i < 198) {
			$suffix .= " (BADGE: " . $badges[$i - 117] . ")";
		}
		else if ($i > 203 && $i < 258) {
			$suffix .= " (OTHER: " . $others[$i - 204] . ")";
		}
		else if ($i === (count($pieces) - 1)) {
			$suffix .= " (ACCOUNT ID)";
		}

		echo bin2hex($piece) . " = " . $suffix . PHP_EOL;
	}
}
?>
</pre>
</body>
</html>
