#!/usr/bin/env python
from app import db

class List(db.Model):
	id = db.Column(db.Integer, primary_key=True)
	name = db.Column(db.String(200), unique=False)

	def __init__(self, name):
		self.name = name

	def json_dump(self):
		return dict(
			id=self.id,
			name=self.name
			)

	@classmethod
	def get(cls, list_id):
		_list = cls._get(list_id)
		if not _list:
			return dict()
		return _list.json_dump()

	@classmethod
	def _get(cls, list_id):
		_list = db.session.query(List) \
			.filter(List.id == list_id) \
			.first()
		return _list

	@classmethod
	def get_all(cls):
		_lists = db.session.query(List) \
			.all()
		return [l.json_dump() for l in _lists]
		

	@classmethod
	def add(cls, name):
		l = List(name)
		db.session.add(l)
		db.session.commit()
		return l.json_dump()
		

	@classmethod
	def update(cls, id, name):
		l = db.session.query(List) \
			.filter(List.id == id) \
			.first()
		l.name = name
		db.session.commit()
		return l.json_dump()

	@classmethod
	def delete(cls, id):
		_list = cls._get(id)
		_j = _list.json_dump()
		db.session.delete(_list)
		db.session.commit()
		return _j

	def __repr__(self):
		return '<List %r>' % self.name

class Task(db.Model):
	id = db.Column(db.Integer, primary_key=True)
	list_id = db.Column(db.Integer, nullable=False)
	name = db.Column(db.String(200), unique=False)

	def __init__(self, list_id, name):
		self.list_id = list_id
		self.name = name

	def json_dump(self):
		return dict(
			id=self.id,
			list_id=self.list_id,
			name=self.name
			)

	@classmethod
	def get(cls, task_id):
		_task = cls._get(task_id)
		return _task.json_dump()

	@classmethod
	def _get(cls, task_id):
		_task = db.session.query(Task) \
			.filter(Task.id == task_id) \
			.first()
		return _task

	@classmethod
	def get_all(cls):
		_tasks = db.session.query(Task) \
			.all()
		return [t.json_dump() for t in _tasks]

	@classmethod
	def get_by_list_id(cls, list_id):
		_tasks = db.session.query(Task) \
			.filter(Task.list_id == list_id) \
			.all()
		return [t.json_dump() for t in _tasks]		

	@classmethod
	def add(cls, list_id, name):
		t = Task(list_id, name)
		db.session.add(t)
		db.session.commit()
		return t.json_dump()
		

	@classmethod
	def update(cls, id, list_id, name):
		t = db.session.query(Task) \
			.filter(Task.id == id) \
			.first()
		t.name = name
		t.list_id = list_id
		db.session.commit()
		return t.json_dump()

	@classmethod
	def delete(cls, id):
		_task = cls._get(id)
		_j = _task.json_dump()
		db.session.delete(_task)
		db.session.commit()
		return _j

	def __repr__(self):
		return '<Task %r>' % self.name
