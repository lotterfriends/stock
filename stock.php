<?php

// action
savePlace();
saveThing();
deleteThing();
deletePlace();
increaseCount();
decreaseCount();

// page
showHistory();
showPlaces();
showAddPlace();
showSearchResult();
showAddThingPlaces();
showAddThing();
showEditThingSearch();
showThingsForPlace();
showEditThingPlaces();
showEditThing();
showSearch();
showEditPlace();
showAllThings();
showAllPlaces();

function showPlaces() {
	global $page;
	if ($page != 'places') return;
	showAllPlaces();
}

function showAllPlaces() {
	$view = new Template('places.html', true);
	$view->setContent('PLACES', getAllPlaces());
	$view->setOne('count', 'active');
	$view->setOne('placesActive', 'active');
	die($view->vorlage);
}

function getAllPlaces($placeId = false) {
	$setArray = array();
	$i = 0;
	$id = ($placeId !== false) ? $placeId : cleanGet('placeId');
	$results = mysql_query("SELECT * from places");
	if (!empty($results)){
		while ($data = mysql_fetch_assoc($results)) {
			$setArray[$i] = array(
				'id' => $data['id'],
				'name' => $data['name'],
				'content' => getThingCountForPlace($data['id'])
			);
			$setArray[$i]['selected'] = ($id == $data['id']) ? 'selected=selected ' : '';
			$i++;
		}
	}
	return $setArray;
}

function getPlace() {
	$id = cleanGet('placeId');
	$result = mysql_query("SELECT * from places WHERE `id` = ".$id);
	return mysql_fetch_assoc($result);
}

function getPlaceName($id) {
	$result = mysql_query("SELECT * from places WHERE `id` = ".$id);
	return mysql_fetch_assoc($result)['name'];
}

function getThingName($id) {
	$result = mysql_query("SELECT * from thing WHERE `id` = ".$id);
	return mysql_fetch_assoc($result)['name'];
}

function showAddPlace() {
	global $page;
	if ($page != 'addPlace') return;
	$view = new Template('addPlace.html', true);
	$view->setOne('placesActive', 'active');
	die($view->vorlage);
}

function savePlace() {
	global $action;
	if ($action != 'savePlace') return;
	$id = cleanPost('placeId');
	$name = cleanPost('name');
	if (!empty($id)) {
		createHistoryEntry('Der Ort '. getPlaceName($id) .' wurde in '.$name. ' umbenannt', 'places', $id);
		mysql_query('UPDATE `places` SET `name` = "'.$name.'" WHERE `id` = "'.$id.'"');
	} else {
		if (!empty($name)) {
			createHistoryEntry('Der Ort '.$name.' wurde erstellt');
			$r = mysql_query ('INSERT INTO `places` (`name`) VALUES ("'.$name.'")');

		}
	}
	redirect("?page=places");
}



function showAllThings() {
	global $page;
	if ($page != 'things') return;
	$view = new Template('things.html', true);
	$view->setContent('THINGS', getAllThings());
	$view->setOne('thingsActive', 'active');
	die($view->vorlage);
}

function addThing($active, $breadcrumb) {
	$view = new Template('addThing.html', true);
	$places = getAllPlaces();
	$view->setContent('PLACES', $places);
	$placeId = cleanGet('placeId');
	if ($placeId === '' && count($places) > 0) {
		$placeId = $places[0]['id'];
	}
	$view->setOne('placeId', $placeId);
	$view->setOne($active, 'active');
	$view->setOne('breadcrumb', $breadcrumb);
	die($view->vorlage);
}

function showAddThing() {
	global $page;
	if ($page != 'addThing') return;
	$breadcrumb = '<a href="?page=things">Sachen</a> / hinzufügen';
	addThing('thingsActive', $breadcrumb);
}


