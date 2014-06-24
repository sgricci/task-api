# Sprinkle specification (v0.1)

Sprinkle is a test application to be made in multiple languages with frameworks for exploratory purposes

## /lists

Returns different instances of a task list, including list meta data

### GET

Returns all lists

### GET id

Returns a specific list

### POST

Creates a new list and returns the newly created list

### PUT

Updates an existing list and returns the newly update list

### DELETE

Removes a list and returns the matched object

---

## /tasks

Represents tasks attached to a list, with it's meta data

### GET

Get all tasks associated with an account

* Optional Parameters
- list_id
	Specify a list ID to filter the tasks by

### GET id

Returns a specific task object

### POST

Creates a new task and returns the task object

### PUT

Updates an existing task (requires ID) and returns the updated task object

### DELETE

Removes a task, returns the matched task object


