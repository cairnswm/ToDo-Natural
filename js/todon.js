// Functions for the TODO app
// William Cairns
// Possible enhancements - search for ToDo

function clearToDos()
{
	$("#todos").html('');
}

function displayToDo(id, name, detail, status)
{
	task = '<p class="todo-element">';
	statusStr = "";
	if (status == 1) { statusStr = "checked"; }
	task += '<input '+statusStr+' onclick="toggleToDo('+id+')" id="todo'+id+'" type="checkbox"> <label for="todo'+id+'">'+name+'</label> <span>'+detail+'</span>';
    task += '</p>';
	$("#todos").append(task);
}

function displayToDos(ToDoList)
{
	$.each(ToDoList, function(index, item)
	{
		displayToDo(item.id, item.Name, item.Detail, item.Status);
	});
}

function toggleToDo(id)
{
	$.ajax({
        method: "POST",
        dataType: "json",
		async: true,
        url: "api/ajax_todo.php?action=toggle",
        data: { id: id}
    })
    .done(function (data) {
        if (data.Result == "OK") {
			// Successful - do not need to update display. 
        }
        if (data.status == "ERROR") {
            alert(data.message);
        }
    })
    .fail(function (data) {
        alert(data.responseText);
    });
}
function saveToDo(name, detail, status)
{
	// write to DB - ajax
	$.ajax({
        method: "POST",
        dataType: "json",
		async: true,
        url: "api/ajax_todo.php?action=create",
        data: { Name: name, Detail: detail, Status: status }
    })
    .done(function (data) {
        if (data.Result == "OK") {
			item = data.Record;
			displayToDo(item.id, item.Name, item.Detail, item.Status);
        }
        if (data.status == "ERROR") {
            alert(data.message);
        }
    })
    .fail(function (data) {
        alert(data.responseText);
    });
}

function submitToDoForm()
{
	name = $("#toDoName").val();
	detail = $("#toDoDetail").val();
	saveToDo(name, detail, 0);
}

function loadToDo(id)
{
	// get ToDo based on id

}

function loadToDos()
{
	// Load all ToDos from DB
	$.ajax({
        method: "POST",
        dataType: "json",
		async: true,
        url: "api/ajax_todo.php?action=list",
        data: {}
    })
    .done(function (data) {
        if (data.Result == "OK") {
			displayToDos(data.Records);
        }
        if (data.status == "ERROR") {
            alert(data.message);
        }
    })
    .fail(function (data) {
        alert(data.responseText);
    });
}

$(function () {
	clearToDos();
	/*ToDoList = [];
	ToDo = [];
	ToDo["id"] = 100;
	ToDo["Name"] = "ToDo 100";
	ToDo["Detail"] = "these are the details";
	ToDo["Status"] = 1;
	ToDoList.push(ToDo);
    displayToDos(ToDoList);       */
	loadToDos();
});