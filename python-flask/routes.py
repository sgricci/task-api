#!/usr/bin/env python
from app import api, app
from objects.Task import Task
from objects.List import List

api.add_resource(Task, 
	'/task',
	'/task/<int:task_id>'
	)
api.add_resource(List, 
	'/list',
	'/list/<int:list_id>'
	)