function showAddThingPlaces() {
	global $page;
	if ($page != 'addThingPlaces') return;
	$placeId = cleanGet('placeId');
	$breadcrumb = '<a href="?page=places">Orte</a> / <a href="?page=showPlace&placeId='.$placeId.'">'.getPlaceName($placeId).'</a> / hinzufügen';
	addThing('placesActive', $breadcrumb);
}

function showSearchResult() {
	global $page;
	if ($page != 'searchResult') return;
	$view = new Template('search.html', true);
	$view->setContent('THINGS', searchThing());
	$view->setOne('thingsActive', 'active');
	die($view->vorlage);
}

function showThingsForPlace() {
	global $page;
	if ($page != 'showPlace') return;
	$view = new Template('place.html', true);
	$place = getPlace();
	// $view->setArray(getPlace());
	$things = getThingsForPlace();
	$view->setContent('THINGS', $things);
	$view->setOne('placesActive', 'active');
	$view->setOne('count', getThingCountForPlace($place['id']));
	$view->setOne('placeName', $place['name']);
	$view->setOne('placeId', $place['id']);
	die($view->vorlage);
}

function getThingCountForPlace($placeId) {
	$results = mysql_query("SELECT SUM(count) AS total FROM thing WHERE placeId = $placeId");
	$data = mysql_fetch_assoc($results);
	$total = floatval($data['total']);
	return $total;
}


function showSearch() {
	global $page;
	if ($page != 'search') return;
	$view = new Template('search.html', true);
	$view->setContent('THINGS', searchThing());
	$view->setOne('searchActive', 'active');
	$view->setOne('query', cleanPost('query'));
	die($view->vorlage);
}

function getAllThings() {
	$setArray = array();
	$i = 0;
	$results = mysql_query("SELECT * from thing ORDER BY name ASC");
	if (!empty($results)){
		while ($data = mysql_fetch_assoc($results)) {
			$setArray[$i] = array(
				'id' => $data['id'],
				'name' => $data['name'],
				'count' => $data['count'],
				'placeId' => $data['placeId'],
				'place' => getPlaceName($data['placeId'])
			);
			$i++;
		}
	}
	return $setArray;
}

function getThingsForPlace() {
	$i = 0;
	$placeId = cleanGet('placeId');
	$setArray = array();
	$results = mysql_query("SELECT * from thing WHERE placeId = ".$placeId." ORDER BY name ASC");
	if (!empty($results)){
		while ($data = mysql_fetch_assoc($results)) {
			$setArray[$i] = array(
				'id' => $data['id'],
				'name' => $data['name'],
				'count' => $data['count']
			);
			$i++;
		}
	}
	return $setArray;
}

function getThingsCountForPlace($placeId) {
	$result = mysql_query("SELECT COUNT(id) from thing WHERE placeId = ".$placeId);
	$count = mysql_fetch_row($result);
	$count = $count[0];
	return $count;
}

function saveThing() {
	global $action;
	if ($action != 'saveThing') return;
	$name = cleanPost('name');
	$count = floatval(cleanPost('count'));
	$place = cleanPost('targetPlaceId');
	$id = cleanPost('thingId');

	// edit
	if (!empty($id)) {
		createHistoryEntry('Die Sache <a href="?page=editThing&thingId='.$id.'">' . $name .'</a> wurde bearbeitet');
		mysql_query('UPDATE `thing` SET `count` = "'.$count.'", `name` = "'.$name.'", `placeId` = "'.$place.'" WHERE `id` = "'.$id.'"');
	} else {
		if (!empty($name)) {
			$results = mysql_query('SELECT * FROM thing WHERE `name` = "'.$name.'" AND `placeId` = "'.$place.'"');
			$alreadyExistingThing = mysql_fetch_array($results);
			if ($alreadyExistingThing !== false) {
				createHistoryEntry('Eine Sache mit dem Namen '.$name.' existierte bereits. Die Existierende Sache wurde um '.$count. ' erhöht');
				mysql_query('UPDATE `thing` SET `count` = `count` + '.$count.' WHERE `name` LIKE "'.$name.'"');
			} else {
				mysql_query('INSERT INTO `thing` (`name`, `count`, `placeId`) VALUES ("'.$name.'", "'.$count.'", "'.$place.'")');
				createHistoryEntry('Die Sache <strong>'.$name.'</strong> wurde erstellt');
			}
			
		}
	}
	// redirect('?page=showPlace&placeId=' . $place);
}

