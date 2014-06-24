#!/usr/bin/env python

from flask.ext.restful import Resource, abort, reqparse
from json import dumps
from flask import jsonify, make_response
from models import List as objList

parser = reqparse.RequestParser()
parser.add_argument('name', type=str, help="List Name")

class List(Resource):
	def get(self, list_id=None):
		if not list_id:
			return self._get_all()
		_list = objList.get(list_id)
		return jsonify(_list)

	def _get_all(self):
		_lists = objList.get_all()

		if not _lists:
			return make_response(dumps([]))

		return make_response(dumps(_lists))

	def post(self):		
		args = parser.parse_args()
		if not args.name:
			abort(500)

		_l = objList.add(args.name)
		return _l

	def put(self, list_id=None):
		if not list_id:
			abort(500)
		args = parser.parse_args()
		if not args.name:
			abort(500)

		_l = objList.update(list_id, args.name)

		return _l

	def delete(self, list_id=None):
		if not list_id:
			abort(500)

		_l = objList.delete(list_id)
		return _l