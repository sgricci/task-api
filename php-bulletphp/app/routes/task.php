<?php

$app->path('task', function($request) use($app) {
	$app->get(function($request) use($app) {
		$list_id = $request->get('list_id', null);
		if (!empty($list_id)) {
			$tasks = Task::where('list_id', '=', $list_id)->get();
			return $tasks;
		}
		$tasks = Task::all();
		return $tasks;
	});

	$app->post(function($request) use ($app) {
		$task = new Task;
		$task->name = $request->post('name');
		$task->list_id = $request->post('list_id');
		$task->save();
		return $task->toJSON();
	});

	$app->param('int', function($request, $task_id) use($app) {
		// Get single list
		$app->get(function($request) use ($app, $task_id) {
			$task = Task::find($task_id);
			if (empty($task)) return '{}';
			return $task;
		});

		$app->put(function($request) use ($app, $task_id) {
			$task = Task::find($task_id);
			$task->list_id = $request->list_id;
			$task->name = $request->name;
			$task->save();
			return $task->toJSON();
		});

		$app->delete(function($request) use ($app, $task_id) {
			$task = Task::find($task_id);
			$task->delete();
			if (empty($task)) return '{}';

			return $task;
		});
	});
});