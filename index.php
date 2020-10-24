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
	echo "<h2>Account #" . $accountId . "</h2>" . PHP_EOL;
	showPlayerStats($files, $accountId);
	showBinaryStats($files, $accountId);
	showSvml($files, $accountId);
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
	$elementNames = array("Total Points", "Team Points", "Combat Points", "Bonus Points", "Time Played", "Kills", "Deaths", "Kill/Death Ratio", "Accuracy", "Wins", "Losses", "Wins/Losses", "Score/Min", "DM Points", "TDM Points", "CTF Points", "Zones Points", "Miles Walked", "Miles Driven", "Miles Flown", "Hero Points", "Collection Points");

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
	$ranks = array("Recruit", "Airman", "Airman 1st Class", "Sergeant", "Chief Sergeant", "Wingman", "Wing Leader", "Sergeant Major", "Command Sergeant", "2nd Lieutenant", "1st Lieutenant", "Commander", "Captain", "Major", "Air Marshal", "Command Marshal", "Lt. Colonel", "Colonel", "Brigadier General", "General");

	$medals = array("Team Death Match", "Meritorious Service", "Team Cross", "Exemplary Team Cross", "Capture The Flag", "Distinguished Defense", "Zone Offense", "Zone Defense", "Marksman", "Combat", "Exemplary Combat", "Distinguished Combat", "Legion of Merit", "Distinguished Soldier", "Death Match", "Supreme Sacrifice", "Warhawk Service", "Warhawk Exemplary Service", "Warhawk Executive Award", "Air Combat", "Exemplary Air Combat", "Distinguished Air Combat", "Aerial Achievement");

	$ribbons = array("Warhawk Wings", "Count", "Master Wings", "Count", "Bandit Wings", "Count", "Warhawk Team", "Count", "Master Team", "Count", "Bandit Team", "Count", "Special Op", "Count", "Infantry Assault", "Count", "Joint Service Commendation", "Count", "Outstanding Transportation", "Count", "Vehicle Assault", "Count", "Combined Arms Achievement", "Count", "Anti-Air Defense", "Count", "Distinguished Marksman", "Count", "Distinguished Assault", "Count", "Distinguished Support", "Count", "Distinguished Air Superiority", "Count", "Distinguished Efficiency", "Count", "Warhawk Recruiting", "Count", "Warhawk Assault", "Count", "Warhawk Aerial Gunnery", "Count", "Good Conduct", "Count", "Outstanding Volunteer Service", "Count", "Presidential Meritorious Conduct", "Count", "CTF Offensive Merit", "Count", "CTF Defensive Merit", "Count", "Front Line Expansion", "Count", "World Victory", "Count", "Base Defense", "Count", "Winning Team", "Count");

	$badges = array("Defense - Bandit", "Defense - Master", "Defense - Warhawk", "Ground Combat - Bandit", "Ground Combat - Master", "Ground Combat - Warhawk", "Munitions - Bandit", "Munitions - Master", "Munitions - Warhawk", "Offense - Bandit", "Offense - Master", "Offense - Warhawk", "Teamwork - Bandit", "Teamwork - Master", "Teamwork - Warhawk", "4x4 - Bandit", "4x4 - Master", "4x4 - Warhawk", "Tank - Bandit", "Tank - Master", "Tank - Warhawk", "Turret - Bandit", "Turret - Master", "Turret - Warhawk", "Air to Air - Bandit", "Air to Air - Master", "Air to Air - Warhawk", "Air to Surface - Bandit", "Air to Surface - Master", "Air to Surface - Warhawk", "Air to Troop - Bandit", "Air to Troop - Master", "Air to Troop - Warhawk", "Air Mine - Bandit", "Air Mine - Master", "Air Mine - Warhawk", "Cluster Bomb - Bandit", "Cluster Bomb - Master", "Cluster Bomb - Warhawk", "Homing Missile - Bandit", "Homing Missile - Master", "Homing Missile - Warhawk", "Lightning Gun - Bandit", "Lightning Gun - Master", "Lightning Gun - Warhawk", "Machineguns - Bandit", "Machineguns - Master", "Machineguns - Warhawk", "Swarm Missile - Bandit", "Swarm Missile - Master", "Swarm Missile - Warhawk", "TOW Missile - Bandit", "TOW Missile - Master", "TOW Missile - Warhawk", "Binoculars - Bandit", "Binoculars - Master", "Binoculars - Warhawk", "Flamethrower - Bandit", "Flamethrower - Master", "Flamethrower - Warhawk", "Grenade - Bandit", "Grenade - Master", "Grenade - Warhawk", "Combat Blade - Bandit", "Combat Blade - Master", "Combat Blade - Warhawk", "Pistol - Bandit", "Pistol - Master", "Pistol - Warhawk", "Land Mine - Bandit", "Land Mine - Master", "Land Mine - Warhawk", "Rifle - Bandit", "Rifle - Master", "Rifle - Warhawk", "Rocket Launcher - Bandit", "Rocket Launcher - Master", "Rocket Launcher - Warhawk", "Sniper - Bandit", "Sniper - Master", "Sniper - Warhawk");

	//TODO: Figure out what this data represents... Some of it exists in other XML files.
	$others = array("Total Points", "Combat Points", "Team Points", "Bonus Points", "Negative Points|Total", "Game Count|Games Played", "Time Played", "Wins", "Losses", "Unknown10", "Unknown11", "Unknown12", "Kills", "Combat: Kills|Total Kills", "Combat: Kills|Kill Assists", "Deaths", "Objective Stats|Objective Captures", "Objective Stats|Objective Saves", "Objective Stats|Objective Kills", "Zone Stats|Captures", "Cluster Bomb|Combat: Deaths|Aircraft Kills", "Unknown22", "Zone Stats|Defends", "Unknown24", "Game Time|Time in Aircraft:|FIRST NUMBER", "Game Time|Time as a Solider:|FIRST NUMBER", "Game Time|Time in Vehicles:|FIRST NUMBER", "Unknown28", "Warhawk/Nemesis|Player Kills|This Vehicle Against Ground", "Warhawk/Nemesis|Player Kills|This Vehicle Against Air", "Warhawk/Nemesis|Combat|Total Kills", "Unknown32", "Unknown33", "4x4|Combat|Total Kills", "Medium Tank|Combat|Total Kills", "Unknown36", "Air Mines|Combat: Kills|Total Kills", "Cluster Bomb|Combat: Kills|Total Kills", "Homing Missile|Combat: Kills|Total Kills", "Unknown40", "Aircraft MG|Combat: Kills|Total Kills", "Swarm Missile|Combat: Kills|Total Kills", "TOW Missile|Combat: Kills|Total Kills", "Binoculars|Combat: Kills|Total Kills", "Flamethrower|Combat: Kills|Total Kills", "Grenade|Combat: Kills|Total Kills", "Combat Blade|Combat: Kills|Total Kills", "Pistol|Combat: Kills|Total Kills", "Land Mine|Combat: Kills|Total Kills", "Rifle|Combat: Kills|Total Kills", "Rocket Launcher|Combat: Kills|Total Kills", "Sniper Rifle|Combat: Kills|Total Kills", "Unknown53", "Unknown54");

	echo "<h3>" . str_replace('%3f', '?', basename($relatedFile)) . "</h3>";

	for ($i = 0; $i < count($pieces); $i++) {
		$piece = $pieces[$i];
		$obtained = hexdec(bin2hex($piece[0]));
		$date = null;

		if ($obtained === 1) {
			$month = hexdec(bin2hex($piece[1]));
			$day =  hexdec(bin2hex($piece[2]));
			$year = 2000 + hexdec(bin2hex($piece[3]));

			if (checkdate($month, $day, $year)) {
				$date = date_create($year . "-" . $month . "-" . $day);
			}
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

function showSvml($files, $accountId) {
	foreach ($files as $file) {
		if (substr(basename($file), 0, 6) !== "Stats_" && !strpos(basename($file), "(") && strpos(basename($file), "accountID=" . $accountId . "&")) {
			showWarhawkStats($file, $accountId);
		}
	}
}

function showWarhawkStats($file, $accountId) {
	$data = file_get_contents($file);
	$data = str_replace('&', '', $data);
	$data = simplexml_load_string($data);

	$labels = array(4 => "Eucadia", 6 => "Island Outpost", 8 => "The Badlands", 10 => "Omega Factory", 11 => "Destroyed Capitol", 12 => "Archipelago", 13 => "Vaporfield Glacier", 14 => "Tau Crater", 101 => "Aircraft MG", 102 => "Lightning Gun", 103 => "Charged Lightning Gun", 104 => "TOW Missile", 105 => "Swarm Missile", 106 => "Homing Missile", 107 => "Chaff", 108 => "Air Mines", 109 => "Cluster Bomb", 110 => "Stealth", 114 => "Pistol", 115 => "Flamethrower", 116 => "Rocket Launcher", 117 => "Land Mine", 118 => "Rifle", 119 => "Sniper Rifle", 120 => "Combat Blade", 121 => "Binoculars", 122 => "Grenade", 123 => "Field Wrench", 124 => "Bio Field", 126 => "Tank HE Round", 130 => "4x4 Heavy MG", 134 => "Fixed Heavy MG", 135 => "Flak Turret", 137 => "Missile Turret", 201 => "Warhawk/Nemesis", 203 => "Dropship", 204 => "4x4", 205 => "Medium Tank", 207 => "APC", 210 => "MachineGuns Turret", 211 => "Flak Turret", 213 => "Missile Turret", 300 => "Death Match", 301 => "Team Death Match", 302 => "Capture The Flag", 303 => "Zones", 304 => "Hero", 305 => "Collection", 325 => "Eucadian", 326 => "Chernovan", 500 => "Recruit", 501 => "Airman", 502 => "Airman 1st Class", 503 => "Sergeant", 504 => "Chief Sergeant", 505 => "Wingman", 506 => "Wing Leader", 507 => "Sergeant Major", 508 => "Command Sergeant", 509 => "2nd Lieutenant", 510 => "1st Lieutenant", 511 => "Commander", 512 => "Captain", 513 => "Major", 514 => "Air Marshal", 515 => "Command Marshal", 516 => "Lt. Colonel", 517 => "Colonel", 518 => "Brigadier General", 519 => "General", 1002 => "Statistics", 1003 => "Player Statistics", 1009 => "Stats Over Time", 1018 => "Combat", 1019 => "Objective Stats", 1020 => "Damage", 1021 => "Game Mode Stats", 1022 => "Game Summary", 1023 => "Global", 1025 => "Map Stats", 1026 => "Marksmanship", 1028 => "Player Stats", 1029 => "Team Stats", 1030 => "Team Play", 1032 => "Scores", 1033 => "Vehicle Stats", 1034 => "Weapon Stats", 1035 => "Stats Over Time", 1036 => "Zones", 1039 => "Played as Chernovan", 1040 => "Played as Eucadian", 1041 => "Time as Chernovan", 1042 => "Time as Eucadian", 1067 => "Level 2 Expansions", 1068 => "Level 2 Expansion Assists", 1069 => "Level 3 Expansions", 1070 => "Level 3 Expansion Assists", 1071 => "Accuracy", 1072 => "Against You", 1074 => "Ammo", 1075 => "Ammo Collected", 1078 => "As Troop", 1079 => "As Vehicle", 1083 => "  Bodyshots", 1084 => "Bodyshots", 1085 => "Bonus", 1088 => "Capture Assists", 1090 => "Captures", 1091 => "  Troop", 1092 => "Collision Hits", 1093 => "Combat", 1097 => "Damage Done", 1098 => "Damage Received", 1101 => "Deaths From Team", 1102 => "Deaths / Minute", 1103 => "Deaths / Round", 1104 => "Deaths", 1105 => "Deaths: Road", 1106 => "Defends", 1107 => "Objective Kill Assists", 1108 => "Disconnects", 1110 => "Early Exits", 1111 => "Game Mode", 1112 => "Level", 1113 => "Favorites", 1118 => "Objective Captures", 1119 => "Objective Grabs", 1120 => "Objective Saves", 1122 => "From Collision", 1124 => "From Self", 1125 => "From Team", 1126 => "From Troops", 1127 => "From Vehicles", 1128 => "Game Count", 1130 => "Game Mode Overview", 1131 => "Game Play", 1132 => "Games Completed", 1133 => "Games Played", 1134 => "Game Time", 1135 => "Total Points", 1137 => "Deadliest Weapon", 1138 => "Headshots", 1139 => "  Headshots", 1140 => "Health Pickups", 1142 => "Hits Received", 1144 => "Kicks", 1145 => "Kill Assists", 1146 => "Kill/Death Ratio", 1147 => "Kills", 1148 => "Kills / Minute", 1149 => "Kills / Round", 1150 => "Kills-Player Weapon", 1151 => "Kills: Road", 1152 => "Kills-Vehicle Weapon", 1154 => "Longest Death Streak", 1155 => "Longest Kill Streak", 1156 => "Losses", 1157 => "Map", 1160 => "Driven", 1161 => "Flown", 1162 => "On Foot", 1163 => "Miles Traveled", 1165 => "Mode", 1167 => "Most Deaths IAR", 1169 => "Most Kills IAR", 1171 => "Most IAR", 1173 => "Negative Points", 1174 => "Neutralize", 1175 => "Neutralize Assists", 1176 => "Number Of Pickups", 1178 => "Stats Over Time", 1179 => "Number of Kills", 1180 => "Player Killed By", 1181 => "Number of Kills", 1182 => "Player Killed", 1183 => "Player Overview", 1184 => "Player Rank", 1189 => "Road Deaths", 1190 => "Road Kills", 1191 => "Saves", 1192 => "Best Points In A Round", 1193 => "Global Points", 1195 => "Score Per Minute", 1196 => "Average Points Per Round", 1197 => "Shot Accuracy", 1198 => "Shots Fired", 1199 => "Shots Hit", 1200 => "Suicides", 1201 => "Team", 1203 => "Team Deaths", 1204 => "Team Kills", 1205 => "Team Overview", 1207 => "Team Points", 1209 => "Team Stats", 1210 => "Zone Stats", 1212 => "Time As Driver", 1213 => "Time As Passenger", 1214 => "Time as a Solider", 1215 => "Time in Vehicles", 1216 => "Time in Aircraft", 1217 => "Time Played", 1219 => "Total Damage Done", 1220 => "Total Damage Received", 1221 => "Total Distance", 1223 => "Total", 1224 => "Wins/Losses", 1225 => "Total Time Using", 1226 => "To Team", 1227 => "To Troops", 1228 => "To Vehicles", 1230 => "  Vehicle", 1232 => "Vehicle Deaths", 1233 => "Vehicle Hits", 1234 => "  Vehicle Hits", 1235 => "Vehicle Kills", 1236 => "Vehicle Overview", 1237 => "Vehicles Destroyed", 1239 => "  Air Weapon", 1240 => "Aircraft Deaths", 1241 => "Aircraft Kills", 1242 => "Weapon", 1244 => "Weapon Most Killed By", 1245 => "Weapon With Most Kills", 1247 => "Win/Loss Ratio", 1248 => "Wins", 1250 => "With Collision", 1253 => "Player Kills", 1254 => "You Against This Vehicle", 1255 => "This Vehicle Against Ground", 1256 => "This Vehicle Against Air", 1158 => "Map Overview", 1259 => "Time In", 1260 => "Weapon Overview", 1261 => "KDR", 1263 => "Time As", 1265 => "Played", 1267 => "Most Points IAR", 1268 => "Select Stats Over Time to view Graphs of your stats.", 1269 => "View your history for Global, Team Stats, Capture ", 1270 => "the Flag and Zones modes. The graphs only keep your ", 1271 => "history for the last 90 days.", 1272 => "Press X to enter the Stats Over Time page menu", 1274 => "Combat: Kills", 1275 => "Combat: Deaths", 1276 => "Total Kills", 1277 => "Total Deaths", 1278 => "Ties", 1279 => "Vehicle", 1280 => "Global Accuracy", 1281 => "Objective Kills", 1282 => "Zone Play", 1283 => "Objective Stats", 1284 => "Level 2 Reductions", 1285 => "Level 2 Reduction Assists", 1286 => "Level 3 Reductions", 1287 => "Level 3 Reduction Assists");

	$buttons = array();
	$buttonCounter = 0;

	echo "<h3>" . str_replace('%3f', '?', basename($file)) . "</h3>" . PHP_EOL;

	foreach ($data->BUTTON as $button) {
		array_push($buttons, $labels[trim($button)]);
	}

	foreach ($data->TEXT as $text) {
		if (strpos($text['name'], "header")) {
			if (substr($text['name'], 1, 1) == $buttonCounter + 1) {
				echo "<h4>" . $buttons[$buttonCounter] . "</h4>" . PHP_EOL;
				$buttonCounter++;
			}

			echo "<h5>" . $labels[trim($text)] . "</h5>" . PHP_EOL;
		}
		else if (substr($text['name'], -1) === "N")
		{
			echo "<strong>" . $labels[trim($text)] . "</strong>: ";
		}
		else if (substr($text['name'], -1) === "V")
		{
			if ($text['class'] == "locnamelocvalue") {
				echo $labels[trim($text)] . PHP_EOL;
			}
			else {
				echo $text . PHP_EOL;
			}
		}
		else if ($text['name'] == "pagetitle") {
			echo $labels[trim($text)] . PHP_EOL;
		}
		else {
			echo $text . PHP_EOL;
		}
	}

	foreach ($data->GRID as $grid) {
		echo "<h4>" . $buttons[$buttonCounter] . "</h4>" . PHP_EOL;
		$buttonCounter++;

		echo "<table>" . PHP_EOL;
		echo "<caption>" . $labels[trim($grid['toolTip'])] . "</caption>" . PHP_EOL;

		foreach ($grid->COLUMNS as $columns) {
			echo "<thead>" . PHP_EOL . "<tr>" . PHP_EOL;

			foreach ($columns->COLUMN as $column) {
				echo "<th>" . $labels[trim($column)] . "</th>" . PHP_EOL;
			}

			echo "</tr>" . PHP_EOL . "</thead>" . PHP_EOL;
		}

		foreach ($grid->ROWS as $rows) {
			echo "<tbody>" . PHP_EOL;

			foreach ($rows->ROW as $row) {
				echo "<tr>" . PHP_EOL;

				foreach ($row->CELL as $cell) {
					echo "<td>";

					if ($cell['href'] || !$row->CELL[0]['href']) {
						echo $labels[trim($cell)];
					}
					else {
						echo trim($cell);
					}

					echo "</td>" . PHP_EOL;
				}

				echo "</tr>" . PHP_EOL;
			}

			echo "</tbody>" . PHP_EOL;
		}

		echo "</table>" . PHP_EOL;
	}

	foreach ($data->GRAPH as $graph) {
		if (substr($graph['name'], 1, 1) - 1 == $buttonCounter && substr($graph['name'], 7, 1) == 1) {
			echo "<h4>" . $buttons[$buttonCounter] . "</h4>" . PHP_EOL;
			$buttonCounter++;
		}

		echo "<table>" . PHP_EOL;
		echo "<caption>" . $labels[trim($graph['toolTip'])] . "</caption>" . PHP_EOL;

		foreach ($graph->COLUMNS as $columns) {
			echo "<thead>" . PHP_EOL . "<tr>" . PHP_EOL;

			foreach ($columns->COLUMN as $column) {
				echo "<th>" . trim($column) . "</th>" . PHP_EOL;
			}

			echo "</tr>" . PHP_EOL . "</thead>" . PHP_EOL;
		}

		foreach ($graph->ROWS as $rows) {
			echo "<tbody>" . PHP_EOL;

			foreach ($rows->ROW as $row) {
				echo "<tr>" . PHP_EOL;

				foreach ($row->CELL as $cell) {
					echo "<td>" . trim($cell) . "</td>" . PHP_EOL;
				}

				echo "</tr>" . PHP_EOL;
			}

			echo "</tbody>" . PHP_EOL;
		}

		echo "</table>" . PHP_EOL;
	}
}
?>
</pre>
</body>
</html>