function deleteThing() {
	global $action;
	if ($action != 'deleteThing') return;
	$id = cleanGet('id');
	$placeId = cleanGet('placeId');
	createHistoryEntry("Die Sache ".getThingName($id)." wurde gelöscht", 'thing', $id);
	mysql_query("DELETE FROM `thing` WHERE `id`=$id");
	die();
}

function deletePlace() {
	global $action;
	if ($action != 'deletePlace') return;
	$id = cleanGet('placeId');
	createHistoryEntry("Der Ort ".getPlaceName($id)." wurde gelöscht", 'places', $id);
	mysql_query("DELETE FROM `places` WHERE `id`=$id");
	die();
}

function editThing($active, $breadcrumb, $target='') {
	$thingId = cleanGet('thingId');
	$result = mysql_fetch_assoc(mysql_query("SELECT * FROM `thing` WHERE `id`=$thingId"));
	$view = new Template('editThing.html', true);
	$view->setContent('PLACES', getAllPlaces($result['placeId']));
	$view->setOne($active, 'active');
	$view->setOne('placeId', $result['placeId']);
	$view->setOne('thingName', $result['name']);
	$view->setOne('thingCount', $result['count']);
	$view->setOne('thingId', $result['id']);
	$view->setOne('target', $target);
	$view->setOne('breadcrumb', $breadcrumb);
	die($view->vorlage);
}

function showEditThingPlaces() {
	global $page;
	if ($page != 'editThingPlaces') return;
	$placeId = cleanGet('placeId');
	$breadcrumb = '<a href="?page=places">Orte</a> / <a href="?page=showPlace&placeId='.$placeId.'">'.getPlaceName($placeId).'</a> / Sache ändern';
	editThing('placesActive', $breadcrumb, "&amp;page=showPlace&placeId=$placeId");
}

function showEditThing() {
	global $page;
	if ($page != 'editThing') return;
	$breadcrumb = '<a href="?page=things">Sachen</a> / ändern';
	editThing('thingsActive', $breadcrumb, '&amp;page=things');
}

function showEditThingSearch() {
	global $page;
	if ($page != 'editThingSearch') return;
	$breadcrumb = '<a href="?page=search">Suche</a> / Sache ändern';
	$query = cleanGet('query');
	editThing('searchActive', $breadcrumb, "&amp;page=search&amp;query=$query");
};

function showEditPlace() {
	global $page;
	if ($page != 'editPlace') return;
	$placeId = cleanGet('placeId');
	$result = mysql_fetch_assoc(mysql_query("SELECT * FROM `places` WHERE `id`=$placeId"));
	$view = new Template('editPlace.html', true);
	$view->setOne('placesActive', 'active');
	$view->setOne('placeName', $result['name']);
	$view->setOne('placeId', $result['id']);
	die($view->vorlage);
}

function searchThing() {
	global $action;
	$query = cleanGet('query');
	if ($action != 'searchThing' && empty($query)) return;
	if (empty($query)) {
		$query = cleanPost('query');
	}

	$setArray = array();
	$i = 0;
	if (!startsWith($query, '^')) {
		$query = '%' . $query;
	} else {
		$query = preg_replace('/^\^(.*)/', '$1', $query);
	}
	if (!endsWith($query, '$')) {
		$query = $query . '%'; 
	} else {
		$query = preg_replace('/(.*)\$$/', '$1', $query);
	}
	if(strpos($query, '*') > -1) {
		$query = preg_replace('/\*/', '%', $query);	
	}
	$results = mysql_query('SELECT * FROM thing WHERE `name` LIKE "'.$query.'" ORDER BY name ASC');
	if (!empty($results)){
		while ($data = mysql_fetch_assoc($results)) {
			$setArray[$i] = array(
				'id' => $data['id'],
				'name' => $data['name'],
				'count' => $data['count'],
				'place' => getPlaceName($data['placeId']),
				'placeId' => $data['placeId']
			);
			$i++;
		}
	}
	return $setArray;
}

