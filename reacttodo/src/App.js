import React, { Component } from "react";
import NavBar from "./components/navbar";
import Todos from "./components/todos";
import Header from "./components/header";
import AddButton from "./components/addbutton";
import AddForm from "./components/addform";

import "./App.css";
import "./css/style.css";

class App extends Component {
  state = {
    todos: []
  };

  componentDidMount() {
    console.log("123");
    fetch("http://localhost/todon/api/ajax_todo.php?action=list")
      .then(res => res.json())
      .then(data => this.setChecked(data.Records));
  }

  setChecked(data) {
    const data2 = data.map(todo => {
      todo.Status = parseInt(todo.Status);
      todo.isChecked = false;
      if (todo.Status === 1) {
        todo.isChecked = true;
      }
      return todo;
    });
    this.setState({ todos: data2 });
  }

  handleToggle = todo => {
    const todos = [...this.state.todos];
    const index = todos.indexOf(todo);
    todo[index] = { ...todo };
    todos[index].Status = 1 - todos[index].Status;
    todos[index].isChecked = false;
    if (todos[index].Status === 1) {
      todos[index].isChecked = true;
    }
    this.setState({ todos: todos });
  };

  handleSaveToDo = todo => {};

  render() {
    return (
      <React.Fragment>
        <Header />
        <NavBar
          totalCounter={this.state.todos.filter(c => c.Status === 1).length}
        />
        <AddForm />
        <main className="container">
          <Todos todos={this.state.todos} onToggle={this.handleToggle} />
        </main>
        <AddButton onSaveForm={this.handleSaveToDo} />
      </React.Fragment>
    );
  }
}

export default App;
