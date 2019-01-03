import React, { Component } from "react";

class Todo extends Component {
  renderTags() {
    const { onToggle, todo } = this.props;

    return (
      <p className="todo-element">
        <input
          type="checkbox"
          checked={todo.isChecked}
          onChange={() => onToggle(this.props.todo)}
          id={todo.id}
        />
        <label htmlFor={todo.id}>{todo.Name}</label>
        <span>{todo.Detail}</span>
      </p>
    );
  }

  render() {
    return <React.Fragment>{this.renderTags()}</React.Fragment>;
  }
}

export default Todo;
