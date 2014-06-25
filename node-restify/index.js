var Sequelize = require('sequelize')
	, sequelize = new Sequelize('todoapp', 'root', 'root', {
		dialect: 'mysql',
		port: 3306
	});

sequelize
	.authenticate()
		.complete(function(err) {
			if (!!err) {
				console.log("Unable to connect to the database:", err);
			} else {
				console.log("Connection has been established successfully");
			}
		});

var List = sequelize.define('List', {
	name: Sequelize.STRING
}, {
	tableName: 'list',
	timestamps: false
});

var Task = sequelize.define('Task', {
	list_id: Sequelize.INTEGER,
	name: Sequelize.STRING
}, {
	tableName: 'task',
	timestamps: false
});

Task.hasOne(List);
List.hasMany(Task);

var restify = require('restify');

var server = restify.createServer({
	name: 'myapp'
})

server.use(restify.bodyParser()); 

function noop(req, res, next) {
	console.log('NOOP');

	res.send(200, {message:'noop'});
	return next();
}


function getAllLists(req, res, next) {
	List.findAll().complete(function(err, records) {
		res.send(200, records);
		return next();
	});
}

function getList(req, res, next) {
	List.find({where: { id : req.params.list_id }})
		.complete(function(err, record) {
			res.send(200, record);
			return next();
		});
}

function addList(req, res, next) {
	List.create({name: req.params.name}).complete(function(err, list) {
		res.send(200, list);
		return next();
	});
}

function updateList(req, res, next) {
	List.find(req.params.list_id).success(function(list) {
		if (list) {
			list.updateAttributes({
				name: req.params.name
			});

			res.send(200, list);
			return next();
		}
	});
}

function deleteList(req, res, next) {
	List.find(req.params.list_id).success(function(list) {
		list.destroy().success(function() {
			res.send(200, list);
			return next();
		})
	});
}

function getAllTasks(req, res, next) {
	if (req.params.list_id) {
		Task.find({list_id: list_id}).success(function(tasks) {
			res.send(200, tasks);
			return next();
		})
	}
	Task.findAll().success(function(tasks) {
		res.send(200, tasks);
		return next();
	});
}

function getTask(req, res, next) {
	Task.find(req.params.task_id).success(function(task) {
		res.send(200, task);
		return next();
	});
}

function addTask(req, res, next) {
	Task.create({
		list_id: req.params.list_id, 
		name: req.params.name
	}).success(function(task) {
		res.send(200, task);
		return next();
	});
}

function updateTask(req, res, next) {
	Task.find(req.params.task_id).success(function(task) {
		if (task) {
			task.updateAttributes({
				list_id: req.params.list_id,
				name: req.params.name
			});
			res.send(200, task);
			return next();
		}
	});
}

function deleteTask(req, res, next) {
	Task.find(req.params.task_id).success(function(task) {
		task.destroy().success(function() {
			res.send(200, task);
			return next();
		});
	});
}

server.get('/task', getAllTasks);
server.get('/task/:task_id', getTask);
server.post('/task', addTask);
server.put('/task/:task_id', updateTask);
server.del('/task/:task_id', deleteTask);

server.get('/list', getAllLists);
server.get('/list/:list_id', getList);
server.post('/list', addList);
server.put('/list/:list_id', updateList);
server.del('/list/:list_id', deleteList);


server.listen('5000', '127.0.0.1', function() {
	console.log('%s listening at %s ', server.name, server.url);
})