function decreaseCount() {
	global $action;
	if ($action != 'decreaseCount') return;
	$id = cleanGet('thingId');
	$currentCount = floatval(getThingCount($id));
	$newCount = $currentCount - 1;
	if ($newCount >= 0) {
		createHistoryEntry(getThingName($id) ." wurde von " . $currentCount . " auf ". $newCount . " verringert", 'thing', $id);
		mysql_query("UPDATE thing SET count = '$newCount' WHERE id = $id");
		die($newCount);
	} else {
		die(0);
	}
}

function increaseCount() {
	global $action;
	if ($action != 'increaseCount') return;
	$id = cleanGet('thingId');
	$currentCount = floatval(getThingCount($id));
	$newCount = $currentCount + 1;
	createHistoryEntry(getThingName($id) ." wurde von " . $currentCount . " auf ". $newCount . " erhöht", 'thing', $id);
	echo "UPDATE thing SET count = '$newCount' WHERE id = $id";
	mysql_query("UPDATE thing SET count = '$newCount' WHERE id = $id");
	die($newCount);
}

function getThingCount($id) {
	$result = mysql_query("SELECT * from thing WHERE `id` = ".$id);
	return mysql_fetch_assoc($result)['count'];
}

function createHistoryEntry($text, $table=false, $id=false) {
	$data = '';
	if ($id !== false) {
		$data = mysql_real_escape_string(serialize(mysql_fetch_assoc(mysql_query("SELECT * FROM `". $table ."` WHERE `id`=". $id))));
	} else if ($table !== false) {
		$data = $table;
	}
	mysql_query('INSERT INTO `history` (`text`, `data`) VALUES ("'.mysql_real_escape_string($text).'", "'.$data.'")');
}

function showHistory() {
	global $page;
	if ($page != 'history') return;
	$from = cleanGet('from');
	if (empty($from)) $from = 0;
	$entriesPerPage = 20;
	$i = 0;
	$countData = mysql_fetch_assoc(mysql_query("SELECT COUNT(id) as total FROM history;"));
	$total = floatval($countData['total']);
	$pages = ceil($total / $entriesPerPage);
	if ($pages > 25) $pages = 25;
	$setArray = array();
	$results = mysql_query("SELECT * FROM history ORDER BY id DESC LIMIT $from,$entriesPerPage");
	if (!empty($results)){
		while ($data = mysql_fetch_assoc($results)) {
			$setArray[$i] = array(
				'historyId' => $data['id'],
				'text' => $data['text'],
				'data' => $data['data'],
				'time' => $data['time']
			);
			$i++;
		}
	}
	$pageData = array();
	$perPageCounter = 0;
	for ($i=1; $i <= $pages; $i++) { 
		$pageData[$i]['current'] = $i;
		if ($i !==  1) $perPageCounter += $entriesPerPage;
		$pageData[$i]['from'] = $perPageCounter;
		$pageData[$i]['active'] = ($from == $perPageCounter) ? 'active' : '';  
	}
	$view = new Template('history.html', true);
	$view->setContent('HISTORY', $setArray);
	$view->setContent('COUNTER', $pageData);
	$prev = ($from > 0) ? ($from - $entriesPerPage) : 0;
	$view->setOne('prev', $prev);
	$next = (($from + $entriesPerPage) < $total) ? $from + $entriesPerPage : $pageData[$pages]['from'];
	$view->setOne('next', $next);
	$view->setOne('historyActive', 'active');
	die($view->vorlage);
}
?>

