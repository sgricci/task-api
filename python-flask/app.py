#!/usr/bin/env python
from flask import Flask
from flask.ext import restful
from flask.ext.restful import Resource, Api
from flask.ext.sqlalchemy import SQLAlchemy


app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://root:root@localhost/todoapp'
api = restful.Api(app)
db = SQLAlchemy(app)

import routes
import models

if __name__ == "__main__":
	app.run(debug=True)
