package main

import (
	"database/sql"
	"fmt"
	"github.com/coopernurse/gorp"
	"github.com/gin-gonic/gin"
	_ "github.com/go-sql-driver/mysql"
	"io/ioutil"
	"log"
	"net/url"
	"os"
	//"reflect"
	"strconv"
)

var dbMap *gorp.DbMap
var paramMap url.Values

type List struct {
	Id   int    `db:"id" json:"id"`
	Name string `db:"name" json:"name"`
}

type Task struct {
	Id     int    `db:"id" json:"id"`
	ListId int    `db:"list_id" json:"list_id"`
	Name   string `db:"name" json:"name"`
}

type Empty struct {
}

func GetList(listid int) List {
	var list List
	err := dbMap.SelectOne(&list, "SELECT * FROM list where id = ?", listid)
	checkErr(err, "Getting list")
	return list
}

func GetLists() []List {
	var lists []List
	_, err := dbMap.Select(&lists, "SELECT * FROM list")
	checkErr(err, "Get all lists")

	return lists
}

func AddList(name string) *List {
	list := &List{0, name}

	err := dbMap.Insert(list)
	checkErr(err, "Inserting list")
	return list
}

func UpdateList(listid int, name string) (List, int) {
	//var list List
	obj, err := dbMap.Get(List{}, listid)
	checkErr(err, "Getting list to update")
	if obj == nil {
		return List{}, 0
	}
	list := obj.(*List)

	if list.Id == 0 {
		return List{}, 0
	}
	fmt.Println(list)
	list.Name = name
	count, err := dbMap.Update(list)
	checkErr(err, "Updating list")
	if count == 0 {
		return List{}, 0
	}
	return List{list.Id, list.Name}, 1
}

func DeleteList(listid int) (List, int) {

	var retList List
	obj, err := dbMap.Get(List{}, listid)
	checkErr(err, "Getting list to update")
	if obj == nil {
		return List{}, 0
	}
	fmt.Println(obj)
	list := obj.(*List)
	if list.Id == 0 {
		return List{}, 0
	}
	retList = List{list.Id, list.Name}
	count, err2 := dbMap.Delete(list)
	if count == 0 {
		return List{}, 0
	}
	checkErr(err2, "delete list")
	return retList, 1
}

func GetTask(taskid int) Task {
	obj, err := dbMap.Get(Task{}, taskid)
	checkErr(err, "Get task")
	if obj == nil {
		return Task{}
	}
	task := obj.(*Task)
	return Task{task.Id, task.ListId, task.Name}
}

func GetTasks() []Task {
	var tasks []Task
	_, err := dbMap.Select(&tasks, "SELECT * FROM task")
	checkErr(err, "Getting lists")
	return tasks
}

func DeleteTask(taskid int) (Task, int) {
	var retTask Task
	obj, err := dbMap.Get(Task{}, taskid)
	checkErr(err, "Get Task for delete")
	if obj == nil {
		return Task{}, 0
	}
	task := obj.(*Task)
	if task.Id == 0 {
		return Task{}, 0
	}
	retTask = Task{task.Id, task.ListId, task.Name}
	count, err := dbMap.Delete(task)
	checkErr(err, "delete task")
	if count == 0 {
		return Task{}, 0
	}
	return retTask, 1
}

func UpdateTask(taskid int, listid int, name string) (Task, int) {

	obj, err := dbMap.Get(Task{}, taskid)
	checkErr(err, "Getting task to update")
	if obj == nil {
		return Task{}, 0
	}
	task := obj.(*Task)
	if task.Id == 0 {
		return Task{}, 0
	}
	task.ListId = listid
	task.Name = name
	count, err := dbMap.Update(task)
	checkErr(err, "Updating task")
	if count == 0 {
		return Task{}, 0
	}

	return Task{task.Id, task.ListId, task.Name}, 1
}

func AddTask(listid int, name string) *Task {
	task := &Task{0, listid, name}
	err := dbMap.Insert(task)
	checkErr(err, "Inserting task")
	return task
}

func initDb() *gorp.DbMap {
	db, err := sql.Open("mysql", "root:root@/todoapp")
	checkErr(err, "Connecting to database")

	dbmap := &gorp.DbMap{Db: db, Dialect: gorp.MySQLDialect{"InnoDB", "UTF8"}}
	dbmap.TraceOn("[gorp]", log.New(os.Stdout, "myapp:", log.Lmicroseconds))

	dbmap.AddTableWithName(List{}, "list").SetKeys(true, "Id")
	dbmap.AddTableWithName(Task{}, "task").SetKeys(true, "Id")

	return dbmap

}

