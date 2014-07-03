require "sinatra"
require "sinatra/activerecord"
require "json"

set :port, 5000
set :database, "mysql://root:root@localhost/todoapp"
ActiveRecord::Base.record_timestamps = false
ActiveRecord::Base.include_root_in_json = false
class Task < ActiveRecord::Base
	set_table_name 'task'
end
class List < ActiveRecord::Base
	set_table_name 'list'
end


get '/task' do
	content_type :json
	if params[:list_id]
		task = Task.where(list_id: params[:list_id])
		p task.to_json
	else
		p Task.all.to_json
	end
end
get '/task/:id' do
	content_type :json
	begin
		task = Task.find(params[:id])
		p task.to_json
	rescue
		body '{}'
	end
end
post '/task' do
	content_type :json
	t = Task.create(params)
	return t.to_json
end
put '/task/:id' do
	content_type :json
	begin
		t = Task.find(params[:id])
		t.list_id = params[:list_id]
		t.name = params[:name]
		t.save
		p t.to_json
	rescue
		body '{}'
	end
end
delete '/task/:id' do
	content_type :json
	begin
		t = Task.find(params[:id])
		n_t = t
		t.destroy
		p n_t.to_json
	rescue
		body '{}'
	end
end

get '/list' do
	content_type :json

	p List.all.to_json
end
get '/list/:id' do
	content_type :json
	begin
		l = List.find(params[:id])
		p l.to_json
	rescue
		p '{}'
	end
end
post '/list' do
	content_type :json
	l = List.create(params)
	p l.to_json
end
put '/list/:id' do
	content_type :json
	begin
		l = List.find(params[:id])
		l.name = params[:name]
		l.save
		p l.to_json
	rescue
		p '{}'
	end

end
delete '/list/:id' do
	begin
		l = List.find(params[:id])
		n_l = l
		l.destroy
		p n_l.to_json
	rescue
		p '{}'
	end
end


