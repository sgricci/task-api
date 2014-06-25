<?php

$app->path('list', function($request) use($app) {
	$app->get(function($request) use($app) {
		$lists = objList::all();
		return $lists;
	});

	$app->post(function($request) use ($app) {
		$list = new objList;
		$list->name = $request->post('name');
		$list->save();
		return $list->toJSON();
	});

	$app->param('int', function($request, $list_id) use($app) {
		// Get single list
		$app->get(function($request) use ($app, $list_id) {
			$list = objList::find($list_id);
			if (empty($list)) return '{}';
			return $list;
		});

		$app->put(function($request) use ($app, $list_id) {
			$list = objList::find($list_id);
			$list->name = $request->name;
			$list->save();
			return $list->toJSON();
		});

		$app->delete(function($request) use ($app, $list_id) {
			$list = objList::find($list_id);
			$list->delete();
			if (empty($list)) return '{}';

			return $list;
		});
	});
});