func checkErr(err error, msg string) {
	if err != nil {
		log.Fatalln(msg, err)
	}
}

func main() {
	dbMap = initDb()
	defer dbMap.Db.Close()

	r := gin.New()
	r.Use(gin.Logger())
	r.Use(gin.Recovery())

	r.GET("/list", func(c *gin.Context) {
		lists := GetLists()
		fmt.Println(len(lists))
		if len(lists) == 0 {
			c.JSON(200, []string{})
		} else {
			c.JSON(200, lists)
		}
	})

	r.GET("/task", func(c *gin.Context) {
		tasks := GetTasks()
		if len(tasks) == 0 {
			c.JSON(200, []string{})
		} else {
			c.JSON(200, tasks)
		}
	})

	r.GET("/list/:list_id", func(c *gin.Context) {
		slist_id := c.Params.ByName("list_id")
		list_id, err := strconv.Atoi(slist_id)
		checkErr(err, "converting list_id to int")
		fmt.Println(list_id)
		list := GetList(list_id)
		if list.Id == 0 {
			c.JSON(200, Empty{})
		} else {
			c.JSON(200, list)
		}
	})

	r.GET("/task/:task_id", func(c *gin.Context) {
		stask_id := c.Params.ByName("task_id")
		task_id, err := strconv.Atoi(stask_id)
		checkErr(err, "converting task_id to int")
		task := GetTask(task_id)
		if task.Id == 0 {
			c.JSON(200, Empty{})
		} else {
			c.JSON(200, task)
		}
	})

	r.POST("/list", func(c *gin.Context) {
		name := getParam(c, "name", true)
		list := AddList(name)
		c.JSON(200, list)
	})
	r.POST("/task", func(c *gin.Context) {

		name := getParam(c, "name", true)
		slist_id := getParam(c, "list_id", false)
		fmt.Println(name)
		fmt.Println(slist_id)
		list_id, err := strconv.Atoi(slist_id)
		checkErr(err, "converting list_id to int")
		task := AddTask(list_id, name)
		c.JSON(200, task)
	})

	r.PUT("/list/:list_id", func(c *gin.Context) {
		slist_id := c.Params.ByName("list_id")
		list_id, err := strconv.Atoi(slist_id)
		checkErr(err, "converting list_id to int")

		name := getParam(c, "name", true)
		list, res := UpdateList(list_id, name)
		if res == 0 {
			c.JSON(200, Empty{})
		} else {
			c.JSON(200, list)
		}
	})

	r.PUT("/task/:task_id", func(c *gin.Context) {
		stask_id := c.Params.ByName("task_id")
		task_id, err := strconv.Atoi(stask_id)
		checkErr(err, "converting task_id to int")
		slist_id := getParam(c, "list_id", true)
		list_id, err := strconv.Atoi(slist_id)
		checkErr(err, "converting list_id to int")

		name := getParam(c, "name", false)

		task, res := UpdateTask(task_id, list_id, name)
		if res == 0 {
			c.JSON(200, Empty{})
		} else {
			c.JSON(200, task)
		}
	})

	r.DELETE("/list/:list_id", func(c *gin.Context) {
		slist_id := c.Params.ByName("list_id")
		list_id, err := strconv.Atoi(slist_id)
		checkErr(err, "converting list_id to int")

		list, res := DeleteList(list_id)

		if res == 0 {
			c.JSON(200, Empty{})
		} else {
			c.JSON(200, list)
		}
	})

	r.DELETE("/task/:task_id", func(c *gin.Context) {
		stask_id := c.Params.ByName("task_id")
		task_id, err := strconv.Atoi(stask_id)
		checkErr(err, "converting task_id to int")

		task, res := DeleteTask(task_id)
		if res == 0 {
			c.JSON(200, Empty{})
		} else {
			c.JSON(200, task)
		}

	})

	r.Run(":5000")
}

func getParam(c *gin.Context, name string, reset bool) string {
	if paramMap == nil || reset == true {
		body, err := ioutil.ReadAll(c.Req.Body)
		checkErr(err, "reading body")

		s := string(body[:])

		m, _ := url.ParseQuery(s)

		paramMap = m
	}

	return paramMap[name][0]
}
