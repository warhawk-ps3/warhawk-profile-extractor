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
include 'labels.php';
$inputDir = "input/";
$files = glob($inputDir . "*.jsp*ID=*");
$accountIds = getAccountIds($files);

foreach ($accountIds as $accountId) {
	echo "<h2>Account #" . $accountId . "</h2>" . PHP_EOL;
	showPlayerStats($files, $accountId, $labels);
	showBinaryStats($files, $accountId, $labels);
	showSvml($files, $accountId, $labels);
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

function showPlayerStats($files, $accountId, $labels) {
	$relatedFile = getRelatedFiles($files, $accountId, "Stats_GetPlayerStats.jsp")[0];
	$data = file_get_contents($relatedFile);
	$data = simplexml_load_string($data);

	$attributeNames = $labels["showPlayerStats"]["attributeNames"];
	$elementNames = $labels["showPlayerStats"]["elementNames"];

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

function showBinaryStats($files, $accountId, $labels) {
	$relatedFile = getRelatedFiles($files, $accountId, "Stats_BinaryStatsDownload_Submit.jsp")[0];
	$data = file_get_contents($relatedFile);
	$pieces = str_split($data, 4);

	$ranks = $labels["showBinaryStats"]["ranks"];
	$medals = $labels["showBinaryStats"]["medals"];
	$ribbons = $labels["showBinaryStats"]["ribbons"];
	$badges = $labels["showBinaryStats"]["badges"];
	$others = $labels["showBinaryStats"]["others"];

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

function showSvml($files, $accountId, $labels) {
	foreach ($files as $file) {
		if (substr(basename($file), 0, 6) !== "Stats_" && !strpos(basename($file), "(") && strpos(basename($file), "accountID=" . $accountId . "&")) {
			showWarhawkStats($file, $accountId, $labels);
		}
	}
}

function showWarhawkStats($file, $accountId, $labels) {
	$data = file_get_contents($file);
	$data = str_replace('&', '', $data);
	$data = simplexml_load_string($data);

	$labels = $labels["showWarhawkStats"]["labels"];

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
