import React, { Component } from "react";

class NavBar extends Component {
  state = {};
  render() {
    return (
      <nav className="navbar navbar-light bg-light">
        <p className="navbar-brand" href="#">
          Todos completed:
          <span className="badge badge-pill badge-secondary">
            {this.props.totalCounter}
          </span>
        </p>
      </nav>
    );
  }
}

export default NavBar;
