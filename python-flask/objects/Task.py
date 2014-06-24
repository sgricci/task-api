#!/usr/bin/env python

from flask.ext.restful import Resource, abort, reqparse
from flask import jsonify, make_response
from json import dumps
from models import Task as objTask

parser = reqparse.RequestParser()
parser.add_argument('name', type=str, help="Task Name")
parser.add_argument('list_id', type=int, help="List ID")

class Task(Resource):
	def get(self, task_id=None):
		parser = reqparse.RequestParser()
		parser.add_argument('list_id', type=int)
		args = parser.parse_args()
		if args.list_id:
			return self._get_by_list_id(args.list_id)

		if not task_id:
			return self._get_all()
		_task = objTask.get(task_id)
		return jsonify(_task)

	def _get_all(self):
		_tasks = objTask.get_all()

		if not _tasks:
			return make_response(dumps([]))

		return make_response(dumps(_tasks))

	def _get_by_list_id(self, list_id):
		_tasks = objTask.get_by_list_id(list_id)

		if not _tasks:
			return make_response(dumps([]))

		return make_response(dumps(_tasks))

	def post(self):
		args = parser.parse_args()
		if not args.name or not args.list_id:
			abort(500)

		_l = objTask.add(args.list_id, args.name)
		return _l

	def put(self, task_id=None):
		if not task_id:
			abort(500)
		args = parser.parse_args()
		if not args.name or not args.list_id:
			abort(500)

		_l = objTask.update(task_id, args.list_id, args.name)

		return _l

	def delete(self, task_id=None):
		if not task_id:
			abort(500)

		_l = objTask.delete(task_id)
		return _l