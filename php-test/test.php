<?php

require_once 'vendor/autoload.php';
use Guzzle\Http\Client;

class ApiTest extends PHPUnit_Framework_TestCase {

	public $client;

	public function setUp() {
		$this->client =  new Client('http://localhost:5000');
	}

	public function testLists() {
		# No lists at the start
		$data = $this->_get_lists();
		$this->assertEmpty($data);

		# Add a list
		$list = $this->_add_list("New List");
		$this->assertNotEmpty($list);
		$this->assertTrue(is_array($list));

		# Get a list
		$new_list = $this->_get_list($list['id']);
		$this->assertNotEmpty($new_list);
		$this->assertTrue(is_array($new_list));

		# Get all lists
		$data = $this->_get_lists();
		$this->assertNotEmpty($data);
		$this->assertEquals(count($data), 1);

		# Update a list
		$list = $this->_update_list($list['id'], "Updated List");
		$this->assertNotEmpty($list);
		$this->assertTrue(is_array($list));
		
		# Get a list
		$new_list = $this->_get_list($list['id']);
		$this->assertNotEmpty($new_list);
		$this->assertTrue(is_array($new_list));

		# Delete a list
		$list = $this->_delete_list($list['id']);
		$this->assertNotEmpty($list);
		$this->assertTrue(is_array($list));

		# No lists at the end
		$data = $this->_get_lists();
		$this->assertEmpty($data);
	}

	private function _get_lists() {
		$request = $this->client->get('/list');
		$response = $request->send();

		$data = $response->json();
		return $data;
	}

	private function _get_list($list_id) {
		$request = $this->client->get('/list/'.$list_id);
		$response = $request->send();

		$data = $response->json();
		return $data;
	}

	private function _add_list($list_name) {
		$request = $this->client->post('/list', array(), array(
			'name' => $list_name
		));
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	private function _update_list($list_id, $list_name) {
		$request = $this->client->put('/list/'.$list_id, array(), array(
			'name' => $list_name
		));
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	private function _delete_list($list_id) {
		$request = $this->client->delete('/list/'.$list_id);
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	public function testTasks() {
		# No tasks at the start
		$data = $this->_get_tasks();
		$this->assertEmpty($data);

		# Add a list
		$list = $this->_add_list("New List");
		$this->assertNotEmpty($list);
		$this->assertTrue(is_array($list));

		# Add a task
		$task = $this->_add_task($list['id'], "New Task");
		$this->assertNotEmpty($task);
		$this->assertTrue(is_array($task));

		# Get a task
		$new_task = $this->_get_task($task['id']);
		$this->assertNotEmpty($new_task);
		$this->assertTrue(is_array($new_task));

		# Get all tasks
		$data = $this->_get_tasks();
		$this->assertNotEmpty($data);
		$this->assertEquals(count($data), 1);
		
		# Get all tasks for a list
		$data = $this->_get_tasks_by_list($list['id']);
		$this->assertNotEmpty($data);
		$this->assertEquals(count($data), 1);

		# Update a task
		$task = $this->_update_task($task['id'], $list['id'], "Updated Task");
		$this->assertNotEmpty($list);
		$this->assertTrue(is_array($list));
		
		# Get a task
		$new_task = $this->_get_task($task['id']);
		$this->assertNotEmpty($new_task);
		$this->assertTrue(is_array($new_task));

		# Delete a task
		$task = $this->_delete_task($task['id']);
		$this->assertNotEmpty($task);
		$this->assertTrue(is_array($task));
		
		# Delete a list
		$list = $this->_delete_list($list['id']);
		$this->assertNotEmpty($list);
		$this->assertTrue(is_array($list));

		# No tasks at the end
		$data = $this->_get_tasks();
		$this->assertEmpty($data);
	}

	private function _add_task($list_id, $task_name) {
		$request = $this->client->post('/task', array(), array(
			'list_id' => $list_id,
			'name'    => $task_name,
		));
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	private function _get_tasks() {
		$request = $this->client->get('/task');
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	private function _get_task($task_id) {
		$request = $this->client->get('/task/'.$task_id);
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	private function _get_tasks_by_list($list_id) {
		$request = $this->client->get('/task?list_id='.$list_id);
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	private function _update_task($task_id, $list_id, $task_name) {
		$request = $this->client->put('/task/'.$task_id, array(), array(
			'list_id' => $list_id,
			'name'    => $task_name
		));
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	private function _delete_task($task_id) {
		$request = $this->client->delete('/task/'.$task_id);
		$response = $request->send();
		$data = $response->json();
		return $data;
	}

	public function tearDown() {
		unset($this->client);
	}
}
