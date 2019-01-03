import React, { Component } from "react";
import Todo from "./todo";

class Todos extends Component {
  render() {
    const { onToggle, todos } = this.props;
    return (
      <div className="todo animated fadeinup delay-1">
        {todos.map(todo => (
          <Todo key={todo.id} onToggle={onToggle} todo={todo} />
        ))}
      </div>
    );
  }
}

export default Todos;
